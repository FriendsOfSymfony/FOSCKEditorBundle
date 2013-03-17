<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Tests\Twig;

use \Twig_Environment,
    \Twig_Loader_String;

use Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper,
    Ivory\CKEditorBundle\Twig\TrimAssetVersionTwigExtension;

/**
 * Trim asset version twig extension test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class TrimAssetVersionTwigExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testTrimAssetVersion()
    {
        $assetsVersionTrimerHelperMock = $this->getMock('Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper');
        $trimAssetVersionTwigExtension = new TrimAssetVersionTwigExtension($assetsVersionTrimerHelperMock);

        $assetsVersionTrimerHelperMock
            ->expects($this->once())
            ->method('trim')
            ->with($this->equalTo('foo'))
            ->will($this->returnValue('bar'));

        $this->assertSame('bar', $trimAssetVersionTwigExtension->trimAssetVersion('foo'));
    }

    public function testTrimAssetVersionFilter()
    {
        $trimAssetVersionTwigExtension = new TrimAssetVersionTwigExtension(new AssetsVersionTrimerHelper());

        $twig = new Twig_Environment(new Twig_Loader_String());
        $twig->addExtension($trimAssetVersionTwigExtension);

        $this->assertSame('/bar', $twig->render('{{ \'/bar?v2\' | trim_asset_version }}'));
    }
}
