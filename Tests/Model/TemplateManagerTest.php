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

use Ivory\CKEditorBundle\Model\TemplateManager;

/**
 * Template manager test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class TemplateManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\CKEditorBundle\Model\TemplateManager */
    protected $templateManager;

    /** @var \Symfony\Component\Templating\Helper\CoreAssetsHelper */
    protected $assetsHelperMock;

    /** @var \Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper */
    protected $assetsVersionTrimerHelperMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->assetsHelperMock = $this->getMockBuilder('Symfony\Component\Templating\Helper\CoreAssetsHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $this->assetsVersionTrimerHelperMock = $this->getMock('Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper');

        $this->templateManager = new TemplateManager($this->assetsHelperMock, $this->assetsVersionTrimerHelperMock);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->templateManager);
        unset($this->assetsHelperMock);
        unset($this->assetsVersionTrimerHelperMock);
    }

    public function testDefaultState()
    {
        $this->assertSame($this->assetsHelperMock, $this->templateManager->getAssetsHelper());
        $this->assertSame($this->assetsVersionTrimerHelperMock, $this->templateManager->getAssetsVersionTrimerHelper());
        $this->assertFalse($this->templateManager->hasTemplates());
        $this->assertSame(array(), $this->templateManager->getTemplates());
    }

    public function testInitialState()
    {
        $this->assetsHelperMock
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo('/my/path'), $this->equalTo(null))
            ->will($this->returnValue('foo'));

        $this->assetsVersionTrimerHelperMock
            ->expects($this->once())
            ->method('trim')
            ->with($this->equalTo('foo'))
            ->will($this->returnValue('/my/rewritten/path'));

        $this->templateManager = new TemplateManager(
            $this->assetsHelperMock,
            $this->assetsVersionTrimerHelperMock,
            array(
                'default' => array(
                    'imagesPath' => '/my/path',
                    'templates'  => array(
                        array(
                            'title' => 'My Template',
                            'html'  => '<h1>Template</h1><p>Type your text here.</p>',
                        ),
                    ),
                ),
            )
        );

        $this->assertTrue($this->templateManager->hasTemplates());
        $this->assertTrue($this->templateManager->hasTemplate('default'));

        $expected = array(
            'imagesPath' => '/my/rewritten/path',
            'templates'  => array(
                array(
                    'title' => 'My Template',
                    'html'  => '<h1>Template</h1><p>Type your text here.</p>',
                ),
            ),
        );

        $this->assertSame($expected, $this->templateManager->getTemplate('default'));
    }

    public function testTemplates()
    {
        $this->assetsHelperMock
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo('/my/path'), $this->equalTo(null))
            ->will($this->returnValue('foo'));

        $this->assetsVersionTrimerHelperMock
            ->expects($this->once())
            ->method('trim')
            ->with($this->equalTo('foo'))
            ->will($this->returnValue('/my/rewritten/path'));

        $this->templateManager->setTemplates(array(
            'default' => array(
                'imagesPath' => '/my/path',
                'templates'  => array(
                    array(
                        'title' => 'My Template',
                        'html'  => '<h1>Template</h1><p>Type your text here.</p>',
                    ),
                ),
            ),
        ));

        $this->assertTrue($this->templateManager->hasTemplates());
        $this->assertTrue($this->templateManager->hasTemplate('default'));

        $expected = array(
            'default' => array(
                'imagesPath' => '/my/rewritten/path',
                'templates'  => array(
                    array(
                        'title' => 'My Template',
                        'html'  => '<h1>Template</h1><p>Type your text here.</p>',
                    ),
                ),
            ),
        );

        $this->assertSame($expected, $this->templateManager->getTemplates());
    }

    /**
     * @expectedException \Ivory\CKEditorBundle\Exception\TemplateManagerException
     * @expectedExceptionMessage The CKEditor template "foo" does not exist.
     */
    public function testGetTemplateWithInvalidValue()
    {
        $this->templateManager->getTemplate('foo');
    }
}
