<?php

namespace PhpDevCommunity\FileSystem\Tools;

use PhpDevCommunity\FileSystem\FileInfo;

final class FileSplitter
{
    private FileInfo $fileInfo;
    private string $directory;
    public function __construct(FileInfo $fileInfo, string $directory = null)
    {
        $this->fileInfo = $fileInfo;
        if ($directory === null) {
            $directory = dirname($fileInfo->getRealPath());
        }
        $this->directory = rtrim($directory, DIRECTORY_SEPARATOR);

    }

    public function splitMb(int $mbSize): array
    {
        return $this->split($mbSize * 1024 * 1024);
    }

    public function splitKb(int $kbSize): array
    {
        return $this->split($kbSize * 1024);
    }

    /**
     * @throws \RuntimeException if the file is not readable
     */
    public function split(int $chunkSize): array
    {
        $filePath = $this->fileInfo->getRealPath();
        $filename = basename($filePath);
        if (!is_readable($filePath)) {
            throw new \RuntimeException("File is not readable: $filePath");
        }

        $parts = [];
        $file = new \SplFileObject($filePath, 'rb');
        $index = 0;

        while (!$file->eof()) {
            $chunk = $file->fread($chunkSize);
            if ($chunk === '') {
                break;
            }
            $partPath = $this->directory .DIRECTORY_SEPARATOR . $filename . '.part' . $index++;
            $partFile = new \SplFileObject($partPath, 'wb');
            $partFile->fwrite($chunk);
            $parts[] = new FileInfo($partPath);
        }
        return $parts;
    }
}
