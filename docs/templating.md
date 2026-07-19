# Templating

This section covers how to render views and templates using decorators like `TemplateVariable`, `TextOfFile`, and `TextOfNumber` in `PhpResponse`.

---

## Template Components

`PhpResponse` provides several classes to compile templates dynamically:

- **`PhpResponse\TemplateVariable`**: Decorator that replaces `${name}` placeholders in a template string with the string representation of another `Text` object.
- **`PhpResponse\TextOfFile`**: Reads raw content from an external file (e.g., HTML template files).
- **`PhpResponse\TextOfNumber`**: Converts a `Number` object to a `Text` object.

---

## Inline Template Example

If you want to render a response using an in-memory template string, define your view class:

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

---

## Encapsulated Template Example

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

Because `UserProfileText` implements the `Text` interface, it is fully compatible with the response decorator tree. You can pass it as a drop-in argument directly to `Body`, loading the template from an external HTML file using `TextOfFile`:

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
