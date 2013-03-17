<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Tests\Model;

use Ivory\CKEditorBundle\Model\PluginManager;

/**
 * Plugin manager test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PluginManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\CKEditorBundle\Model\PluginManager */
    protected $pluginManager;

    /** @var \Symfony\Component\Templating\Helper\CoreAssetsHelper */
    protected $assetsHelperMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->assetsHelperMock = $this->getMockBuilder('Symfony\Component\Templating\Helper\CoreAssetsHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $this->pluginManager = new PluginManager($this->assetsHelperMock);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->pluginManager);
        unset($this->assetsHelperMock);
    }

    public function testDefaultState()
    {
        $this->assertSame($this->assetsHelperMock, $this->pluginManager->getAssetsHelper());
        $this->assertFalse($this->pluginManager->hasPlugins());
    }

    public function testInitialState()
    {
        $this->assetsHelperMock
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo('/my/path'), $this->equalTo(null))
            ->will($this->returnValue('/my/rewritten/path'));

        $this->pluginManager = new PluginManager($this->assetsHelperMock, array(
            'wordcount' => array(
                'path'     => '/my/path',
                'filename' => 'plugin.js'
            ),
        ));

        $this->assertTrue($this->pluginManager->hasPlugins());
        $this->assertTrue($this->pluginManager->hasPlugin('wordcount'));

        $this->assertSame(
            array('path' => '/my/rewritten/path', 'filename' => 'plugin.js'),
            $this->pluginManager->getPlugin('wordcount')
        );
    }

    public function testAssetsHelper()
    {
        $assetsHelperMock = $this->getMockBuilder('Symfony\Component\Templating\Helper\CoreAssetsHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $this->pluginManager->setAssetsHelper($assetsHelperMock);

        $this->assertSame($assetsHelperMock, $this->pluginManager->getAssetsHelper());
    }

    public function testPlugins()
    {
        $this->assetsHelperMock
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo('/my/path'), $this->equalTo(null))
            ->will($this->returnValue('/my/rewritten/path'));

        $this->pluginManager->setPlugins(array(
            'wordcount' => array(
                'path'     => '/my/path',
                'filename' => 'plugin.js'
            ),
        ));

        $this->assertTrue($this->pluginManager->hasPlugins());
        $this->assertTrue($this->pluginManager->hasPlugin('wordcount'));

        $this->assertSame(
            array('path' => '/my/rewritten/path', 'filename' => 'plugin.js'),
            $this->pluginManager->getPlugin('wordcount')
        );
    }

    /**
     * @expectedException \Ivory\CKEditorBundle\Exception\PluginManagerException
     * @expectedExceptionMessage The CKEditor plugin "foo" does not exist.
     */
    public function testGetPluginWithInvalidValue()
    {
        $this->pluginManager->getPlugin('foo');
    }
}
