<?php
/**
 * @author Greenrivers
 * @copyright Copyright (c) 2022 Greenrivers
 * @package Grav\Plugin\Webp
 */

namespace Grav\Plugin\Console;

use Grav\Common\Grav;
use Grav\Common\Language\Language;
use Grav\Console\ConsoleCommand;
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

    /** @var Clear */
    private $clear;

    /** @var File */
    private $file;

    /** @var Image */
    private $image;

    /** @var Webp */
    private $webp;

    /**
     * ClearCommand constructor.
     * @param $name
     */
    public function __construct($name = null)
    {
        $this->clear = new Clear();
        $this->file = new File();
        $this->image = new Image();
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
                'Path to image'
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
            [$result, $message] = $this->clearAll($lang);
        } else if ($path) {
            [$result, $message] = $this->clear($lang, $path);
        } else {
            $message = $lang->translate('PLUGIN_WEBP.PATH_OPTION_ERROR');
        }

        $status = $result | 0;
        $result ? $this->output->success($message) : $this->output->error($message);

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
        $webpImage = $this->file->getFile($webpPath);

        if ($webpImage) {
            if ($this->image->isWebp($webpImage)) {
                $webpImageData = $this->image->getImageData($webpImage, true);

                $result = $this->clear->removeImage($webpImageData);
                $message = $lang->translate(['PLUGIN_WEBP.CLEAR_SUCCESS_MESSAGE', $webpPath]);
            } else {
                $message = $lang->translate('PLUGIN_WEBP.IMAGE_NOT_WEBP_ERROR');
            }
        } else {
            $message = $lang->translate('PLUGIN_WEBP.IMAGE_NOT_FOUND_ERROR');
        }

        return [$result, $message];
    }

    /**
     * @param Language $lang
     * @return array
     */
    private function clearAll(Language $lang): array
    {
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
            $message = $lang->translate('PLUGIN_WEBP.CLEAR_ALL_NO_IMAGES_MESSAGE');
        }

        return [true, $message];
    }

    /**
     * @return Language
     */
    private static function getLanguage(): Language
    {
        $grav = Grav::instance();
        return $grav['language'];
    }
}
