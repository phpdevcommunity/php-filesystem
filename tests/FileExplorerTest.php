<?php

namespace Test\PhpDevCommunity\FileSystem;

use PhpDevCommunity\FileSystem\Tools\FileExplorer;
use PHPUnit\Framework\TestCase;

class FileExplorerTest extends TestCase
{

    public function testListAll()
    {
        $explorer = new FileExplorer(__DIR__ . '/resources/syncsource');

        $files = $explorer->listAll(true);
        $this->assertIsArray($files);
        $this->assertCount(3, $files);

        $file1 = $files[0];
        $this->assertIsArray($file1);
        $this->assertArrayHasKey('path', $file1);
        $this->assertArrayHasKey('name', $file1);
        $this->assertArrayHasKey('is_directory', $file1);
        $this->assertArrayHasKey('size', $file1);
        $this->assertArrayHasKey('modified_time', $file1);
        $this->assertFalse($file1['is_directory']);

        $file2 = $files[1];
        $this->assertIsArray($file2);
        $this->assertArrayHasKey('path', $file2);
        $this->assertArrayHasKey('name', $file2);
        $this->assertArrayHasKey('is_directory', $file2);
        $this->assertArrayHasKey('size', $file2);
        $this->assertArrayHasKey('modified_time', $file2);
        $this->assertFalse($file2['is_directory']);

        $file3 = $files[2];
        $this->assertIsArray($file3);
        $this->assertArrayHasKey('path', $file3);
        $this->assertArrayHasKey('name', $file3);
        $this->assertArrayHasKey('is_directory', $file3);
        $this->assertArrayHasKey('size', $file3);
        $this->assertArrayHasKey('modified_time', $file3);
        $this->assertArrayHasKey('files', $file3);
        $this->assertTrue($file3['is_directory']);

        $this->assertIsArray($file3['files']);
        $this->assertCount(2, $file3['files']);

    }

    public function testSearchByPattern()
    {
        $explorer = new FileExplorer(__DIR__ . '/resources');
        $files = $explorer->searchByPattern('*.html', true);

        $this->assertIsArray($files);
        $this->assertCount(2, $files);

        $files = $explorer->searchByPattern('*.txt', true);

        $this->assertIsArray($files);
        $this->assertCount(4, $files);

        $files = $explorer->searchByPattern('*.txt');

        $this->assertIsArray($files);
        $this->assertCount(2, $files);

    }

    public function testSearchByExtension()
    {
        $explorer = new FileExplorer(__DIR__ . '/resources');
        $files = $explorer->searchByExtension('html', true);
        $this->assertIsArray($files);
        $this->assertCount(2, $files);
    }
}
