# HTTP Status Lines, Headers & Redirects

This section covers how to represent HTTP status lines, response headers, and redirects in `PhpResponse`.

---

## Status Line Decorators

In `PhpResponse`, HTTP status codes are represented as decorators implementing the [Response](../src/Response/Response.php) interface.
They wrap an inner response to inject the HTTP status code and text.

The following status line decorators are available under the `PhpResponse\Response\StatusLine` namespace:

* **[Ok](../src/Response/StatusLine/Ok.php)**: `200 OK`
* **[Created](../src/Response/StatusLine/Created.php)**: `201 Created`
* **[NoContent](../src/Response/StatusLine/NoContent.php)**: `204 No Content`
* **[BadRequest](../src/Response/StatusLine/BadRequest.php)**: `400 Bad Request`
* **[Unauthorized](../src/Response/StatusLine/Unauthorized.php)**: `401 Unauthorized`
* **[Forbidden](../src/Response/StatusLine/Forbidden.php)**: `403 Forbidden`
* **[NotFound](../src/Response/StatusLine/NotFound.php)**: `404 Not Found`
* **[MethodNotAllowed](../src/Response/StatusLine/MethodNotAllowed.php)**: `405 Method Not Allowed`
* **[InternalServerError](../src/Response/StatusLine/InternalServerError.php)**: `500 Internal Server Error`

### Example Usage

```php
<?php

use PhpResponse\Response\Body;
use PhpResponse\Response\StatusLine\NotFound;
use PhpResponse\Text\LiteralText;

$response = new NotFound(
    new Body(new LiteralText("The requested resource was not found on this server."))
);
```

---

## Headers & Specialized Formatting

HTTP headers are applied using response decorators.

* **[Header](../src/Response/Header.php)**: A generic decorator that sets any custom header on the response.
* **[JsonHeader](../src/Response/JsonHeader.php)**: A specialized header decorator that sets the `Content-Type: application/json` header.

### Example Usage

```php
<?php

use PhpResponse\Response\Body;
use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\JsonHeader;
use PhpResponse\Text\Json\JsonObject;
use PhpResponse\Text\Json\JsonMember;

$response = new JsonHeader(
    new Ok(
        new Body(
            new JsonObject(
                new JsonMember("status", "ok")
            )
        )
    )
);
```

---

## Redirects

The **[Redirect](../src/Response/Redirect.php)** response decorator redirects the client to a different URL.
It takes a target path/URL (represented as a [Text](../src/Text.php) object) and an optional redirect status code (defaults to `302 Found`).

It automatically maps standard HTTP redirect codes to their respective status messages (e.g., `301 Moved Permanently`, `302 Found`, `303 See Other`, `307 Temporary Redirect`, `308 Permanent Redirect`).

### Example Usage

```php
<?php

use PhpResponse\Response\Redirect;
use PhpResponse\Text\LiteralText;

// Temporary redirect (302 Found)
$redirect = new Redirect(new LiteralText("/dashboard"));

// Permanent redirect (301 Moved Permanently)
$permanentRedirect = new Redirect(new LiteralText("/new-location"), 301);
```

---

## JSON Responses

To parse incoming JSON request payloads or build structured JSON responses, see the dedicated **[JSON Handling](json.md)** documentation.
