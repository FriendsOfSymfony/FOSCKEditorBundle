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
use Ivory\CKEditorBundle\Tests\AbstractTestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class PluginManagerTest extends AbstractTestCase
{
    /**
     * @var PluginManager
     */
    private $pluginManager;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->pluginManager = new PluginManager();
    }

    public function testDefaultState()
    {
        $this->assertFalse($this->pluginManager->hasPlugins());
        $this->assertSame(array(), $this->pluginManager->getPlugins());
    }

    public function testInitialState()
    {
        $plugins = array(
            'wordcount' => array(
                'path'     => '/my/path',
                'filename' => 'plugin.js',
            ),
        );

        $this->pluginManager = new PluginManager($plugins);

        $this->assertTrue($this->pluginManager->hasPlugins());
        $this->assertTrue($this->pluginManager->hasPlugin('wordcount'));
        $this->assertSame($plugins['wordcount'], $this->pluginManager->getPlugin('wordcount'));
    }

    public function testPlugins()
    {
        $plugins = array(
            'wordcount' => array(
                'path'     => '/my/path',
                'filename' => 'plugin.js',
            ),
        );

        $this->pluginManager->setPlugins($plugins);

        $this->assertTrue($this->pluginManager->hasPlugins());
        $this->assertTrue($this->pluginManager->hasPlugin('wordcount'));
        $this->assertSame($plugins, $this->pluginManager->getPlugins());
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
