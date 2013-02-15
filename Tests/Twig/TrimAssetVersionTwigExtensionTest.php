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

use Ivory\CKEditorBundle\Twig\TrimAssetVersionTwigExtension;

/**
 * Trim asset version twig extension test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class TrimAssetVersionTwigExtensionTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\CKEditorBundle\Twig\TrimAssetVersionTwigExtension */
    protected $trimAssetVersionTwigExtension;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->trimAssetVersionTwigExtension = new TrimAssetVersionTwigExtension();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->trimAssetVersionTwigExtension);
    }

    public function testTrimAssetVersionWithVersion()
    {
        $this->assertSame('/bar', $this->trimAssetVersionTwigExtension->trimAssetVersion('/bar?v2'));
    }

    public function testTrimAssetVersionWithoutVersion()
    {
        $this->assertSame('/bar', $this->trimAssetVersionTwigExtension->trimAssetVersion('/bar'));
    }

    public function testTrimAssetVersionFilter()
    {
        $twig = new Twig_Environment(new Twig_Loader_String());
        $twig->addExtension($this->trimAssetVersionTwigExtension);

        $this->assertSame('/bar', $twig->render('{{ \'/bar?v2\' | trim_asset_version }}'));
    }
}
