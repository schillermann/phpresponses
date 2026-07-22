<?php

declare(strict_types=1);

namespace PhpResponse;

use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use PhpResponse\Request\Cookie;
use PhpResponse\Request\FormParam;
use PhpResponse\Request\RequestFile;
use PhpResponse\Text\LiteralText;
use RuntimeException;

final class RequestParsingTest extends TestCase
{
    protected function setUp(): void
    {
        $_POST = [];
        $_COOKIE = [];
        $_FILES = [];
    }

    public function testFormParamReturnsPostValue(): void
    {
        $_POST['username'] = 'john_doe';
        $param = new FormParam('username');

        $this->assertEquals('john_doe', $param->string());
    }

    public function testFormParamHandlesArrayInput(): void
    {
        $_POST['tags'] = ['php', 'oop', 'web'];
        $param = new FormParam('tags');

        $this->assertEquals('php,oop,web', $param->string());
    }

    public function testFormParamThrowsExceptionWhenMissing(): void
    {
        $param = new FormParam('missing_field');

        $this->expectException(OutOfBoundsException::class);
        $param->string();
    }

    public function testCookieReturnsCookieValue(): void
    {
        $_COOKIE['session_token'] = 'secret_token_123';
        $cookie = new Cookie('session_token');

        $this->assertEquals('secret_token_123', $cookie->string());
    }

    public function testCookieHandlesArrayInput(): void
    {
        $_COOKIE['prefs'] = ['theme' => 'dark', 'lang' => 'en'];
        $cookie = new Cookie('prefs');

        $this->assertEquals('dark,en', $cookie->string());
    }

    public function testCookieThrowsExceptionWhenMissing(): void
    {
        $cookie = new Cookie('non_existent');

        $this->expectException(OutOfBoundsException::class);
        $cookie->string();
    }

    public function testRequestFileSavesToDestination(): void
    {
        $tmpSource = sys_get_temp_dir() . '/php_upload_test_' . uniqid() . '.tmp';
        $tmpTarget = sys_get_temp_dir() . '/php_upload_target_' . uniqid() . '.tmp';
        file_put_contents($tmpSource, 'uploaded content');

        $_FILES['avatar'] = [
            'name' => 'avatar.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => $tmpSource,
            'error' => UPLOAD_ERR_OK,
            'size' => 16,
        ];

        try {
            $file = new RequestFile('avatar');
            $file->saveTo(new LiteralText($tmpTarget));

            $this->assertFileExists($tmpTarget);
            $this->assertEquals('uploaded content', file_get_contents($tmpTarget));
        } finally {
            if (file_exists($tmpSource)) {
                unlink($tmpSource);
            }
            if (file_exists($tmpTarget)) {
                unlink($tmpTarget);
            }
        }
    }

    public function testRequestFileCreatesDestinationDirectory(): void
    {
        $tmpSource = sys_get_temp_dir() . '/php_upload_test_' . uniqid() . '.tmp';
        $nestedSubdir = sys_get_temp_dir() . '/nested_dir_' . uniqid();
        $tmpTarget = $nestedSubdir . '/saved_file.txt';
        file_put_contents($tmpSource, 'nested upload');

        $_FILES['document'] = [
            'name' => 'doc.txt',
            'type' => 'text/plain',
            'tmp_name' => $tmpSource,
            'error' => UPLOAD_ERR_OK,
            'size' => 13,
        ];

        try {
            $file = new RequestFile('document');
            $file->saveTo(new LiteralText($tmpTarget));

            $this->assertFileExists($tmpTarget);
            $this->assertEquals('nested upload', file_get_contents($tmpTarget));
        } finally {
            if (file_exists($tmpSource)) {
                unlink($tmpSource);
            }
            if (file_exists($tmpTarget)) {
                unlink($tmpTarget);
            }
            if (is_dir($nestedSubdir)) {
                rmdir($nestedSubdir);
            }
        }
    }

    public function testRequestFileThrowsExceptionWhenNotUploaded(): void
    {
        $file = new RequestFile('missing_key');

        $this->expectException(RuntimeException::class);
        $file->saveTo(new LiteralText('/tmp/dummy'));
    }

    public function testRequestFileThrowsExceptionOnUploadError(): void
    {
        $_FILES['avatar'] = [
            'name' => 'avatar.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => '/tmp/nonexistent',
            'error' => UPLOAD_ERR_NO_FILE,
            'size' => 0,
        ];

        $file = new RequestFile('avatar');

        $this->expectException(RuntimeException::class);
        $file->saveTo(new LiteralText('/tmp/dummy'));
    }
}
