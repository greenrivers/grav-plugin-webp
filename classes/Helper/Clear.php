<?php
/**
 * @author Greenrivers
 * @copyright Copyright (c) 2022 Greenrivers
 * @package Grav\Plugin\Webp
 */

namespace Grav\Plugin\Webp\Helper;

use Symfony\Component\Filesystem\Filesystem;

class Clear
{
    /** @var Filesystem */
    private $filesystem;

    /**
     * Clear constructor.
     */
    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    /**
     * @param array $image
     * @return bool
     */
    public function removeImage(array $image): bool
    {
        $webpPath = $image['pathname'];

        $this->filesystem->remove($webpPath);

        return !$this->filesystem->exists($webpPath);
    }
}
