<?php
/**
 * @author Greenrivers
 * @copyright Copyright (c) 2022 Greenrivers
 * @package Grav\Plugin\Webp
 */

namespace Grav\Plugin\Webp;

use Grav\Common\Filesystem\Folder;
use Grav\Plugin\Webp\Helper\Clear;
use Grav\Plugin\Webp\Helper\Converter;
use Grav\Plugin\Webp\Helper\File;
use Grav\Plugin\Webp\Helper\Image;
use Grav\Plugin\Webp\Helper\View;
use Symfony\Component\Finder\Finder;

class Webp
{
    /** @var Finder */
    private $finder;

    /** @var File */
    private $file;

    /** @var Image */
    private $image;

    /** @var Converter */
    private $converter;

    /** @var Clear */
    private $clear;

    /** @var View */
    private $view;

    /**
     * Webp constructor.
     */
    public function __construct()
    {
        $this->finder = new Finder();
        $this->file = new File();
        $this->image = new Image();
        $this->converter = new Converter();
        $this->clear = new Clear();
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
            if (!$this->file->fileExists($folder)) {
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

            if (!$this->file->fileExists($webpPath)) {
                $imagesToConversion[] = $this->image->getImageData($image);
            }
        }

        return $imagesToConversion;
    }

    /**
     * @return array
     */
    public function getImagesToRemove(): array
    {
        $imagesToRemove = [];
        $folder = ['user/webp'];
        $extensions = ['*.webp'];

        $images = $this->finder
            ->ignoreDotFiles(false)
            ->files()
            ->in($folder)
            ->exclude('node_modules')
            ->name($extensions);

        foreach ($images as $image) {
            $imagesToRemove[] = $this->image->getImageData($image);
        }

        return $imagesToRemove;
    }

    /**
     * @param array $totalImages
     * @param int $convertedImagesCount
     * @param int $quality
     * @return int
     */
    public function process(array $totalImages, int $convertedImagesCount, int $quality): int
    {
        $index = $convertedImagesCount;

        foreach ($totalImages as $key => $image) {
            if ($key === $index && $this->converter->convert($image, $quality)) {
                $convertedImagesCount++;
            }
        }

        return $convertedImagesCount;
    }

    /**
     * @param array $webpImages
     * @param int $removedImagesCount
     * @return int
     */
    public function clearAll(array $webpImages, int $removedImagesCount): int
    {
        $index = $removedImagesCount;

        foreach ($webpImages as $key => $image) {
            if ($key === $index && $this->clear->removeImage($image)) {
                $removedImagesCount++;
            }
        }

        return $removedImagesCount;
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
