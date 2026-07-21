# Routing

This section covers how routing is handled declaratively in `PhpResponse`.

---

We compose routes cleanly using a decorator chain (Chain of Responsibility).
The routing criteria themselves are `Response` decorators that either delegate to a `target` response on match, or a `fallback` response on mismatch.

## Basic Route Matching

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\Header;
use PhpResponse\Response\Body;
use PhpResponse\Response\Media\Wire;
use PhpResponse\Text\LiteralText;
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

---

## Route Groups (`PrefixPath`) & Relative Routing (`StripPrefix`)

You can group routes under a common path prefix (such as `/api/v1`) using `PrefixPath`.
Combine it with `StripPrefix` to build relative nested route trees:

```php
use PhpResponse\Route\PrefixPath;
use PhpResponse\Route\ExactPath;
use PhpResponse\Text\StripPrefix;
use PhpResponse\Request\Path;

$fullPath = new Path();
$relative = new StripPrefix("/api/v1", $fullPath);

$apiRoutes = new PrefixPath(
    "/api/v1",
    new ExactPath(
        "/users",
        $listUsersResponse,
        $notFoundResponse,
        $relative
    ),
    $notFoundResponse,
    $fullPath
);
```

---

## Dynamic Path Parameter Extraction (`PathParam`)

Path parameters are extracted without mutable context bags by passing a `PathParam` (which implements `Text`) to target components:

```php
use PhpResponse\Request\PathParam;

// Extract named capture group 'id' dynamically
$userId = new PathParam("#^/users/(?<id>\d+)$#", "id");

// Extract numeric capture group 1 dynamically
$orderId = new PathParam("#^/orders/(\d+)$#", 1);
```
