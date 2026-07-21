<?php

declare(strict_types=1);

namespace PhpResponse;

use PHPUnit\Framework\TestCase;
use PhpResponse\Response\Body;
use PhpResponse\Response\Media\Fake;
use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Route\ExactPath;
use PhpResponse\Route\PrefixPath;
use PhpResponse\Text\LiteralText;
use PhpResponse\Text\StripPrefix;

final class PrefixPathTest extends TestCase
{
    public function testRoutesToTargetWhenPrefixMatches(): void
    {
        $media = (new PrefixPath(
            '/api/v1',
            new Ok(new Body(new LiteralText('API V1 Target'))),
            new Body(new LiteralText('Not Found')),
            new LiteralText('/api/v1/users')
        ))->media(new Fake());

        /** @var Fake $media */
        $this->assertEquals(
            [
                'status: 200 OK',
                'body: API V1 Target',
            ],
            $media->array()
        );
    }

    public function testRoutesToFallbackWhenPrefixDoesNotMatch(): void
    {
        $media = (new PrefixPath(
            '/api/v1',
            new Ok(new Body(new LiteralText('API V1 Target'))),
            new Body(new LiteralText('Prefix Fallback')),
            new LiteralText('/web/home')
        ))->media(new Fake());

        /** @var Fake $media */
        $this->assertEquals(
            [
                'body: Prefix Fallback',
            ],
            $media->array()
        );
    }

    public function testNestedPrefixRoutingWithStrippedPath(): void
    {
        $fullPath = new LiteralText('/api/v1/users');
        $relative = new StripPrefix('/api/v1', $fullPath);

        $media = (new PrefixPath(
            '/api/v1',
            new ExactPath(
                '/users',
                new Ok(new Body(new LiteralText('Users List'))),
                new Body(new LiteralText('Sub-route Not Found')),
                $relative
            ),
            new Body(new LiteralText('Prefix Not Found')),
            $fullPath
        ))->media(new Fake());

        /** @var Fake $media */
        $this->assertEquals(
            [
                'status: 200 OK',
                'body: Users List',
            ],
            $media->array()
        );
    }
}
