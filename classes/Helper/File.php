<?php
/**
 * @author Greenrivers
 * @copyright Copyright (c) 2022 Greenrivers
 * @package Grav\Plugin\Webp
 */

namespace Grav\Plugin\Webp\Helper;

use Exception;
use Grav\Plugin\Webp\Utils\Logger;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class File
{
    /** @var Filesystem */
    private $filesystem;

    /** @var Finder */
    private $finder;

    /**
     * File constructor.
     */
    public function __construct()
    {
        $this->filesystem = new Filesystem();
        $this->finder = new Finder();
    }

    /**
     * @param string $filename
     * @return bool
     */
    public function fileExists(string $filename): bool
    {
        return $this->filesystem->exists($filename);
    }

    /**
     * @param string $filename
     * @return SplFileInfo|null
     */
    public function getFile(string $filename): ?SplFileInfo
    {
        $file = null;
        $filename = $this->stringStartsWith($filename, ROOT_DIR) ? $filename : ROOT_DIR . $filename;

        $extensions = ['*.jpg', '*.jpeg', '*.png', '*.webp'];
        $finder = $this->finder
            ->in(ROOT_DIR)
            ->files()
            ->name($extensions)
            ->filter(static function (SplFileInfo $file) use ($filename) {
                return $file->isFile() && $file->getPathname() === $filename;
            });

        if ($finder->hasResults()) {
            try {
                $iterator = $finder->getIterator();
                $iterator->rewind();
                $file = $iterator->current();
            } catch (Exception $e) {
                Logger::addErrorMessage($e->getMessage());
            }
        }

        return $file;
    }

    /**
     * @param string $text
     * @param string $startsWith
     * @return bool
     */
    private function stringStartsWith(string $text, string $startsWith): bool
    {
        return strpos($text, $startsWith) === 0;
    }
}
