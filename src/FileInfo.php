<?php

namespace PhpDevCommunity\FileSystem;

final class FileInfo
{
    private \SplFileInfo $fileInfo;

    /**
     * FileInfo constructor.
     * @param string $filePath The path to the file
     * @throws \RuntimeException if the file does not exist
     * @throws \RuntimeException if the path is not a valid file
     */
    public function __construct(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("File not found at path: $filePath");
        }

        if (!is_file($filePath)) {
            throw new \RuntimeException("The path provided is not a valid file: $filePath");
        }

        $this->fileInfo = new \SplFileInfo($filePath);
    }

    /**
     * Get the SplFileInfo object associated with this file.
     * @return \SplFileInfo
     */
    public function getSplFileInfo(): \SplFileInfo
    {
        return $this->fileInfo;
    }

    /**
     * Get the real path of the file.
     * @return string
     */
    public function getRealPath(): string
    {
        return $this->fileInfo->getRealPath() ?: $this->fileInfo->getPathname();
    }

    public function getFilename(): string
    {
        return $this->fileInfo->getFilename();
    }

    /**
     * Get the size of the file.
     * @return int
     */
    public function getSize(): int
    {
        return $this->fileInfo->getSize();
    }

    /**
     * Open the file for reading or writing.
     * @param string $mode The mode in which to open the file
     * @return \SplFileObject
     */
    public function openFile(string $mode = 'r'): \SplFileObject
    {
        return $this->fileInfo->openFile($mode);
    }

    /**
     * Get the MIME type of the file.
     * @return string|null The MIME type of the file, or null if it cannot be determined
     */
    public function getMimeType(): ?string
    {
        if (function_exists('mime_content_type')) {
            return mime_content_type($this->fileInfo->getRealPath()) ?: null;
        }

        return null;
    }

    /**
     * Get the extension of the file.
     * @return string
     */
    public function getExtension(): string
    {
        return $this->fileInfo->getExtension();
    }

    public function toBase64(): string
    {
        return base64_encode($this->toBinary());
    }

    public function toDataUrl(): string
    {
        return sprintf('data:%s;base64,%s', $this->getMimeType(), $this->toBase64());
    }

    public function toBinary(): string
    {
        return file_get_contents($this->fileInfo->getRealPath());
    }

    public function getMetadata(): array
    {
        return [
            'path' => $this->getRealPath(),
            'size' => $this->getSize(),
            'size_in_kb' => round($this->getSize() / 1024, 2) ,
            'size_in_mb' => round($this->getSize() / 1024 / 1024, 2),
            'mime_type' => $this->getMimeType(),
            'extension' => $this->getExtension(),
            'basename' => $this->getSplFileInfo()->getBasename(),
            'last_modified' => date('Y-m-d H:i:s', $this->fileInfo->getMTime()),
            'creation_date' => date('Y-m-d H:i:s', $this->fileInfo->getCTime())
        ];
    }

    public function compareWith(FileInfo $fileInfo): bool
    {
        return hash_file('sha256', $this->fileInfo->getRealPath()) === hash_file('sha256', $fileInfo->getRealPath());
    }

    /**
     * Delete the file.
     * @return bool true on success, false on failure
     */
    public function delete(): bool
    {
        return unlink($this->fileInfo->getRealPath());
    }
}
