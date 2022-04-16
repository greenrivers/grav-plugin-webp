<?php
/**
 * @author Greenrivers
 * @copyright Copyright (c) 2022 Greenrivers
 * @package Grav\Plugin\Webp
 */

namespace Grav\Plugin\Webp\Helper;

use Symfony\Component\Finder\SplFileInfo;

class Image
{
    private const WEBP_EXTENSION = 'webp';
    private const WEBP_DIR_PREFIX = 'user/webp';

    /**
     * @param SplFileInfo $image
     * @param bool $cli
     * @return array
     */
    public function getImageData(SplFileInfo $image, bool $cli = false): array
    {
        return [
            'extension' => $image->getExtension(),
            'pathname' => $cli ? $image->getRelativePathname() : $image->getPathname(),
            'pathinfo' => [
                'pathname' => $cli ? $image->getRelativePath() : $image->getPathInfo()->getPathname()
            ],
            'filenamewithoutextension' => $image->getFilenameWithoutExtension()
        ];
    }

    /**
     * @param SplFileInfo $image
     * @return bool
     */
    public function isWebp(SplFileInfo $image): bool
    {
        return $image->getExtension() === self::WEBP_EXTENSION &&
            exif_imagetype($image->getPathname()) === IMAGETYPE_WEBP;
    }

    /**
     * @param string $pathname
     * @param string $filenameWithoutExtension
     * @return string
     */
    public function getWebpPath(string $pathname, string $filenameWithoutExtension): string
    {
        $webpDir = $this->getWebpDir($pathname);
        return $webpDir . DIRECTORY_SEPARATOR . $filenameWithoutExtension . '.' . self::WEBP_EXTENSION;
    }

    /**
     * @param string $pathname
     * @return string
     */
    public function getWebpDir(string $pathname): string
    {
        return self::WEBP_DIR_PREFIX . DIRECTORY_SEPARATOR . $pathname;
    }
}
