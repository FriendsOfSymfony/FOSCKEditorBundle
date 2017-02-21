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
use Ivory\CKEditorBundle\Tests\AbstractTestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ConfigManagerTest extends AbstractTestCase
{
    /**
     * @var ConfigManager
     */
    private $configManager;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->configManager = new ConfigManager();
    }

    public function testDefaultState()
    {
        $this->assertNull($this->configManager->getDefaultConfig());
        $this->assertFalse($this->configManager->hasConfigs());
        $this->assertSame([], $this->configManager->getConfigs());
    }

    public function testInitialState()
    {
        $configs = [
            'foo' => ['foo'],
            'bar' => ['bar'],
        ];

        $this->configManager = new ConfigManager($configs, 'foo');

        $this->assertSame('foo', $this->configManager->getDefaultConfig());
        $this->assertTrue($this->configManager->hasConfigs());
        $this->assertSame($configs, $this->configManager->getConfigs());
    }

    public function testSetConfig()
    {
        $this->configManager->setConfig('foo', ['foo' => 'bar']);
        $this->configManager->setConfig('foo', $config = ['foo' => 'baz']);

        $this->assertSame($config, $this->configManager->getConfig('foo'));
    }

    public function testMergeConfig()
    {
        $this->configManager->setConfig('foo', $config1 = ['foo' => 'bar', 'bar' => 'foo']);
        $this->configManager->mergeConfig('foo', $config2 = ['foo' => 'baz']);

        $this->assertSame(array_merge($config1, $config2), $this->configManager->getConfig('foo'));
    }

    public function testDefaultConfig()
    {
        $this->configManager->setConfig('foo', ['foo' => 'bar']);
        $this->configManager->setDefaultConfig('foo');
    }

    /**
     * @expectedException \Ivory\CKEditorBundle\Exception\ConfigManagerException
     * @expectedExceptionMessage The CKEditor config "foo" does not exist.
     */
    public function testDefaultConfigWithInvalidValue()
    {
        $this->configManager->setDefaultConfig('foo');
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
        $this->configManager->mergeConfig('foo', ['foo' => 'bar']);
    }
}
