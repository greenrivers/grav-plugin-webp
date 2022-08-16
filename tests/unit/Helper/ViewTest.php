<?php
/**
 * @author Greenrivers
 * @copyright Copyright (c) 2022 Greenrivers
 * @package Grav\Plugin\Webp
 */

namespace Tests\Unit\Helper;

use Codeception\Test\Unit;
use Grav\Plugin\Webp\Helper\View;

class ViewTest extends Unit
{
    /** @var View */
    private $view;

    /**
     * @inheritDoc
     */
    protected function _before()
    {
        $this->view = new View();
    }

    /**
     * @covers View::getImageExtension
     */
    public function testGetJpgImageExtension()
    {
        $imagePath = 'user/themes/quark/thumbnail.jpg';

        $result = $this->view->getImageExtension($imagePath);

        $this->assertEquals('jpg', $result);
    }

    /**
     * @covers View::getImageExtension
     */
    public function testGetWebpImageExtension()
    {
        $imagePath = 'user/webp/user/themes/quark/thumbnail.webp';

        $result = $this->view->getImageExtension($imagePath);

        $this->assertEquals('webp', $result);
    }
}
