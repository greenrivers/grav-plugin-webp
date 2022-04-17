<?php
/**
 * @author Greenrivers
 * @copyright Copyright (c) 2022 Greenrivers
 * @package Grav\Plugin\Webp
 */

namespace Grav\Plugin\Console;

use Grav\Common\Language\Language;
use Grav\Plugin\Webp\Console\ConsoleCommand;
use Grav\Plugin\Webp\Helper\Clear;
use Grav\Plugin\Webp\Helper\File;
use Grav\Plugin\Webp\Helper\Image;
use Grav\Plugin\Webp\Webp;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputOption;

class ClearCommand extends ConsoleCommand
{
    private const PATH_OPTION_NAME = 'path';

    private const ALL_OPTION_NAME = 'all';

    /** @var File */
    private $file;

    /** @var Image */
    private $image;

    /** @var Clear */
    private $clear;

    /** @var Webp */
    private $webp;

    /**
     * ClearCommand constructor.
     * @param $name
     */
    public function __construct($name = null)
    {
        $this->file = new File();
        $this->image = new Image();
        $this->clear = new Clear();
        $this->webp = new Webp();

        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this
            ->setName('clear')
            ->setDescription('Remove webp images')
            ->addOption(
                self::PATH_OPTION_NAME,
                'p',
                InputOption::VALUE_REQUIRED,
                'Path to webp image'
            )
            ->addOption(
                self::ALL_OPTION_NAME,
                'a',
                InputOption::VALUE_NONE,
                'Remove all webp images'
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

        $all = $this->input->getOption(self::ALL_OPTION_NAME);

        if ($all) {
            [$result, $message, $messageType] = $this->clearAll($lang);
        } else if ($path) {
            [$result, $message, $messageType] = $this->clear($lang, $path);
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
     * @param string $webpPath
     * @return array
     */
    private function clear(Language $lang, string $webpPath): array
    {
        $result = false;
        $messageType = self::MESSAGE_TYPE_SUCCESS;
        $webpImage = $this->file->getFile($webpPath);

        if ($webpImage) {
            if ($this->image->isWebp($webpImage)) {
                $webpImageData = $this->image->getImageData($webpImage, true);

                $result = $this->clear->removeImage($webpImageData);
                $message = $lang->translate(['PLUGIN_WEBP.CLEAR_SUCCESS_MESSAGE', $webpPath]);
            } else {
                $message = $lang->translate('PLUGIN_WEBP.IMAGE_NOT_WEBP_ERROR');
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
     * @return array
     */
    private function clearAll(Language $lang): array
    {
        $messageType = self::MESSAGE_TYPE_SUCCESS;
        $webpImages = $this->webp->getImagesToRemove();
        $removedImages = 0;
        $totalImages = count($webpImages);

        if ($totalImages) {
            $progressBar = new ProgressBar($this->output, $totalImages);
            $progressBar->start();

            foreach ($webpImages as $image) {
                if ($this->clear->removeImage($image)) {
                    $removedImages++;
                    $progressBar->advance();
                }
            }

            $progressBar->finish();
            $this->output->newLine();

            $message = $lang->translate(['PLUGIN_WEBP.CLEAR_ALL_SUCCESS_MESSAGE', $removedImages, $totalImages]);
        } else {
            $message = $lang->translate('PLUGIN_WEBP.NO_IMAGES_TO_REMOVE_MESSAGE');
            $messageType = self::MESSAGE_TYPE_INFO;
        }

        return [true, $message, $messageType];
    }
}
