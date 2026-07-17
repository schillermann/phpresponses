# PhpResponse

A simple web framework in PHP that respects OOP.

Inspired by pure OOP, Alan Kay with [Smalltalk](https://en.wikipedia.org/wiki/Smalltalk), and Yegor Bugayenko's [Cactoos](https://github.com/yegor256/cactoos), [Takes](https://github.com/yegor256/takes), and [JPages](https://github.com/yegor256/jpages).

I’ve also created the web framework in other languages that you can check out.
- [Java](https://github.com/schillermann/jresponses)
- [JavaScript](https://github.com/schillermann/jsresponses)

## Core Principles
- **Strictly OOP**: No getters, no setters, no nulls.
- **Composition over Inheritance**: Functionality is built using decorators.
- **Immutability**: Responses are defined through nested objects.

## Quick Start

Create an `index.php` and use decorators to compose your response:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpResponse\Response\Body;
use PhpResponse\Response\Header;
use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\Media\Wire;
use PhpResponse\LiteralText;

(new Ok(
    new Header(
        new Body(new LiteralText("<h1>Hello from PhpResponse!</h1>")),
        "Content-Type", "text/html"
    )
))->media(new Wire());
```

## Request Example

You can compose request parsing declaratively by encapsulating the request variables in a custom class representing the request details:

```php
<?php

use PhpResponse\Text;
use PhpResponse\TemplateVariable;
use PhpResponse\LiteralText;
use PhpResponse\FallbackText;
use PhpResponse\Request\Header;
use PhpResponse\Request\Method;
use PhpResponse\Request\Path;
use PhpResponse\Request\Body;

final class RequestDetailsText implements Text {
    public function string(): string {
        return (new TemplateVariable(
            new TemplateVariable(
                new TemplateVariable(
                    new TemplateVariable(
                        new LiteralText("<html><body><h1>Your Browser: ${agent}</h1><p>Method: ${method}</p><p>Path: ${path}</p><p>Body: ${body}</p></body></html>"),
                        "agent",
                        new FallbackText(
                            new Header("User-Agent"),
                            new LiteralText("Unknown")
                        )
                    ),
                    "method",
                    new Method()
                ),
                "path",
                new Path()
            ),
            "body",
            new Body()
        ))->string();
    }
}
```

Now, compose the response cleanly:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\Header;
use PhpResponse\Response\Body;
use PhpResponse\Response\Media\Wire;

(new Ok(
    new Header(
        new Body(
            new RequestDetailsText()
        ),
        "Content-Type", "text/html"
    )
))->media(new Wire());
```

Now just open this file in your browser via a web server (like `php -S localhost:8000`).

## JSON Request Example

You can parse JSON requests declaratively using template variables.

### Inline Template Example

If you want to render the response using an in-memory template string, define your view class:

```php
<?php

use PhpResponse\Text;
use PhpResponse\Number;
use PhpResponse\TemplateVariable;
use PhpResponse\LiteralText;
use PhpResponse\TextOfNumber;

final class UserWelcomeText implements Text {
    private Text $name;
    private Number $age;

    public function __construct(Text $name, Number $age) {
        $this->name = $name;
        $this->age = $age;
    }

    public function string(): string {
        return (new TemplateVariable(
            new TemplateVariable(
                new LiteralText("<html><body><h1>Hello, ${name}!</h1><p>You are ${age} years old.</p></body></html>"),
                "name",
                $this->name
            ),
            "age",
            new TextOfNumber($this->age)
        ))->string();
    }
}
```

Now, pass this view to `Body`:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpResponse\Request\Body;
use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\Header;
use PhpResponse\Response\Body as ResponseBody;
use PhpResponse\Response\Media\Wire;
use PhpResponse\JsonSubTree;
use PhpResponse\JsonString;
use PhpResponse\JsonInt;

(new Ok(
    new Header(
        new ResponseBody(
            new UserWelcomeText(
                new JsonString(
                    new JsonSubTree(new Body(), 'user'),
                    'name'
                ),
                new JsonInt(
                    new JsonSubTree(new Body(), 'user'),
                    'age'
                )
            )
        ),
        "Content-Type", "text/html"
    )
))->media(new Wire());
```

### Encapsulated Template Example

If you have multiple template variables, nesting them can become deeply indented. The pure OOP approach is to encapsulate the composition inside a custom class representing the view:

```php
<?php

use PhpResponse\Text;
use PhpResponse\Number;
use PhpResponse\TemplateVariable;
use PhpResponse\TextOfNumber;

final class UserProfileText implements Text {
    private Text $template;
    private Text $name;
    private Number $age;

    public function __construct(Text $template, Text $name, Number $age) {
        $this->template = $template;
        $this->name = $name;
        $this->age = $age;
    }

    public function string(): string {
        return (new TemplateVariable(
            new TemplateVariable(
                $this->template,
                "name",
                $this->name
            ),
            "age",
            new TextOfNumber($this->age)
        ))->string();
    }
}
```

Because `UserProfileText` implements the `Text` interface, it is fully compatible with the response decorator tree. You can pass it as a drop-in argument directly to `Body`, which is then wrapped by `Header` and `StatusLine\Ok`:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpResponse\Request\Body;
use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\Header;
use PhpResponse\Response\Body as ResponseBody;
use PhpResponse\Response\Media\Wire;
use PhpResponse\JsonSubTree;
use PhpResponse\JsonString;
use PhpResponse\JsonInt;
use PhpResponse\TextOfFile;

(new Ok(
    new Header(
        new ResponseBody(
            new UserProfileText(
                new TextOfFile("template.html"),
                new JsonString(
                    new JsonSubTree(new Body(), 'user'),
                    'name'
                ),
                new JsonInt(
                    new JsonSubTree(new Body(), 'user'),
                    'age'
                )
            )
        ),
        "Content-Type", "text/html"
    )
))->media(new Wire());
```

## Caching and Performance with StickyText

`Body` object should only do one thing: read the request body from the input stream.
If you want to add a caching feature, use `StickyText` as a decorator.

You should use `StickyText` whenever reading the original `Text` like `Body` has side effects or a high performance cost (e.g., repeatedly reading streams, querying files, or calling external APIs).

```php
use PhpResponse\Request\Body;
use PhpResponse\StickyText;

// The request body is read only once upon the first call to string()
$body = new StickyText(new Body());

// First call: reads from stream and caches the result
$content = $body->string();

// Second call: retrieves from memory instantly
$contentAgain = $body->string();
```

## Routing


We compose routes cleanly using a decorator chain (Chain of Responsibility). The routing criteria themselves are `Response` decorators that either delegate to a `target` response on match, or a `fallback` response on mismatch:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\Header;
use PhpResponse\Response\Body;
use PhpResponse\Response\Media\Wire;
use PhpResponse\LiteralText;
use PhpResponse\Route\Method;
use PhpResponse\Route\RegexPath;
use PhpResponse\Route\ExactPath;

// Define a 404 Not Found response
$notFound = new Header(
    new Body(new LiteralText("Page not found")),
    "Content-Type", "text/plain"
);

(new Method("GET",
    new ExactPath("/",
        new Ok(new Header(new Body(new LiteralText("Welcome to the Home page")), "Content-Type", "text/plain")),
        new RegexPath("#^/users/(?<id>\d+)$#",
            new Ok(new Header(new Body(new LiteralText("User details page")), "Content-Type", "text/plain")),
            $notFound
        )
    ),
    new Method("POST",
        new ExactPath("/submit",
            new Ok(new Header(new Body(new LiteralText("Form submitted successfully")), "Content-Type", "text/plain")),
            $notFound
        ),
        $notFound
    )
))->media(new Wire());
```

## Logging

We do not inject a `Log` dependency directly into our business classes (such as `UserRegistration`), as this violates the Single Responsibility Principle.
Instead, we keep our business logic pure and add logging using the **Decorator Pattern**.

### Registration Example

First, define the core domain contract:

```php
interface Registration
{
    public function register(string $username): void;
}
```

Implement the pure business logic:

```php
final class UserRegistration implements Registration
{
    public function register(string $username): void
    {
        // Core business logic only (no logging)
    }
}
```

Decorate the business logic with logging behavior:

```php
use PhpResponse\Log\Log;
use PhpResponse\Log\PlainEntry;
use PhpResponse\Log\Level\InfoLevel;

final class LoggedRegistration implements Registration
{
    private Registration $origin;
    private Log $log;

    public function __construct(Registration $origin, Log $log)
    {
        $this->origin = $origin;
        $this->log = $log;
    }

    public function register(string $username): void
    {
        $this->origin->register($username);
        $this->log->write(
            new PlainEntry(
                new InfoLevel(),
                new FormattedText(
                    new LiteralText("User registered: %s"),
                    new LiteralText($username)
                )
            )
        );
    }
}
```

At runtime, compose the object graph:

```php
use PhpResponse\Log\FileLog;
use PhpResponse\ProjectPath;

$registration = new LoggedRegistration(
    new UserRegistration(),
    new FileLog(new ProjectPath('app.log'))
);

$registration->register("john_doe");
```

#### Extended Registration Example With Routing

First, define the response representing the action of a successful user registration:

```php
use PhpResponse\Response\Response;
use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\Header;
use PhpResponse\Response\Body as ResponseBody;
use PhpResponse\Response\Media\Media;
use PhpResponse\Text;
use PhpResponse\LiteralText;
use PhpResponse\FormattedText;

final class RegistrationResponse implements Response
{
    private Registration $registration;
    private Text $username;

    public function __construct(Registration $registration, Text $username)
    {
        $this->registration = $registration;
        $this->username = $username;
    }

    public function media(Media $media): Media
    {
        // Execute the registration action (side-effect)
        $this->registration->register($this->username->string());

        // Construct the success response
        return (new Ok(
            new Header(
                new ResponseBody(
                    new FormattedText(
                        new LiteralText("User '%s' registered successfully!"),
                        $this->username
                    )
                ),
                "Content-Type", "text/plain"
            )
        ))->media($media);
    }
}
```

Now, wire the route to this response:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpResponse\Response\Header;
use PhpResponse\Response\Body as ResponseBody;
use PhpResponse\Response\Media\Wire;
use PhpResponse\LiteralText;
use PhpResponse\Route\Method;
use PhpResponse\Route\ExactPath;
use PhpResponse\Request\Body as RequestBody;
use PhpResponse\JsonString;
use PhpResponse\Log\FileLog;
use PhpResponse\ProjectPath;

// 1. Instantiate the logging-decorated registration service
$registration = new LoggedRegistration(
    new UserRegistration(),
    new FileLog(new ProjectPath('app.log'))
);

// 2. Define fallback response
$notFound = new Header(
    new ResponseBody(new LiteralText("Not Found")),
    "Content-Type", "text/plain"
);

// 3. Match a POST request to '/register', extract the username parameter from JSON body,
// and trigger the registration response
(new Method("POST",
    new ExactPath("/register",
        new RegistrationResponse(
            $registration,
            new JsonString(new RequestBody(), "username")
        ),
        $notFound
    ),
    $notFound
))->media(new Wire());
```

### Log With Timestamp

```php
use PhpResponse\Log\PlainEntry;
use PhpResponse\Log\TimestampedEntry;
use PhpResponse\Log\UtcEpoch;
use PhpResponse\Log\Level\InfoLevel;
use PhpResponse\LiteralText;

$timestamped = new TimestampedEntry(
    new PlainEntry(new InfoLevel(), new LiteralText('Action triggered')),
    new UtcEpoch()
);
```

### Log With JSON Format

Serialize log entry metadata (level and message) into a JSON string:

```php
use PhpResponse\Log\PlainEntry;
use PhpResponse\Log\JsonEntry;
use PhpResponse\Log\Level\ErrorLevel;
use PhpResponse\LiteralText;

$jsonEntry = new JsonEntry(
    new PlainEntry(new ErrorLevel(), new LiteralText('Database down'))
);
```


### Log Targets

* `StreamLog`: Writes entries to standard resources (e.g., standard output, files).
* `ConsoleLog`: Writes entries directly to standard output (`php://stdout`).
* `FileLog`: Appends entries directly to a specified file path.
* `BufferLog`: Appends entries to an in-memory `ArrayObject` without exposing getters.
* `TeeLog`: Replicates entries to multiple log targets simultaneously.
* `LevelLog`: Filters entries, only writing those matching allowed levels.
* `FailsafeLog`: Catches and logs internal exceptions silently, preventing logging errors from crashing the application.

```php
use PhpResponse\Log\ConsoleLog;
use PhpResponse\Log\FileLog;
use PhpResponse\Log\TeeLog;
use PhpResponse\Log\LevelLog;
use PhpResponse\Log\FailsafeLog;
use PhpResponse\Log\PlainEntry;
use PhpResponse\Log\Level\ErrorLevel;
use PhpResponse\Log\Level\WarningLevel;
use PhpResponse\ProjectPath;
use PhpResponse\LiteralText;

// Setup a failsafe, level-filtered log writing to both console and file
$log = new FailsafeLog(
    new LevelLog(
        new TeeLog(
            new ConsoleLog(),
            new FileLog(new ProjectPath('app.log'))
        ),
        new ErrorLevel(),
        new WarningLevel()
    )
);

// This entry will be written to stdout and app.log
$log->write(new PlainEntry(new ErrorLevel(), new LiteralText('Disk almost full')));
```
