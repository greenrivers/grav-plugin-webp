<?php
/**
 * @author Greenrivers
 * @copyright Copyright (c) 2022 Greenrivers
 * @package Grav\Plugin\Webp
 */

namespace Tests\Unit\Helper;

use Codeception\Stub\Expected;
use Codeception\Test\Unit;
use Grav\Plugin\Webp\Helper\Image;
use Symfony\Component\Finder\SplFileInfo;

class ImageTest extends Unit
{
    /** @var Image */
    private $image;

    /**
     * @inheritDoc
     */
    protected function _before()
    {
        $this->image = new Image();
    }

    /**
     * @covers Image::getImageData
     */
    public function testGetImageData()
    {
        $imageMock = $this->make(SplFileInfo::class, [
            'getExtension' => Expected::once('jpg'),
            'getPathname' => Expected::once('user/themes/quark/thumbnail.jpg'),
            'getPathInfo' => Expected::once(
                $this->make(SplFileInfo::class, [
                    'getPathName' => Expected::once('user/themes/quark')
                ])
            ),
            'getFilenameWithoutExtension' => Expected::once('thumbnail')
        ]);

        $result = $this->image->getImageData($imageMock);

        $this->assertArrayHasKey('extension', $result);
        $this->assertArrayHasKey('pathname', $result);
        $this->assertArrayHasKey('pathinfo', $result);
        $this->assertArrayHasKey('pathname', $result['pathinfo']);
        $this->assertArrayHasKey('filenamewithoutextension', $result);

        $this->assertEquals('jpg', $result['extension']);
        $this->assertEquals('user/themes/quark/thumbnail.jpg', $result['pathname']);
        $this->assertEquals('user/themes/quark', $result['pathinfo']['pathname']);
        $this->assertEquals('thumbnail', $result['filenamewithoutextension']);
    }

    /**
     * @covers Image::getImageData
     */
    public function testGetImageDataFromCli()
    {
        $imageMock = $this->make(SplFileInfo::class, [
            'getExtension' => Expected::once('jpg'),
            'getRelativePathname' => Expected::once('user/themes/quark/thumbnail.jpg'),
            'getRelativePath' => Expected::once('user/themes/quark'),
            'getFilenameWithoutExtension' => Expected::once('thumbnail')
        ]);

        $result = $this->image->getImageData($imageMock, true);

        $this->assertArrayHasKey('extension', $result);
        $this->assertArrayHasKey('pathname', $result);
        $this->assertArrayHasKey('pathinfo', $result);
        $this->assertArrayHasKey('pathname', $result['pathinfo']);
        $this->assertArrayHasKey('filenamewithoutextension', $result);

        $this->assertEquals('jpg', $result['extension']);
        $this->assertEquals('user/themes/quark/thumbnail.jpg', $result['pathname']);
        $this->assertEquals('user/themes/quark', $result['pathinfo']['pathname']);
        $this->assertEquals('thumbnail', $result['filenamewithoutextension']);
    }

    /**
     * @covers Image::getWebpDir
     */
    public function testGetWebpPath()
    {
        $pathname = 'user/themes/quark';
        $filenameWithoutExtension = 'thumbnail';

        $result = $this->image->getWebpPath($pathname, $filenameWithoutExtension);

        $this->assertEquals('user/webp/user/themes/quark/thumbnail.webp', $result);
    }

    /**
     * @covers Image::getWebpDir
     */
    public function testGetWebpDir()
    {
        $pathname = 'user/themes/quark';

        $result = $this->image->getWebpDir($pathname);

        $this->assertEquals('user/webp/user/themes/quark', $result);
    }
}
