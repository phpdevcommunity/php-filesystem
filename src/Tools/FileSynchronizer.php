<?php

namespace PhpDevCommunity\FileSystem\Tools;

final class FileSynchronizer
{
    private string $sourceDir;
    private string $targetDir;
    private ?\Closure $log;

    public function __construct(string $sourceDir, string $targetDir, ?\Closure $log = null)
    {
        if (!is_dir($sourceDir) || !is_dir($targetDir)) {
            throw new \InvalidArgumentException('Both source and target must be valid directories.');
        }
        $this->sourceDir = rtrim($sourceDir, DIRECTORY_SEPARATOR);
        $this->targetDir = rtrim($targetDir, DIRECTORY_SEPARATOR);
        $this->log = $log;
    }

    public function sync(bool $recursive = false): void
    {
        $explorer = new FileExplorer($this->sourceDir);
        $files = $explorer->listAll($recursive);
        foreach ($files as $file) {
            $targetPath = str_replace( $this->sourceDir, $this->targetDir, $file['path']);
            if ($file['is_directory']) {
                if ($recursive) {
                    if (!is_dir($targetPath)) {
                        mkdir($targetPath);
                    }
                    $sync = new FileSynchronizer($file['path'], $targetPath, $this->log);
                    $sync->sync(true);
                }
                continue;
            }

            $this->copyFile($targetPath,$file);
        }
    }

    private function copyFile(string $targetPath, array $file): void
    {
        if (!file_exists($targetPath) || filemtime($file['path']) > filemtime($targetPath)) {
            copy($file['path'], $targetPath);
        }
        $this->log([
            'action' => 'copy',
            'source' => $file['path'],
            'target' => $targetPath
        ]);
    }

    private function log(array $info): void
    {
        if ($this->log) {
            $callback = $this->log;
            $callback($info);
        }
    }
}
