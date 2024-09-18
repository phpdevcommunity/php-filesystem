<?php

namespace Test\PhpDevCommunity\FileSystem;

use FilesystemIterator;
use PhpDevCommunity\FileSystem\Tools\FileSynchronizer;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FileSynchronizerTest extends TestCase
{

    public function testSync()
    {
        $targetDir = __DIR__ . '/target';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $sync = new FileSynchronizer(__DIR__ . '/resources', $targetDir, function (array $info) {
            $this->assertArrayHasKey('action', $info);
            $this->assertArrayHasKey('source', $info);
            $this->assertArrayHasKey('target', $info);
        });
        $sync->sync(true);

        $objects = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($targetDir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        $this->assertCount(8, iterator_to_array($objects));

        foreach ($objects as $object) {
            if ($object->isFile()) {
                unlink($object->getPathname());
            }
            elseif ($object->isDir()) {
                rmdir($object->getPathname());
            }
        }

        rmdir($targetDir);
    }

}
