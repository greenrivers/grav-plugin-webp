<?php
/**
 * @author Greenrivers Team
 * @copyright Copyright (c) 2021 Greenrivers
 * @package Grav\Plugin
 */

namespace Grav\Plugin\Webp\Helper;

class Image
{
    const WEBP_EXTENSION = 'webp';
    const WEBP_DIR_PREFIX = 'user/webp';

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
