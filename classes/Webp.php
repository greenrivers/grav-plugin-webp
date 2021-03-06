<?php
/**
 * @author Greenrivers Team
 * @copyright Copyright (c) 2021 Greenrivers
 * @package Grav\Plugin
 */

namespace Grav\Plugin\Webp;

use Grav\Common\Filesystem\Folder;
use Grav\Plugin\Webp\Helper\Converter;
use Grav\Plugin\Webp\Helper\Image;
use Grav\Plugin\Webp\Helper\View;
use Symfony\Component\Finder\Finder;

class Webp
{
    /** @var Finder */
    private $finder;

    /** @var Image */
    private $image;

    /** @var Converter */
    private $converter;

    /** @var View */
    private $view;

    /**
     * Webp constructor.
     */
    public function __construct()
    {
        $this->finder = new Finder();
        $this->image = new Image();
        $this->converter = new Converter();
        $this->view = new View();
    }

    /**
     * @return array
     */
    public function getImagesToConversion(): array
    {
        $imagesToConversion = [];
        $folders = ['user/images', 'user/pages', 'user/themes'];
        $extensions = ['*.jpg', '*.jpeg', '*.png'];

        foreach ($folders as $folder) {
            if (!file_exists($folder)) {
                Folder::create($folder);
            }
        }

        $images = $this->finder
            ->ignoreDotFiles(false)
            ->files()
            ->in($folders)
            ->exclude('node_modules')
            ->name($extensions);

        foreach ($images as $image) {
            $webpPath = $this->image->getWebpPath(
                $image->getPathInfo()->getPathname(),
                $image->getFilenameWithoutExtension()
            );

            if (!file_exists($webpPath)) {
                $imagesToConversion[] = [
                    'extension' => $image->getExtension(),
                    'pathname' => $image->getPathname(),
                    'pathinfo' => [
                        'pathname' => $image->getPathInfo()->getPathname()
                    ],
                    'filenamewithoutextension' => $image->getFilenameWithoutExtension()
                ];
            }
        }

        return $imagesToConversion;
    }

    /**
     * @param array $totalImages
     * @param int $convertedImagesCount
     * @return int
     */
    public function process(array $totalImages, int $convertedImagesCount): int
    {
        $index = $convertedImagesCount;

        foreach ($totalImages as $key => $image) {
            if ($key === $index && $this->converter->convert($image)) {
                $convertedImagesCount++;
            }
        }

        return $convertedImagesCount;
    }

    /**
     * @param string $imagePath
     * @return string
     */
    public function changeImagePath(string $imagePath): string
    {
        return $this->view->changeImagePath($imagePath);
    }

    /**
     * @param string $imagePath
     * @return string
     */
    public function getImageExtension(string $imagePath): string
    {
        return $this->view->getImageExtension($imagePath);
    }
}
