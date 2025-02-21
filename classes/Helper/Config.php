<?php

namespace Grav\Plugin\Webp\Helper;

use Grav\Common\Config\Config as GravConfig;
use Grav\Common\Grav;

class Config
{
    private const PLUGINS_WEBP_ENABLED_PATH = 'plugins.webp.enabled';
    private const PLUGINS_WEBP_ORIGINAL_PATH_PATH = 'plugins.webp.original_path';
    private const PLUGINS_WEBP_QUALITY_PATH = 'plugins.webp.quality';

    /**
     * @return bool
     */
    public static function isEnabled(): bool
    {
        return self::getConfig()->get(self::PLUGINS_WEBP_ENABLED_PATH);
    }

    /**
     * @return bool
     */
    public static function isOriginalPath(): bool
    {
        return self::getConfig()->get(self::PLUGINS_WEBP_ORIGINAL_PATH_PATH);
    }

    /**
     * @return int
     */
    public static function getQuality(): int
    {
        return self::getConfig()->get(self::PLUGINS_WEBP_QUALITY_PATH);
    }

    /**
     * @return GravConfig
     */
    private static function getConfig(): GravConfig
    {
        return Grav::instance()['config'];
    }
}
