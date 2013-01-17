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

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->configManager = new ConfigManager();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->configManager);
    }

    public function testDefaultState()
    {
        $this->assertFalse($this->configManager->hasConfigs());
        $this->assertEmpty($this->configManager->getConfigs());
    }

    public function testInitialState()
    {
        $configs = array(
            'foo' => array('foo'),
            'bar' => array('bar'),
        );

        $this->configManager = new ConfigManager($configs);

        $this->assertTrue($this->configManager->hasConfigs());
        $this->assertSame($configs, $this->configManager->getConfigs());
    }

    public function testConfig()
    {
        $this->configManager->setConfig('foo', $config = array('foo', 'bar'));

        $this->assertSame($config, $this->configManager->getConfig('foo'));
    }

    /**
     * @expectedException \Ivory\CKEditorBundle\Exception\ConfigManagerException
     * @expectedExceptionMessage The CKEditor config "foo" does not exist.
     */
    public function testConfigWithInvalidName()
    {
        $this->configManager->getConfig('foo');
    }
}
