<?php
/**
 * @author Greenrivers
 * @copyright Copyright (c) 2022 Greenrivers
 * @package Grav\Plugin\Webp
 */

namespace Grav\Plugin\Webp\Utils;

use Grav\Common\Grav;
use Psr\Log\LoggerInterface;

class Logger
{
    /**
     * @param string $message
     * @return void
     */
    public static function addInfoMessage(string $message): void
    {
        $logger = self::getLogger();
        $logger->info($message);
    }

    /**
     * @param string $message
     * @return void
     */
    public static function addErrorMessage(string $message): void
    {
        $logger = self::getLogger();
        $logger->error($message);
    }

    /**
     * @return LoggerInterface
     */
    private static function getLogger(): LoggerInterface
    {
        $grav = Grav::instance();
        return $grav['log'];
    }
}
