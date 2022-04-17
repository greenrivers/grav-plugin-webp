<?php
/**
 * @author Greenrivers
 * @copyright Copyright (c) 2022 Greenrivers
 * @package Grav\Plugin\Webp
 */

namespace Grav\Plugin\Webp\Console;

use Grav\Common\Grav;
use Grav\Common\Language\Language;
use Grav\Console\ConsoleCommand as BaseConsoleCommand;

class ConsoleCommand extends BaseConsoleCommand
{
    protected const MESSAGE_TYPE_SUCCESS = 'success';
    protected const MESSAGE_TYPE_INFO = 'info';
    protected const MESSAGE_TYPE_ERROR = 'error';

    /**
     * @return Language
     */
    protected static function getLanguage(): Language
    {
        $grav = Grav::instance();
        return $grav['language'];
    }

    /**
     * @param string $message
     * @param string $messageType
     * @return void
     */
    protected function printMessage(string $message, string $messageType): void
    {
        switch ($messageType) {
            case self::MESSAGE_TYPE_SUCCESS:
                $this->output->success($message);
                break;
            case self::MESSAGE_TYPE_INFO:
                $this->info($message);
                break;
            case self::MESSAGE_TYPE_ERROR:
                $this->output->error($message);
                break;
        }
    }

    /**
     * @param string $message
     * @return void
     */
    protected function info(string $message): void
    {
        $this->output->block($message, 'INFO', 'fg=black;bg=blue', ' ', true);
    }
}
