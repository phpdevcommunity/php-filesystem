<?php

namespace PhpDevCommunity\tests;

use PhpDevCommunity\FileSystem\FileInfo;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use SplFileInfo;
use SplFileObject;

class FileInfoTest extends TestCase
{

    public function testGetSplFileInfo()
    {
        $file = new FileInfo(__FILE__);
        $this->assertInstanceOf(SplFileInfo::class, $file->getSplFileInfo());
    }

    public function testGetRealPath()
    {
        $fileInfo = new FileInfo(__FILE__);

        $this->assertIsString($fileInfo->getRealPath());
    }

    public function testGetSize()
    {
        $fileInfo = new FileInfo(__FILE__);
        $this->assertIsInt($fileInfo->getSize());
    }

    public function testGetMimeType()
    {
        $fileInfo = new FileInfo(__FILE__);
        $this->assertEquals('text/x-php', $fileInfo->getMimeType());
    }

    public function testGetExtension()
    {
        $fileInfo = new FileInfo(__FILE__);
        $this->assertEquals('php', $fileInfo->getExtension());
    }

    public function testOpenFile()
    {
        $fileInfo = new FileInfo(__FILE__);
        $this->assertInstanceOf(SplFileObject::class, $fileInfo->openFile());
    }

    public function testOpenFileInvalidMode()
    {
        $this->expectException(RuntimeException::class);
        $fileInfo = new FileInfo(__FILE__);
        $fileInfo->openFile('invalid');
    }

    public function testDirectory()
    {
        $this->expectException(RuntimeException::class);
        new FileInfo(__DIR__);
    }

    public function testDelete()
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'tmp__');
        $fileInfo = new FileInfo($tmpFile);
        $this->assertTrue($fileInfo->delete());
        $this->assertFalse(file_exists($tmpFile));
    }

    public function testGetMetadata()
    {
        $filePath = __FILE__;
        $fileSize = filesize($filePath);
        $mimeType = 'text/x-php';
        $extension = 'php';
        $basename = basename($filePath);
        $lastModified = filemtime($filePath);
        $creationDate = filectime($filePath);

        $fileInfo = new FileInfo($filePath);
        $metadata = $fileInfo->getMetadata();
        $this->assertEquals([
            'path' => $filePath,
            'size' => $fileSize,
            'size_in_kb' => round($fileSize / 1024, 2),
            'size_in_mb' => round($fileSize / 1024 / 1024, 2),
            'mime_type' => $mimeType,
            'extension' => $extension,
            'basename' => $basename,
            'last_modified' => date('Y-m-d H:i:s', $lastModified),
            'creation_date' => date('Y-m-d H:i:s', $creationDate)
        ], $metadata);
    }

    public function testCompareWith()
    {
        $filePath1 = __FILE__;
        $filePath2 = __FILE__;

        $fileInfo1 = new FileInfo($filePath1);
        $fileInfo2 = new FileInfo($filePath2);

        $result = $fileInfo1->compareWith($fileInfo2);
        $this->assertTrue($result);

        $filePath1 = __DIR__.'/resources/syncsource/file1.txt';
        $filePath2 = __DIR__.'/resources/syncsource/file2.txt';
        $fileInfo1 = new FileInfo($filePath1);
        $fileInfo2 = new FileInfo($filePath2);

        $result = $fileInfo1->compareWith($fileInfo2);
        $this->assertFalse($result);
    }
}
