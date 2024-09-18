<?php

namespace PhpDevCommunity\FileSystem\Tools;

use DirectoryIterator;
use InvalidArgumentException;

final class FileExplorer
{
    private string $directory;

    public function __construct(string $directory)
    {
        if (!is_dir($directory)) {
            throw new InvalidArgumentException('The path provided is not a valid directory: ' . $directory);
        }

        $this->directory = rtrim($directory, DIRECTORY_SEPARATOR);
    }

    public function listAll(bool $recursive = false): array
    {
        $files = [];
        $iterator = new DirectoryIterator($this->directory);

        foreach ($iterator as $fileInfo) {
            if (!$fileInfo->isDot()) {
                if ($recursive && $fileInfo->isDir()) {
                    $files[] = self::SplInfoToArray($fileInfo, (new FileExplorer($fileInfo->getRealPath()))->listAll(true));
                    continue;
                }
                $files[] = self::SplInfoToArray($fileInfo);
            }
        }

        return $files;
    }

    public function searchByPattern(string $pattern, bool $recursive = false): array
    {
        $regexPattern = '/' . str_replace(['*', '?'], ['.*', '.'], preg_quote($pattern, '/')) . '/i';

        $dir = new \RecursiveDirectoryIterator($this->directory);
        if ($recursive) {
            $iterator = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::SELF_FIRST);
        } else {
            $iterator = new \IteratorIterator($dir);
        }
        $iterator = new \RegexIterator($iterator, $regexPattern, \RegexIterator::MATCH);

        $files = [];
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isFile()) {
                $files[] = self::SplInfoToArray(new \SplFileInfo($fileInfo));
            }
        }
        return $files;
    }

    public function searchByExtension(string $extension,bool $recursive = false): array
    {
        return $this->searchByPattern("*.$extension", $recursive);
    }

    private static function SplInfoToArray(\SplFileInfo $fileInfo, array $files = []): array
    {
        return [
            'path' => $fileInfo->getPathname(),
            'name' => $fileInfo->getFilename(),
            'is_directory' => $fileInfo->isDir(),
            'size' => $fileInfo->isFile() ? $fileInfo->getSize() : null,
            'size_in_kb' => $fileInfo->isFile() ? round($fileInfo->getSize() / 1024, 2) : null,
            'size_in_mb' => $fileInfo->isFile() ? round($fileInfo->getSize() / 1024 / 1024, 2) : null,
            'modified_time' => date('Y-m-d H:i:s', $fileInfo->getMTime()),
            'files' => $files
        ];
    }
}
