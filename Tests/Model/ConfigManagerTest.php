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

use Ivory\CKEditorBundle\Model\ConfigManager;

/**
 * Config manager test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ConfigManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\CKEditorBundle\Model\ConfigManager */
    protected $configManager;

    /** @var \Symfony\Component\Templating\Helper\CoreAssetsHelper */
    protected $assetsHelperMock;

    /** @var \Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper */
    protected $assetsVersionTrimerHelperMock;

    /** @var \Symfony\Component\Routing\RouterInterface */
    protected $routerMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->assetsHelperMock = $this->getMockBuilder('Symfony\Component\Templating\Helper\CoreAssetsHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $this->assetsVersionTrimerHelperMock = $this->getMock('Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper');
        $this->routerMock = $this->getMock('Symfony\Component\Routing\RouterInterface');

        $this->configManager = new ConfigManager(
            $this->assetsHelperMock,
            $this->assetsVersionTrimerHelperMock,
            $this->routerMock
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->assetsHelperMock);
        unset($this->assetsVersionTrimerHelperMock);
        unset($this->routerMock);
        unset($this->configManager);
    }

    /**
     * Gets the valid filebrowsers keys.
     *
     * @return array The valid filebrowsers keys.
     */
    public static function filebrowserProvider()
    {
        return array(
            array('Browse'),
            array('FlashBrowse'),
            array('ImageBrowse'),
            array('ImageBrowseLink'),
            array('Upload'),
            array('FlashUpload'),
            array('ImageUpload'),
        );
    }

    public function testDefaultState()
    {
        $this->assertSame($this->assetsHelperMock, $this->configManager->getAssetsHelper());
        $this->assertSame($this->assetsVersionTrimerHelperMock, $this->configManager->getAssetsVersionTrimerHelper());
        $this->assertSame($this->routerMock, $this->configManager->getRouter());
        $this->assertFalse($this->configManager->hasConfigs());
        $this->assertEmpty($this->configManager->getConfigs());
    }

    public function testInitialState()
    {
        $configs = array(
            'foo' => array('foo'),
            'bar' => array('bar'),
        );

        $this->configManager = new ConfigManager(
            $this->assetsHelperMock,
            $this->assetsVersionTrimerHelperMock,
            $this->routerMock,
            $configs
        );

        $this->assertTrue($this->configManager->hasConfigs());
        $this->assertSame($configs, $this->configManager->getConfigs());
    }

    public function testSetConfig()
    {
        $this->configManager->setConfig('foo', array('foo' => 'bar'));
        $this->configManager->setConfig('foo', $config = array('foo' => 'baz'));

        $this->assertSame($config, $this->configManager->getConfig('foo'));
    }

    public function testMergeConfig()
    {
        $this->configManager->setConfig('foo', $config1 = array('foo' => 'bar', 'bar' => 'foo'));
        $this->configManager->mergeConfig('foo', $config2 = array('foo' => 'baz'));

        $this->assertSame(array_merge($config1, $config2), $this->configManager->getConfig('foo'));
    }

    public function testConfigContentsCssWithString()
    {
        $this->assetsHelperMock
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo('foo'), $this->equalTo(null))
            ->will($this->returnValue('bar'));

        $this->assetsVersionTrimerHelperMock
            ->expects($this->once())
            ->method('trim')
            ->with($this->equalTo('bar'))
            ->will($this->returnValue('baz'));

        $this->configManager->setConfig('foo', array('contentsCss' => 'foo'));

        $this->assertSame(array('contentsCss' => array('baz')), $this->configManager->getConfig('foo'));
    }

    public function testConfigContentsCssWithArray()
    {
        $this->assetsHelperMock
            ->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValueMap(array(array('foo', null, 'foo1'), array('bar', null, 'bar1'))));

        $this->assetsVersionTrimerHelperMock
            ->expects($this->any())
            ->method('trim')
            ->will($this->returnValueMap(array(array('foo1', 'baz1'), array('bar1', 'baz2'))));

        $this->configManager->setConfig('foo', array('contentsCss' => array('foo', 'bar')));

        $this->assertSame(array('contentsCss' => array('baz1', 'baz2')), $this->configManager->getConfig('foo'));
    }

    /**
     * @dataProvider filebrowserProvider
     */
    public function testConfigFileBrowser($filebrowser)
    {
        $this->routerMock
            ->expects($this->once())
            ->method('generate')
            ->with(
                $this->equalTo('browse_route'),
                $this->equalTo(array('foo' => 'bar')),
                $this->equalTo(true)
            )
            ->will($this->returnValue('browse_url'));

        $this->configManager->setConfig(
            'foo',
            array(
                'filebrowser'.$filebrowser.'Route'           => 'browse_route',
                'filebrowser'.$filebrowser.'RouteParameters' => array('foo' => 'bar'),
                'filebrowser'.$filebrowser.'RouteAbsolute'   => true,
            )
        );

        $this->assertSame(
            array('filebrowser'.$filebrowser.'Url' => 'browse_url'),
            $this->configManager->getConfig('foo')
        );
    }

    /**
     * @expectedException \Ivory\CKEditorBundle\Exception\ConfigManagerException
     * @expectedExceptionMessage The CKEditor config "foo" does not exist.
     */
    public function testGetConfigWithInvalidName()
    {
        $this->configManager->getConfig('foo');
    }

    /**
     * @expectedException \Ivory\CKEditorBundle\Exception\ConfigManagerException
     * @expectedExceptionMessage The CKEditor config "foo" does not exist.
     */
    public function testMergeConfigWithInvalidName()
    {
        $this->configManager->mergeConfig('foo', array('foo' => 'bar'));
    }
}
