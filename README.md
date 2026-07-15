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

In true Object-Oriented Programming, objects must be small and cohesive. An object representing the request body, like `Body`, should only do one thing: read the request body from the input stream. It must not concern itself with caching or memoization. Adding caching logic directly to it would make the object stateful, complex, and bloated—violating the Single Responsibility Principle.

Instead of introducing state into environment-interacting objects, we use decorators.

`StickyText` is a decorator that caches (memoizes) the text returned by another `Text` object. You should use `StickyText` whenever reading the original `Text` has side effects or a high performance cost (e.g., repeatedly reading streams, querying files, or calling external APIs).

### Example

Instead of reading the raw input stream multiple times, wrap `Body` with `StickyText`:

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





