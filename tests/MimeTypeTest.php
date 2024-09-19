<?php

namespace Test\PhpDevCommunity\FileSystem;

use PhpDevCommunity\FileSystem\Mime\MimeType;
use PHPUnit\Framework\TestCase;

class MimeTypeTest extends TestCase
{

    public function testGetMimeTypeByExtension()
    {
        // Test when extension exists
        $this->assertEquals('text/x-php', MimeType::getMimeTypeByExtension('php'));

        $this->assertNull(MimeType::getMimeTypeByExtension('unknown_extension'));
    }

    public function testGetExtensionByMimeType()
    {
        $this->assertEquals('php', MimeType::getExtensionByMimeType('text/x-php'));

        $this->assertNull(MimeType::getExtensionByMimeType('unknown_mime_type'));
    }

}
