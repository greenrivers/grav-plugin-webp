<?php
/**
 * @author Greenrivers
 * @copyright Copyright (c) 2022 Greenrivers
 * @package Grav\Plugin\Webp
 */

namespace Grav\Plugin\Webp\Helper;

use Exception;
use Grav\Plugin\Webp\Utils\Logger;
use Symfony\Component\Finder\SplFileInfo;

class Image
{
    private const WEBP_EXTENSION = 'webp';
    private const WEBP_DIR_PREFIX = 'user/webp';

    private const EXIF_ROTATION_0 = 1;
    private const EXIF_MIRROR_0 = 2;
    private const EXIF_ROTATION_90 = 6;
    private const EXIF_MIRROR_90 = 5;
    private const EXIF_ROTATION_180 = 3;
    private const EXIF_MIRROR_180 = 4;
    private const EXIF_ROTATION_270 = 8;
    private const EXIF_MIRROR_270 = 7;

    private const JPG_TYPE = 2;
    private const PNG_TYPE = 3;

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

    /**
     * Auto-rotate image based on EXIF data
     *
     * @param string $imagePath
     * @param int $quality
     * @return void
     */
    public static function adjustImageOrientation(string $imagePath, int $quality = 100): void
    {
        try {
            $exifData = @exif_read_data($imagePath);
        } catch (Exception $e) {
            Logger::addErrorMessage($e->getMessage());
        }

        $orientation = ($exifData && array_key_exists('Orientation', $exifData)) ?
            $exifData['Orientation'] : false;

        if ($orientation && $orientation !== self::EXIF_ROTATION_0) {
            switch (@exif_imagetype($imagePath)) {
                case self::JPG_TYPE:
                    $image = @imageCreateFromJpeg($imagePath);
                    break;
                case self::PNG_TYPE:
                    $image = @imageCreateFromPng($imagePath);
                    break;
                default:
                    $image = @imagecreatefromjpeg($imagePath);
            }

            if ($image) {
                $mirror = in_array(
                    $orientation,
                    [
                        self::EXIF_MIRROR_0,
                        self::EXIF_MIRROR_90,
                        self::EXIF_MIRROR_180,
                        self::EXIF_MIRROR_270
                    ],
                    true
                );

                $angle = 0;
                switch ($orientation) {
                    case self::EXIF_ROTATION_180:
                    case self::EXIF_MIRROR_180:
                        $angle = 180;
                        break;
                    case self::EXIF_ROTATION_90:
                    case self::EXIF_MIRROR_90:
                        $angle = 270;
                        break;
                    case self::EXIF_ROTATION_270:
                    case self::EXIF_MIRROR_270:
                        $angle = 90;
                        break;
                }

                $backgroundColor = 0;

                if ($angle) {
                    $image = @imagerotate($image, $angle, $backgroundColor);
                }

                if ($mirror) {
                    $image = @imageflip($image, IMG_FLIP_HORIZONTAL);
                }

                switch (@exif_imagetype($imagePath)) {
                    case self::JPG_TYPE:
                        @imagejpeg($image, $imagePath, $quality);
                        break;
                    case self::PNG_TYPE:
                        @imagepng($image, $imagePath, $quality);
                        break;
                    default:
                        @imagejpeg($image, $imagePath, $quality);
                }
            }
        }
    }
}
