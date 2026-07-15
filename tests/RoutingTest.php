<?php

declare(strict_types=1);

namespace PhpResponse;

use PHPUnit\Framework\TestCase;
use PhpResponse\Response\Body;
use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\Media\Fake;
use PhpResponse\Route\Method;
use PhpResponse\Route\RegexPath;
use PhpResponse\Route\ExactPath;

final class RoutingTest extends TestCase
{
    public function testRoutesCorrectlyOnMatch(): void
    {
        $media = (new Method(
            "GET",
            new RegexPath(
                "#^/home$#",
                new Ok(new Body(new LiteralText("Home page"))),
                new Body(new LiteralText("Unreached Path Fallback")),
                new LiteralText("/home")
            ),
            new Body(new LiteralText("Method Fallback")),
            new LiteralText("GET")
        ))->media(new Fake());

        /** @var Fake $media */
        $this->assertEquals(
            [
                "status: 200 OK",
                "body: Home page"
            ],
            $media->array()
        );
    }

    public function testRoutesCorrectlyOnExactPathMatch(): void
    {
        $media = (new Method(
            "GET",
            new ExactPath(
                "/about",
                new Ok(new Body(new LiteralText("About page"))),
                new Body(new LiteralText("404")),
                new LiteralText("/about")
            ),
            new Body(new LiteralText("Method Fallback")),
            new LiteralText("GET")
        ))->media(new Fake());

        /** @var Fake $media */
        $this->assertEquals(
            [
                "status: 200 OK",
                "body: About page"
            ],
            $media->array()
        );
    }

    public function testReturnsFallbackWhenNoMatch(): void
    {
        $media = (new Method(
            "POST",
            new RegexPath(
                "#^/home$#",
                new Ok(new Body(new LiteralText("Home"))),
                new Body(new LiteralText("Regex Fallback")),
                new LiteralText("/home")
            ),
            new Body(new LiteralText("Method Fallback")),
            new LiteralText("GET")
        ))->media(new Fake());

        /** @var Fake $media */
        $this->assertEquals(
            [
                "body: Method Fallback"
            ],
            $media->array()
        );
    }
}
