<?php
/**
 * @author Greenrivers Team
 * @copyright Copyright (c) 2021 Greenrivers
 * @package Grav\Plugin
 */

namespace Grav\Plugin;

use Composer\Autoload\ClassLoader;
use Grav\Common\Grav;
use Grav\Common\Plugin;
use Grav\Common\Twig\Twig;
use Grav\Common\Uri;
use Grav\Framework\Session\SessionInterface;
use Grav\Plugin\Webp\Utils\Response;
use Grav\Plugin\Webp\Webp;
use RocketTheme\Toolbox\Event\Event;

class WebpPlugin extends Plugin
{
    /** @var Webp */
    private $webp;

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onPluginsInitialized' => [
                ['autoload', 100000],
                ['onPluginsInitialized', 0],
                ['onPagesInitialized', 0]
            ]
        ];
    }

    /**
     * @return ClassLoader
     */
    public function autoload(): ClassLoader
    {
        return require __DIR__ . '/vendor/autoload.php';
    }

    public function onPluginsInitialized(): void
    {
        $this->enable([
            'onAssetsInitialized' => ['onAssetsInitialized', 0],
            'onAdminTwigTemplatePaths' => ['onAdminTwigTemplatePaths', 0],
            'onTwigInitialized' => ['onTwigInitialized', 0]
        ]);

        $this->webp = new Webp();
    }

    public function onPagesInitialized(): void
    {
        /** @var SessionInterface $session */
        $session = $this->grav['session'];

        /** @var Uri $uri */
        $uri = $this->grav['uri'];

        $paths = $uri->paths();

        if (isset($paths[2]) && $paths[2] === 'webp' && isset($paths[3])) {
            switch ($paths[3]) {
                case 'convert':
                    $totalImages = $session->getFlashObject('total_images');
                    $convertedImagesCount = $this->webp->process(
                        $totalImages, $session->getFlashObject('converted_images_count')
                    );
                    $session->setFlashObject('total_images', $totalImages);
                    $session->setFlashObject('converted_images_count', $convertedImagesCount);

                    Response::sendJsonResponse(['converted_images' => $convertedImagesCount]);
                    break;
                case 'clear_all':
                    $webpImages = $session->getFlashObject('webp_images');
                    $removedImagesCount = $this->webp->clearAll(
                        $webpImages, $session->getFlashObject('removed_images_count')
                    );
                    $session->setFlashObject('webp_images', $webpImages);
                    $session->setFlashObject('removed_images_count', $removedImagesCount);

                    Response::sendJsonResponse(['removed_images' => $removedImagesCount]);
                    break;
                case 'webp_images':
                    $webpImages = $this->webp->getImagesToRemove();
                    $session->setFlashObject('webp_images', $webpImages);
                    $session->setFlashObject('removed_images_count', 0);

                    Response::sendJsonResponse(['webp_images' => count($webpImages)]);
                    break;
                case 'images':
                    $totalImages = $this->webp->getImagesToConversion();
                    $session->setFlashObject('total_images', $totalImages);
                    $session->setFlashObject('converted_images_count', 0);

                    Response::sendJsonResponse(['total_images' => count($totalImages)]);
                    break;
                default:
                    Response::sendJsonResponse(['error' => true]);
                    break;
            }
        }
    }

    public function onAssetsInitialized(): void
    {
        if ($this->isAdmin()) {
            $this->grav['assets']->addCss('plugin://webp/assets/css/admin.min.css');
            $this->grav['assets']->addJs('plugin://webp/assets/js/admin.min.js');
        }
    }

    /**
     * @param Event $event
     * @return Event
     */
    public function onAdminTwigTemplatePaths(Event $event): Event
    {
        $event['paths'] = array_merge(
            $event['paths'],
            [__DIR__ . '/admin/themes/grav/templates']
        );

        return $event;
    }

    /**
     * @param Event $event
     */
    public function onTwigInitialized(Event $event): void
    {
        if (self::isEnabled()) {
            /** @var Twig $gravTwig */
            $gravTwig = $this->grav['twig'];

            $gravTwig->twig()->addFilter(
                new \Twig_SimpleFilter('webp', [$this, 'webp'])
            );

            $gravTwig->twig()->addFilter(
                new \Twig_SimpleFilter('extension', [$this, 'extension'])
            );
        }
    }

    /**
     * @param string $imagePath
     * @return string
     */
    public function webp(string $imagePath): string
    {
        return $this->webp->changeImagePath($imagePath);
    }

    /**
     * @param string $imagePath
     * @return string
     */
    public function extension(string $imagePath): string
    {
        return $this->webp->getImageExtension($imagePath);
    }

    /**
     * @return bool
     */
    private static function isEnabled(): bool
    {
        return Grav::instance()['config']->get('plugins.webp.enabled');
    }
}
