# PhpResponses

A web framework in PHP that respects OOP fully, following the principles of "Elegant Objects" by Yegor Bugayenko.

## Core Principles
- **Strictly OOP**: No getters, no setters, no nulls.
- **Composition over Inheritance**: Functionality is built using decorators.
- **Immutability**: Responses are defined through nested objects.

## Quick Start

Create an `index.php` and use decorators to compose your response:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpResponses\ResponseBody;
use PhpResponses\ResponseHeader;
use PhpResponses\ResponseStatusLineOk;
use PhpResponses\MediaToWire;

(new ResponseStatusLineOk(
    new ResponseHeader(
        new ResponseBody("<h1>Hello from PhpResponses!</h1>"),
        "Content-Type", "text/html"
    )
))->media(new MediaToWire());
```

## Request Example

You can also wrap the incoming request and extract data from it:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpResponses\RequestFromEnv;
use PhpResponses\BodyFromRequest;
use PhpResponses\ResponseStatusLineOk;
use PhpResponses\ResponseHeader;
use PhpResponses\ResponseBody;
use PhpResponses\MediaToWire;

$request = new RequestFromEnv();

$agent = $request->header("User-Agent");
$body = (new BodyFromRequest($request))->string();
$method = $request->requestLine()->method();
$path = $request->requestLine()->path();

(new ResponseStatusLineOk(
    new ResponseHeader(
        new ResponseBody(
            sprintf(
                "<html><body><h1>Your Browser: %s</h1><p>Method: %s</p><p>Path: %s</p><p>Body: %s</p></body></html>",
                $agent->exists() ? $agent->string() : "Unknown",
                $method,
                $path,
                $body
            )
        ),
        "Content-Type", "text/html"
    )
))->media(new MediaToWire());
```

Now just open this file in your browser via a web server (like `php -S localhost:8000`).
`).
