# Request Handling

This section covers how to handle request data (such as headers, methods, paths, protocol, query parameters, and body content) and parse JSON requests in `PhpResponse`.

---

## HTTP Request Components

`PhpResponse` provides several decorators to extract details from the active HTTP request:

- **[`PhpResponse\Request\Method`](../src/Request/Method.php)**: Extracts the HTTP request method (e.g., `GET`, `POST`, `PUT`, `DELETE`).
- **[`PhpResponse\Request\Path`](../src/Request/Path.php)**: Extracts the path of the request URI (e.g., `/users/123`).
- **[`PhpResponse\Request\Header`](../src/Request/Header.php)**: Extracts a specific HTTP request header (e.g., `User-Agent`, `Accept`).
- **[`PhpResponse\Request\Protocol`](../src/Request/Protocol.php)**: Extracts the server protocol (e.g., `HTTP/1.1`, `HTTP/2.0`).
- **[`PhpResponse\Request\QueryParam`](../src/Request/QueryParam.php)**: Extracts a query parameter from the URL. Throws an `OutOfBoundsException` if the parameter is missing.
- **[`PhpResponse\Request\PathParam`](../src/Request/PathParam.php)**: Dynamically extracts path parameters using regex capture groups from the request path.
- **[`PhpResponse\Request\Body`](../src/Request/Body.php)**: Reads the raw request body stream.

### Request Extraction Example

You can compose request parsing declaratively by encapsulating request variables in a custom view class:

```php
<?php

use PhpResponse\Text;
use PhpResponse\Text\LiteralText;
use PhpResponse\Text\FallbackText;
use PhpResponse\Request\Header;
use PhpResponse\Request\Method;
use PhpResponse\Request\Path;
use PhpResponse\Request\Protocol;
use PhpResponse\Request\QueryParam;
use PhpResponse\Request\Body;
use PhpResponse\Text\TemplateVariable;

final class RequestDetailsText implements Text {
    public function string(): string {
        return (new TemplateVariable(
            new TemplateVariable(
                new TemplateVariable(
                    new TemplateVariable(
                        new TemplateVariable(
                            new LiteralText("<html><body><h1>Browser: ${agent}</h1><p>Protocol: ${proto}</p><p>Method: ${method}</p><p>Path: ${path}</p><p>Body: ${body}</p></body></html>"),
                            "agent",
                            new FallbackText(
                                new Header("User-Agent"),
                                new LiteralText("Unknown")
                            )
                        ),
                        "proto",
                        new Protocol()
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

---

## JSON Request Parsing

To parse JSON requests (e.g. extracting nested subtrees or properties), see the dedicated **[JSON Handling](json.md)** documentation.
