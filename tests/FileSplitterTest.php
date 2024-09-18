<?php

namespace PhpDevCommunity\tests;

use PhpDevCommunity\FileSystem\FileInfo;
use PhpDevCommunity\FileSystem\Tools\FileSplitter;
use PHPUnit\Framework\TestCase;

class FileSplitterTest extends TestCase
{

    public function testSplitMb()
    {
        $fileInfo = new FileInfo(dirname(__FILE__).'/resources/file_2MB.txt');
        $splitter = new FileSplitter($fileInfo);
        $files = $splitter->splitMb(1);
        $this->assertCount(2, $files);

        foreach ($files as $file) {
            $this->assertInstanceOf(FileInfo::class, $file);
            $file->delete();
        }
    }

    public function testSplitNoSameSize()
    {
        $fileInfo = new FileInfo(dirname(__FILE__).'/resources/file_2MB.txt');
        $splitter = new FileSplitter($fileInfo);
        $files = $splitter->splitKb(200);
        $this->assertCount(11, $files);

        foreach ($files as $file) {
            $this->assertInstanceOf(FileInfo::class, $file);
            $file->delete();
        }
    }

    public function testSplitKb()
    {
        $fileInfo = new FileInfo(dirname(__FILE__).'/resources/file_1MB.txt');
        $splitter = new FileSplitter($fileInfo);
        $files = $splitter->splitKb(512);
        $this->assertCount(2, $files);

        foreach ($files as $file) {
            $this->assertInstanceOf(FileInfo::class, $file);
            $file->delete();
        }

        $fileInfo = new FileInfo(dirname(__FILE__).'/resources/file_1MB.txt');
        $splitter = new FileSplitter($fileInfo);
        $files = $splitter->splitKb(500);
        $this->assertCount(3, $files);

        foreach ($files as $file) {
            $this->assertInstanceOf(FileInfo::class, $file);
            $file->delete();
        }
    }


}
