# Request Handling

This section covers how to handle request data (such as headers, methods, paths, protocol, query parameters, form parameters, cookies, file uploads, and body content) and parse JSON requests in `PhpResponse`.

---

## HTTP Request Components

`PhpResponse` provides several decorators to extract details from the active HTTP request:

- **[`PhpResponse\Request\Method`](../src/Request/Method.php)**: Extracts the HTTP request method (e.g., `GET`, `POST`, `PUT`, `DELETE`).
- **[`PhpResponse\Request\Path`](../src/Request/Path.php)**: Extracts the path of the request URI (e.g., `/users/123`).
- **[`PhpResponse\Request\Header`](../src/Request/Header.php)**: Extracts a specific HTTP request header (e.g., `User-Agent`, `Accept`).
- **[`PhpResponse\Request\Protocol`](../src/Request/Protocol.php)**: Extracts the server protocol (e.g., `HTTP/1.1`, `HTTP/2.0`).
- **[`PhpResponse\Request\QueryParam`](../src/Request/QueryParam.php)**: Extracts a query parameter from the URL. Throws an `OutOfBoundsException` if missing.
- **[`PhpResponse\Request\PathParam`](../src/Request/PathParam.php)**: Dynamically extracts path parameters using regex capture groups from the request path.
- **[`PhpResponse\Request\FormParam`](../src/Request/FormParam.php)**: Extracts form parameters from `POST` payload. Throws `OutOfBoundsException` if missing.
- **[`PhpResponse\Request\Cookie`](../src/Request/Cookie.php)**: Extracts client cookie values. Throws `OutOfBoundsException` if missing.
- **[`PhpResponse\Request\RequestFile`](../src/Request/RequestFile.php)**: Encapsulates file saving via `File` interface.
- **[`PhpResponse\Request\Body`](../src/Request/Body.php)**: Reads the raw request body stream.

---

## Input Parsing & Form Handling Examples

### 1. Form Parameters & Cookie Extraction

You can compose form fields and cookies declaratively, decorating missing inputs with fallback values and using constructor attributes for maximum testability:

```php
<?php

use PhpResponse\Text;
use PhpResponse\Text\LiteralText;
use PhpResponse\Text\FallbackText;
use PhpResponse\Text\FormattedText;
use PhpResponse\Request\FormParam;
use PhpResponse\Request\Cookie;

final class UserFormPayload implements Text
{
    private Text $payload;

    public function __construct(
        Text $username = new FallbackText(new FormParam("username"), new LiteralText("Guest")),
        Text $sessionToken = new FallbackText(new Cookie("session_id"), new LiteralText("anonymous"))
    ) {
        $this->payload = new FormattedText(
            new LiteralText("User: %s, Session: %s"),
            $username,
            $sessionToken
        );
    }

    public function string(): string
    {
        return $this->payload->string();
    }
}
```

### 2. File Uploads (`File`)

File uploads are handled by objects implementing `File`. Instead of querying array containers for paths, you send a message instructing the file to save itself to a destination path:

```php
<?php

use PhpResponse\Request\RequestFile;
use PhpResponse\Request\File;
use PhpResponse\Text\LiteralText;

// Saves the uploaded file associated with input name "avatar"
$file = new RequestFile("avatar");
$file->saveTo(new LiteralText("/var/www/uploads/avatar.png"));
```

---

## General Request Extraction Example

You can compose request parsing declaratively by encapsulating request attributes in a custom view class:

```php
<?php

use PhpResponse\Text;
use PhpResponse\Text\LiteralText;
use PhpResponse\Text\FallbackText;
use PhpResponse\Request\Header;
use PhpResponse\Request\Method;
use PhpResponse\Request\Path;
use PhpResponse\Request\Protocol;
use PhpResponse\Request\Body;
use PhpResponse\Text\TemplateVariable;

final class RequestDetailsText implements Text 
{
    private Text $template;

    public function __construct(
        Text $agent = new FallbackText(new Header("User-Agent"), new LiteralText("Unknown")),
        Text $proto = new Protocol(),
        Text $method = new Method(),
        Text $path = new Path(),
        Text $body = new Body()
    ) {
        $this->template = new TemplateVariable(
            new TemplateVariable(
                new TemplateVariable(
                    new TemplateVariable(
                        new TemplateVariable(
                            new LiteralText("<html><body><h1>Browser: ${agent}</h1><p>Protocol: ${proto}</p><p>Method: ${method}</p><p>Path: ${path}</p><p>Body: ${body}</p></body></html>"),
                            "agent", $agent
                        ),
                        "proto", $proto
                    ),
                    "method", $method
                ),
                "path", $path
            ),
            "body", $body
        );
    }

    public function string(): string 
    {
        return $this->template->string();
    }
}
```

---

## JSON Request Parsing

To parse JSON requests (e.g. extracting nested subtrees or properties), see the dedicated **[JSON Handling](json.md)** documentation.
