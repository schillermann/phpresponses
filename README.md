# PhpResponses

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
use PhpResponses\BodyTextFromRequest;
use PhpResponses\ResponseStatusLineOk;
use PhpResponses\ResponseHeader;
use PhpResponses\ResponseBody;
use PhpResponses\MediaToWire;

$request = new RequestFromEnv();

$agent = $request->header("User-Agent");
$body = (new BodyTextFromRequest($request))->string();
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

## JSON Request Example

You can also parse JSON requests:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpResponses\RequestFromEnv;
use PhpResponses\BodyTextFromRequest;
use PhpResponses\ResponseStatusLineOk;
use PhpResponses\ResponseHeader;
use PhpResponses\ResponseBody;
use PhpResponses\MediaToWire;
use PhpResponses\JsonSubTree;
use PhpResponses\JsonString;
use PhpResponses\JsonInt;

$body = new BodyTextFromRequest(new RequestFromEnv());
$userNode = new JsonSubTree($body, 'user');
$name = new JsonString($userNode, 'name');
$age  = new JsonInt($userNode, 'age');

(new ResponseStatusLineOk(
    new ResponseHeader(
        new ResponseBody(
            sprintf(
                "<html><body><h1>Hello, %s!</h1><p>You are %d years old.</p></body></html>",
                $name->string(),
                $age->int()
            )
        ),
        "Content-Type", "text/html"
    )
))->media(new MediaToWire());
```
