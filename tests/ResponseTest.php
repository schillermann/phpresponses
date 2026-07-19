<?php

declare(strict_types=1);

namespace PhpResponse;
use PhpResponse\Text\LiteralText;

use PHPUnit\Framework\TestCase;
use PhpResponse\Response\Body;
use PhpResponse\Response\Header;
use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\StatusLine\Created;
use PhpResponse\Response\StatusLine\NoContent;
use PhpResponse\Response\StatusLine\BadRequest;
use PhpResponse\Response\StatusLine\Unauthorized;
use PhpResponse\Response\StatusLine\Forbidden;
use PhpResponse\Response\StatusLine\NotFound;
use PhpResponse\Response\StatusLine\MethodNotAllowed;
use PhpResponse\Response\StatusLine\InternalServerError;
use PhpResponse\Response\Redirect;
use PhpResponse\Response\JsonHeader;
use PhpResponse\Response\Media\Fake;

final class ResponseTest extends TestCase
{
    public function testFullResponse(): void
    {
        $media = (new Ok(
            new Header(
                new Body(new LiteralText("Hello!")),
                "X-Custom", "Value"
            )
        ))->media(new Fake());

        /** @var Fake $media */
        $this->assertEquals(
            [
                "status: 200 OK",
                "header: X-Custom=Value",
                "body: Hello!"
            ],
            $media->array()
        );
    }

    public function testCreated(): void
    {
        $media = (new Created(new Body(new LiteralText("Created!"))))->media(new Fake());
        $this->assertEquals(
            [
                "status: 201 Created",
                "body: Created!"
            ],
            $media->array()
        );
    }

    public function testNoContent(): void
    {
        $media = (new NoContent(new Body(new LiteralText(""))))->media(new Fake());
        $this->assertEquals(
            [
                "status: 204 No Content",
                "body: "
            ],
            $media->array()
        );
    }

    public function testBadRequest(): void
    {
        $media = (new BadRequest(new Body(new LiteralText("Bad Request!"))))->media(new Fake());
        $this->assertEquals(
            [
                "status: 400 Bad Request",
                "body: Bad Request!"
            ],
            $media->array()
        );
    }

    public function testUnauthorized(): void
    {
        $media = (new Unauthorized(new Body(new LiteralText("Unauthorized!"))))->media(new Fake());
        $this->assertEquals(
            [
                "status: 401 Unauthorized",
                "body: Unauthorized!"
            ],
            $media->array()
        );
    }

    public function testForbidden(): void
    {
        $media = (new Forbidden(new Body(new LiteralText("Forbidden!"))))->media(new Fake());
        $this->assertEquals(
            [
                "status: 403 Forbidden",
                "body: Forbidden!"
            ],
            $media->array()
        );
    }

    public function testNotFound(): void
    {
        $media = (new NotFound(new Body(new LiteralText("Not Found!"))))->media(new Fake());
        $this->assertEquals(
            [
                "status: 404 Not Found",
                "body: Not Found!"
            ],
            $media->array()
        );
    }

    public function testMethodNotAllowed(): void
    {
        $media = (new MethodNotAllowed(new Body(new LiteralText("Method Not Allowed!"))))->media(new Fake());
        $this->assertEquals(
            [
                "status: 405 Method Not Allowed",
                "body: Method Not Allowed!"
            ],
            $media->array()
        );
    }

    public function testInternalServerError(): void
    {
        $media = (new InternalServerError(new Body(new LiteralText("Internal Server Error!"))))->media(new Fake());
        $this->assertEquals(
            [
                "status: 500 Internal Server Error",
                "body: Internal Server Error!"
            ],
            $media->array()
        );
    }

    public function testRedirectDefault(): void
    {
        $media = (new Redirect(new LiteralText("/target")))->media(new Fake());
        $this->assertEquals(
            [
                "status: 302 Found",
                "header: Location=/target"
            ],
            $media->array()
        );
    }

    public function testRedirectMovedPermanently(): void
    {
        $media = (new Redirect(new LiteralText("/target-new"), 301))->media(new Fake());
        $this->assertEquals(
            [
                "status: 301 Moved Permanently",
                "header: Location=/target-new"
            ],
            $media->array()
        );
    }

    public function testJsonHeader(): void
    {
        $media = (new JsonHeader(
            new Ok(new Body(new LiteralText('{"abc":123}')))
        ))->media(new Fake());
        $this->assertEquals(
            [
                "header: Content-Type=application/json",
                "status: 200 OK",
                "body: {\"abc\":123}"
            ],
            $media->array()
        );
    }
}
