# Request Handling

This section covers how to handle request data (such as headers, methods, paths, protocol, query parameters, and body content) and parse JSON requests in `PhpResponse`.

---

## HTTP Request Components

`PhpResponse` provides several decorators to extract details from the active HTTP request:

- **`PhpResponse\Request\Method`**: Extracts the HTTP request method (e.g., `GET`, `POST`, `PUT`, `DELETE`).
- **`PhpResponse\Request\Path`**: Extracts the path of the request URI (e.g., `/users/123`).
- **`PhpResponse\Request\Header`**: Extracts a specific HTTP request header (e.g., `User-Agent`, `Accept`).
- **`PhpResponse\Request\Protocol`**: Extracts the server protocol (e.g., `HTTP/1.1`, `HTTP/2.0`).
- **`PhpResponse\Request\QueryParam`**: Extracts a query parameter from the URL. Throws an `OutOfBoundsException` if the parameter is missing.
- **`PhpResponse\Request\Body`**: Reads the raw request body stream.

### Request Extraction Example

You can compose request parsing declaratively by encapsulating request variables in a custom view class:

```php
<?php

use PhpResponse\Text;
use PhpResponse\LiteralText;
use PhpResponse\FallbackText;
use PhpResponse\Request\Header;
use PhpResponse\Request\Method;
use PhpResponse\Request\Path;
use PhpResponse\Request\Protocol;
use PhpResponse\Request\QueryParam;
use PhpResponse\Request\Body;
use PhpResponse\TemplateVariable;

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

## JSON Request Parsing & Extraction

You can parse JSON requests declaratively by wrapping the request body in decorators that extract specific subtrees or typed properties:

- **`PhpResponse\JsonSubTree`**: Extracts a JSON subtree by key and returns it as a JSON-encoded string.
- **`PhpResponse\JsonString`**: Extracts a string field by key from a JSON string. Throws `DomainException` if the value is missing or not a string.
- **`PhpResponse\JsonInt`**: Extracts an integer field by key from a JSON string, implementing the `Number` interface. Throws `DomainException` if the value is missing or not an integer.

### JSON Extraction Example

```php
<?php

use PhpResponse\Request\Body;
use PhpResponse\JsonSubTree;
use PhpResponse\JsonString;
use PhpResponse\JsonInt;

// Extract a sub-object from the JSON request body: {"user": {"name": "Alice", "age": 30}}
$userSubTree = new JsonSubTree(new Body(), 'user');

// Extract properties from the sub-object
$name = new JsonString($userSubTree, 'name');
$age = new JsonInt($userSubTree, 'age'); // Implements Number
```
