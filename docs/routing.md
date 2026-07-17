# Routing

This section covers how routing is handled declaratively in `PhpResponse`.

---

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
