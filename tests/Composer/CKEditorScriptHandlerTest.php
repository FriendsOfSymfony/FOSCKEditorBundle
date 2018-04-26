<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Tests\Composer;

use Composer\Composer;
use Composer\Config;
use Composer\IO\IOInterface;
use Composer\Package\Package;
use Composer\Script\CommandEvent;
use Composer\Script\Event;
use Ivory\CKEditorBundle\Composer\CKEditorScriptHandler;
use Ivory\CKEditorBundle\Installer\CKEditorInstaller;
use Ivory\CKEditorBundle\Tests\AbstractTestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CKEditorScriptHandlerTest extends AbstractTestCase
{
    /**
     * @var string
     */
    private $path;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->path = __DIR__.'/../../Resources/public';

        $this->tearDown();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        if (file_exists($this->path)) {
            exec('rm -rf '.$this->path);
        }
    }

    public function testInstall()
    {
        CKEditorScriptHandler::install($this->createEventMock());
        $this->assertInstall();
    }

    public function testReinstall()
    {
        CKEditorScriptHandler::install($this->createEventMock());
        $this->assertInstall();

        CKEditorScriptHandler::install($this->createEventMock());
        $this->assertInstall();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Event
     */
    private function createEventMock()
    {
        $config = $this->createMock(Config::class);
        $config
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap([
                ['process-timeout', 300],
                ['vendor-dir', __DIR__.'/../../vendor'],
            ]));

        $package = $this->getMockBuilder(Package::class)
            ->disableOriginalConstructor()
            ->getMock();

        $package
            ->expects($this->any())
            ->method('getExtra')
            ->will($this->returnValue([
                'ckeditor-clear'  => CKEditorInstaller::CLEAR_DROP,
                'symfony-bin-dir' => __DIR__.'/../Fixtures',
                'symfony-var-dir' => __DIR__.'/../Fixtures',
            ]));

        $composer = $this->createMock(Composer::class);
        $composer
            ->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($config));

        $composer
            ->expects($this->any())
            ->method('getPackage')
            ->will($this->returnValue($package));

        $io = $this->createMock(IOInterface::class);

        $event = $this->getMockBuilder(class_exists(CommandEvent::class) ? CommandEvent::class : Event::class)
            ->disableOriginalConstructor()
            ->getMock();

        $event
            ->expects($this->any())
            ->method('getComposer')
            ->will($this->returnValue($composer));

        $event
            ->expects($this->any())
            ->method('getIO')
            ->will($this->returnValue($io));

        return $event;
    }

    private function assertInstall()
    {
        $this->assertFileExists($this->path.'/ckeditor.js');
    }
}
