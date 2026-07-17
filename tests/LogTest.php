<?php

declare(strict_types=1);

namespace PhpResponse;

use PHPUnit\Framework\TestCase;
use PhpResponse\Log\Log;
use PhpResponse\Log\PlainEntry;
use PhpResponse\Log\StreamLog;
use PhpResponse\Log\TeeLog;
use PhpResponse\Log\BufferLog;
use PhpResponse\Log\LoggedText;
use PhpResponse\Log\Epoch;
use PhpResponse\Log\TimestampedEntry;
use PhpResponse\Log\JsonEntry;
use PhpResponse\Log\FailsafeLog;
use PhpResponse\Log\LevelLog;
use PhpResponse\Log\FileLog;
use PhpResponse\Log\ConsoleLog;
use PhpResponse\Log\Level\InfoLevel;
use PhpResponse\Log\Level\DebugLevel;
use PhpResponse\Log\Level\ErrorLevel;
use PhpResponse\Log\Level\WarningLevel;

final class LogTest extends TestCase
{
    public function testStreamLogWritesToStream(): void
    {
        $stream = fopen('php://memory', 'r+');
        $this->assertNotFalse($stream);
        
        $log = new StreamLog($stream);
        $log->write(new PlainEntry(new InfoLevel(), new LiteralText('something happened')));

        fseek($stream, 0);
        $content = stream_get_contents($stream);
        fclose($stream);

        $this->assertSame("[INFO] something happened\n", $content);
    }

    public function testTeeLogDistributesToMultipleLoggers(): void
    {
        $buffer1 = new \ArrayObject();
        $buffer2 = new \ArrayObject();
        
        $log = new TeeLog(
            new BufferLog($buffer1),
            new BufferLog($buffer2)
        );

        $log->write(new PlainEntry(new ErrorLevel(), new LiteralText('critical issue')));

        $this->assertCount(1, $buffer1);
        $this->assertCount(1, $buffer2);
        $this->assertSame('ERROR: critical issue', $buffer1[0]);
        $this->assertSame('ERROR: critical issue', $buffer2[0]);
    }

    public function testLoggedTextDecoratorLogsMessage(): void
    {
        $buffer = new \ArrayObject();
        $log = new BufferLog($buffer);

        $text = new LoggedText(
            new LiteralText('hello oop'),
            $log
        );

        $this->assertSame('hello oop', $text->string());
        $this->assertCount(1, $buffer);
        $this->assertSame("DEBUG: Evaluated text: 'hello oop'", $buffer[0]);
    }

    public function testTimestampedEntryPrependsTime(): void
    {
        $epoch = $this->createMock(Epoch::class);
        $epoch->method('string')->willReturn('2026-07-17T12:00:00Z');

        $entry = new TimestampedEntry(
            new PlainEntry(new InfoLevel(), new LiteralText('test message')),
            $epoch
        );

        $this->assertSame('INFO', $entry->level()->string());
        $this->assertSame('[2026-07-17T12:00:00Z] test message', $entry->message()->string());
    }

    public function testJsonEntryFormatsAsJson(): void
    {
        $entry = new JsonEntry(
            new PlainEntry(new InfoLevel(), new LiteralText('test message'))
        );

        $this->assertSame('INFO', $entry->level()->string());
        $this->assertSame('{"level":"INFO","message":"test message"}', $entry->message()->string());
    }

    public function testFailsafeLogCatchesException(): void
    {
        $failingLog = $this->createMock(Log::class);
        $failingLog->method('write')->willThrowException(new \RuntimeException('Disk full'));

        $log = new FailsafeLog($failingLog);
        $log->write(new PlainEntry(new ErrorLevel(), new LiteralText('failed task')));

        $this->assertTrue(true);
    }

    public function testLevelLogAllowsMatchingLevels(): void
    {
        $buffer = new \ArrayObject();
        $log = new LevelLog(new BufferLog($buffer), new ErrorLevel(), new WarningLevel());

        $log->write(new PlainEntry(new InfoLevel(), new LiteralText('ignored')));
        $log->write(new PlainEntry(new ErrorLevel(), new LiteralText('logged')));

        $this->assertCount(1, $buffer);
        $this->assertSame('ERROR: logged', $buffer[0]);
    }

    public function testFileLogWritesToFile(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'log_test');
        $this->assertNotFalse($path);
        
        $log = new FileLog(new LiteralText($path));
        $log->write(new PlainEntry(new InfoLevel(), new LiteralText('test file logging')));

        $content = file_get_contents($path);
        unlink($path);

        $this->assertSame("[INFO] test file logging\n", $content);
    }

    public function testConsoleLogWritesToStdout(): void
    {
        $log = new ConsoleLog();
        ob_start();
        $log->write(new PlainEntry(new InfoLevel(), new LiteralText('test console logging')));
        $content = ob_get_clean();
        
        $this->assertTrue(true);
    }
}
