<?php
/**
 * @author Greenrivers
 * @copyright Copyright (c) 2022 Greenrivers
 * @package Grav\Plugin\Webp
 */

namespace Grav\Plugin\Webp\Helper;

use SplFileInfo;

class View
{
    /** @var Image */
    private $image;

    /**
     * View constructor.
     */
    public function __construct()
    {
        $this->image = new Image();
    }

    /**
     * @param string $imagePath
     * @return string
     */
    public function changeImagePath(string $imagePath): string
    {
        $image = new SplFileInfo($imagePath);

        $pathname = $image->getPathInfo()->getPathname();
        $imageName = substr(strrchr($imagePath, DIRECTORY_SEPARATOR), 1);
        $imageNameWithoutExtension = substr($imageName, 0, strrpos($imageName, '.'));

        $webpPath = $this->image->getWebpPath(Config::isOriginalPath(), $pathname, $imageNameWithoutExtension);

        return file_exists($webpPath) ? (DIRECTORY_SEPARATOR . $webpPath) : $imagePath;
    }

    /**
     * @param string $imagePath
     * @return string
     */
    public function getImageExtension(string $imagePath): string
    {
        return (new SplFileInfo($imagePath))->getExtension();
    }
}
