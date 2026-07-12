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

use PhpResponse\ResponseBody;
use PhpResponse\ResponseHeader;
use PhpResponse\ResponseStatusLineOk;
use PhpResponse\MediaToWire;
use PhpResponse\LiteralText;

(new ResponseStatusLineOk(
    new ResponseHeader(
        new ResponseBody(new LiteralText("<h1>Hello from PhpResponse!</h1>")),
        "Content-Type", "text/html"
    )
))->media(new MediaToWire());
```

## Request Example

You can compose request parsing declaratively by encapsulating the request variables in a custom class representing the request details:

```php
<?php

use PhpResponse\Text;
use PhpResponse\TemplateVariable;
use PhpResponse\LiteralText;
use PhpResponse\FallbackText;
use PhpResponse\Request\HeaderFromEnv;
use PhpResponse\Request\MethodFromEnv;
use PhpResponse\Request\PathFromEnv;
use PhpResponse\Request\BodyFromEnv;

final class RequestDetailsText implements Text {
    public function string(): string {
        return (new TemplateVariable(
            new TemplateVariable(
                new TemplateVariable(
                    new TemplateVariable(
                        new LiteralText("<html><body><h1>Your Browser: ${agent}</h1><p>Method: ${method}</p><p>Path: ${path}</p><p>Body: ${body}</p></body></html>"),
                        "agent",
                        new FallbackText(
                            new HeaderFromEnv("User-Agent"),
                            new LiteralText("Unknown")
                        )
                    ),
                    "method",
                    new MethodFromEnv()
                ),
                "path",
                new PathFromEnv()
            ),
            "body",
            new BodyFromEnv()
        ))->string();
    }
}
```

Now, compose the response cleanly:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpResponse\ResponseStatusLineOk;
use PhpResponse\ResponseHeader;
use PhpResponse\ResponseBody;
use PhpResponse\MediaToWire;

(new ResponseStatusLineOk(
    new ResponseHeader(
        new ResponseBody(
            new RequestDetailsText()
        ),
        "Content-Type", "text/html"
    )
))->media(new MediaToWire());
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

Now, pass this view to `ResponseBody`:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpResponse\Request\BodyFromEnv;
use PhpResponse\ResponseStatusLineOk;
use PhpResponse\ResponseHeader;
use PhpResponse\ResponseBody;
use PhpResponse\MediaToWire;
use PhpResponse\JsonSubTree;
use PhpResponse\JsonString;
use PhpResponse\JsonInt;

(new ResponseStatusLineOk(
    new ResponseHeader(
        new ResponseBody(
            new UserWelcomeText(
                new JsonString(
                    new JsonSubTree(new BodyFromEnv(), 'user'),
                    'name'
                ),
                new JsonInt(
                    new JsonSubTree(new BodyFromEnv(), 'user'),
                    'age'
                )
            )
        ),
        "Content-Type", "text/html"
    )
))->media(new MediaToWire());
```

### Encapsulated Template Example (Best Practice)

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

Because `UserProfileText` implements the `Text` interface, it is fully compatible with the response decorator tree. You can pass it as a drop-in argument directly to `ResponseBody`, which is then wrapped by `ResponseHeader` and `ResponseStatusLineOk`:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpResponse\Request\BodyFromEnv;
use PhpResponse\ResponseStatusLineOk;
use PhpResponse\ResponseHeader;
use PhpResponse\ResponseBody;
use PhpResponse\MediaToWire;
use PhpResponse\JsonSubTree;
use PhpResponse\JsonString;
use PhpResponse\JsonInt;
use PhpResponse\TextOfFile;

(new ResponseStatusLineOk(
    new ResponseHeader(
        new ResponseBody(
            new UserProfileText(
                new TextOfFile("template.html"),
                new JsonString(
                    new JsonSubTree(new BodyFromEnv(), 'user'),
                    'name'
                ),
                new JsonInt(
                    new JsonSubTree(new BodyFromEnv(), 'user'),
                    'age'
                )
            )
        ),
        "Content-Type", "text/html"
    )
))->media(new MediaToWire());
```
