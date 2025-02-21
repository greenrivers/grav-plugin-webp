<?php
/**
 * @author Greenrivers
 * @copyright Copyright (c) 2022 Greenrivers
 * @package Grav\Plugin\Webp
 */

namespace Grav\Plugin\Console;

use Grav\Common\Language\Language;
use Grav\Plugin\Webp\Console\ConsoleCommand;
use Grav\Plugin\Webp\Helper\Config;
use Grav\Plugin\Webp\Helper\Converter;
use Grav\Plugin\Webp\Helper\File;
use Grav\Plugin\Webp\Helper\Image;
use Grav\Plugin\Webp\Webp;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputOption;

class ConvertCommand extends ConsoleCommand
{
    private const PATH_OPTION_NAME = 'path';
    private const ORIGINAL_PATH_OPTION_NAME = 'original_path';
    private const QUALITY_OPTION_NAME = 'quality';
    private const OVERRIDE_OPTION_NAME = 'overwrite';

    private const ALL_OPTION_NAME = 'all';

    private const MAX_QUALITY = 100;

    /** @var File */
    private $file;

    /** @var Image */
    private $image;

    /** @var Converter */
    private $converter;

    /** @var Webp */
    private $webp;

    /**
     * ConvertCommand constructor.
     * @param $name
     */
    public function __construct($name = null)
    {
        $this->file = new File();
        $this->image = new Image();
        $this->converter = new Converter();
        $this->webp = new Webp();

        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this
            ->setName('convert')
            ->setDescription('Convert images to webp format')
            ->addOption(
                self::PATH_OPTION_NAME,
                'p',
                InputOption::VALUE_REQUIRED,
                'Path to image'
            )
            ->addOption(
                self::ORIGINAL_PATH_OPTION_NAME,
                'op',
                InputOption::VALUE_OPTIONAL,
                'Save the webp image in the same directory as the original image',
                Config::isOriginalPath()
            )
            ->addOption(
                self::QUALITY_OPTION_NAME,
                'qlt',
                InputOption::VALUE_OPTIONAL,
                'Conversion quality',
                Config::getQuality() ?? self::MAX_QUALITY
            )
            ->addOption(
                self::OVERRIDE_OPTION_NAME,
                'oride',
                InputOption::VALUE_NONE,
                'Convert the image whether the webp file exists or not'
            )
            ->addOption(
                self::ALL_OPTION_NAME,
                'a',
                InputOption::VALUE_NONE,
                'Convert all images in folders: user/images, user/pages, user/themes'
            );
    }

    /**
     * @inheritDoc
     */
    protected function serve(): int
    {
        $result = false;
        $lang = self::getLanguage();

        $path = $this->input->getOption(self::PATH_OPTION_NAME);
        $originalPath = $this->input->getOption(self::ORIGINAL_PATH_OPTION_NAME);
        $quality = $this->input->getOption(self::QUALITY_OPTION_NAME);
        $override = $this->input->getOption(self::OVERRIDE_OPTION_NAME);

        $all = $this->input->getOption(self::ALL_OPTION_NAME);

        if ($all) {
            [$result, $message, $messageType] = $this->convertAll($lang, $originalPath, $quality);
        } else if ($path) {
            [$result, $message, $messageType] = $this->convert($lang, $path, $override, $originalPath, $quality);
        } else {
            $message = $lang->translate('PLUGIN_WEBP.PATH_OPTION_ERROR');
            $messageType = self::MESSAGE_TYPE_ERROR;
        }

        $status = $result | 0;
        $this->printMessage($message, $messageType);

        return $status;
    }

    /**
     * @param Language $lang
     * @param string $path
     * @param bool $override
     * @param bool $originalPath
     * @param int $quality
     * @return array
     */
    private function convert(Language $lang, string $path, bool $override, bool $originalPath, int $quality): array
    {
        $result = false;
        $messageType = self::MESSAGE_TYPE_SUCCESS;
        $image = $this->file->getFile($path);

        if ($image) {
            if (!$this->image->isWebp($image)) {
                $imageData = $this->image->getImageData($image, true);

                $webpPath = $this->image->getWebpPath(
                    $originalPath,
                    $image->getRelativePath(),
                    $image->getFilenameWithoutExtension()
                );

                $imageExists = $this->file->fileExists($path);
                $webpExistsAndOverride = $this->file->fileExists($webpPath) && $override;
                $webpNotExists = !$this->file->fileExists($webpPath);

                if ($imageExists && ($webpExistsAndOverride || $webpNotExists)) {
                    $result = $this->converter->convert($imageData, $originalPath, $quality);
                    $message = $lang->translate(['PLUGIN_WEBP.CONVERSION_SUCCESS_MESSAGE', $webpPath]);
                } else {
                    $message = $lang->translate(['PLUGIN_WEBP.WEBP_IMAGE_EXISTS_ERROR', self::OVERRIDE_OPTION_NAME]);
                    $messageType = self::MESSAGE_TYPE_ERROR;
                }
            } else {
                $message = $lang->translate('PLUGIN_WEBP.IMAGE_WEBP_ERROR');
                $messageType = self::MESSAGE_TYPE_ERROR;
            }
        } else {
            $message = $lang->translate('PLUGIN_WEBP.IMAGE_NOT_FOUND_ERROR');
            $messageType = self::MESSAGE_TYPE_ERROR;
        }

        return [$result, $message, $messageType];
    }

    /**
     * @param Language $lang
     * @param int $quality
     * @return array
     */
    private function convertAll(Language $lang, bool $originalPath, int $quality): array
    {
        $messageType = self::MESSAGE_TYPE_SUCCESS;
        $images = $this->webp->getImagesToConversion($originalPath);
        $convertedImages = 0;
        $totalImages = count($images);

        if ($totalImages) {
            $progressBar = new ProgressBar($this->output, $totalImages);
            $progressBar->start();

            foreach ($images as $image) {
                if ($this->converter->convert($image, $originalPath, $quality)) {
                    $convertedImages++;
                    $progressBar->advance();
                }
            }

            $progressBar->finish();
            $this->output->newLine();

            $message = $lang->translate(['PLUGIN_WEBP.CONVERSION_ALL_SUCCESS_MESSAGE', $convertedImages, $totalImages]);
        } else {
            $message = $lang->translate('PLUGIN_WEBP.NO_IMAGES_TO_CONVERSION_MESSAGE');
            $messageType = self::MESSAGE_TYPE_INFO;
        }

        return [true, $message, $messageType];
    }
}
