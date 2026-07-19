# JSON Handling (Parsing & Building)

This section covers how to work with JSON payloads in `PhpResponse`—both parsing incoming JSON request data and building structured JSON responses.

---

## 1. Building JSON Responses (Writing)

To output JSON data from your application, use the specialized JSON builders under the `PhpResponse\Text\Json` namespace. These builders let you compose JSON structures declaratively using objects instead of dealing with raw associative arrays or manually concatenated strings.

### Key Components

* **[JsonObject](../src/Text/Json/JsonObject.php)**: Represents a JSON object (`{...}`) composed of `JsonMember` instances.
* **[JsonArray](../src/Text/Json/JsonArray.php)**: Represents a JSON array (`[...]`) of values.
* **[JsonMember](../src/Text/Json/JsonMember.php)**: Represents a key-value member (`"key":value`) in an object.
* **[JsonText](../src/Text/Json/JsonText.php)**: A decorator that escapes and quotes any generic text value to comply with the JSON string literal specification.
  * *Note: Any raw PHP string passed directly to `JsonMember` or `JsonArray` is automatically wrapped in `JsonText` under the hood.*
* **[JsonNumber](../src/Text/Json/JsonNumber.php)**: Represents unquoted integer or float values (e.g. `30` or `9.99`).
* **[LiteralText](../src/Text/LiteralText.php) (or any generic `Text` object)**: Since the builders accept `Json|Text|string`, passing a `Text` object directly will output its string raw and untouched. Use this to embed pre-serialized JSON blocks.
* **[JsonTrue](../src/Text/Json/JsonTrue.php) & [JsonFalse](../src/Text/Json/JsonFalse.php)**: Zero-state decorators representing boolean constant literals.

### Example: Composing an Outgoing JSON Response

```php
<?php

use PhpResponse\Response\Body;
use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\JsonHeader;
use PhpResponse\Text\Json\JsonObject;
use PhpResponse\Text\Json\JsonMember;
use PhpResponse\Text\Json\JsonArray;
use PhpResponse\Text\Json\JsonNumber;
use PhpResponse\Text\Json\JsonTrue;
use PhpResponse\Text\LiteralText;

$response = new JsonHeader(
    new Ok(
        new Body(
            new JsonObject(
                new JsonMember("name", "Alice"),                     // String: "Alice"
                new JsonMember("age", new JsonNumber(30)),           // Number: 30
                new JsonMember("active", new JsonTrue()),            // Boolean: true
                new JsonMember("tags", new JsonArray("dev", "oop")),  // Array: ["dev", "oop"]
                new JsonMember("settings", new LiteralText('{"theme":"dark"}')) // Pre-serialized JSON raw
            )
        )
    )
);
```

---

## 2. Parsing JSON Requests (Reading)

To extract and parse JSON data sent by clients in incoming request payloads, wrap the request body using the decorators under the `PhpResponse` namespace:

### Key Components

* **[JsonSubTree](../src/Text/Json/JsonSubTree.php)**: Extracts a specific nested JSON node/subtree by key and returns it as a JSON-encoded string.
* **[JsonString](../src/Text/Json/JsonString.php)**: Extracts a string field by key from a JSON string. Throws `DomainException` if the value is missing or not a string.
* **[JsonInt](../src/JsonInt.php)**: Extracts an integer field by key from a JSON string, implementing the [Number](../src/Number.php) interface. Throws `DomainException` if the value is missing or not an integer.

### Example: Reading an Incoming JSON Request

Given the incoming JSON request:
```json
{
  "user": {
    "name": "Alice",
    "age": 30
  }
}
```

You can extract the properties declaratively as follows:

```php
<?php

use PhpResponse\Request\Body;
use PhpResponse\Text\Json\JsonSubTree;
use PhpResponse\Text\Json\JsonString;
use PhpResponse\JsonInt;

// 1. Extract the nested "user" sub-object
$userSubTree = new JsonSubTree(new Body(), 'user');

// 2. Extract string and integer fields from the user sub-object
$name = new JsonString($userSubTree, 'name');
$age = new JsonInt($userSubTree, 'age'); // Implements Number

echo $name->string(); // Outputs: Alice
echo $age->int();     // Outputs: 30
```
