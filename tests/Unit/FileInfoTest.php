<?php

namespace Tests\Unit;

use Tests\TestCase;
use Weirdo\Helper\Support\FileInfo;

class FileInfoTest extends TestCase
{
    public function testFileInfo()
    {
        /** \Weirdo\Helper\Support\FileInfo $base */
        $base = new FileInfo('https://fotos.perfil.com/2020/07/25/trim/960/540/0507nasaargentina-991945.jpg?format=webp');
        /** @var array $info */
        $info = $base->summary();
        $this->assertArrayHasKey('filesize', $info);
        $this->assertArrayHasKey('filename', $info);
        $this->assertArrayHasKey('filenamepath', $info);
        $this->assertArrayHasKey('fileformat', $info);
        $this->assertArrayHasKey('mime_type', $info);
        $this->assertArrayHasKey('width', $info);
        $this->assertArrayHasKey('height', $info);
        $this->assertArrayHasKey('fullname', $info);
    }
}
