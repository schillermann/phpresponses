<?php

declare(strict_types=1);

namespace PhpResponse\Request;

use RuntimeException;
use PhpResponse\Text;

final class RequestFile implements File
{
    private string $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function saveTo(Text $destination): void
    {
        if (!isset($_FILES[$this->key]) || !is_array($_FILES[$this->key])) {
            throw new RuntimeException("File '{$this->key}' was not uploaded.");
        }

        $file = $_FILES[$this->key];
        if (isset($file['error']) && $file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException("Upload failed for file '{$this->key}' with error code {$file['error']}.");
        }

        if (!isset($file['tmp_name']) || !is_string($file['tmp_name']) || !file_exists($file['tmp_name'])) {
            throw new RuntimeException("Temporary file for '{$this->key}' does not exist or is invalid.");
        }

        $destPath = $destination->string();
        $directory = dirname($destPath);

        if (!is_dir($directory)) {
            if (!@mkdir($directory, 0777, true) && !is_dir($directory)) {
                throw new RuntimeException("Failed to create destination directory '{$directory}'.");
            }
        }

        $moved = @move_uploaded_file($file['tmp_name'], $destPath);
        if (!$moved) {
            if (!@copy($file['tmp_name'], $destPath)) {
                throw new RuntimeException("Failed to save uploaded file '{$this->key}' to destination '{$destPath}'.");
            }
        }
    }
}
