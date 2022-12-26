<?php
/**
 * @author Greenrivers
 * @copyright Copyright (c) 2022 Greenrivers
 * @package Grav\Plugin\Webp
 */

namespace Grav\Plugin\Webp\Helper;

use Grav\Common\Filesystem\Folder;

class Converter
{
    private const PNG_EXTENSION = 'png';

    /** @var Image */
    private $image;

    /**
     * Converter constructor.
     */
    public function __construct()
    {
        $this->image = new Image();
    }

    /**
     * @param array $image
     * @param int $quality
     * @return bool
     */
    public function convert(array $image, int $quality): bool
    {
        $extension = $image['extension'];
        $imagePath = $image['pathname'];
        $pathname = $image['pathinfo']['pathname'];
        $filenameWithoutExtension = $image['filenamewithoutextension'];

        $webpDir = $this->image->getWebpDir($pathname);
        $webpPath = $this->image->getWebpPath($pathname, $filenameWithoutExtension);

        Image::adjustImageOrientation($imagePath);

        if ($extension === self::PNG_EXTENSION) {
            $image = @imagecreatefrompng($imagePath);
            @imagepalettetotruecolor($image);
            @imagealphablending($image, true);
            @imagesavealpha($image, true);
        } else {
            $image = @imagecreatefromstring(file_get_contents($imagePath));
        }

        if ($image) {
            ob_start();

            if (!file_exists($webpDir)) {
                Folder::create($webpDir);
            }

            $result = @imagewebp($image, $webpPath, $quality);
            ob_get_clean();
        } else {
            $result = false;
        }

        return $result;
    }
}
