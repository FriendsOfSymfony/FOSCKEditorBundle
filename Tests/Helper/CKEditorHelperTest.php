<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Tests\Helper;

use Ivory\CKEditorBundle\Helper\CKEditorHelper;
use Symfony\Component\Routing\RouterInterface;

/**
 * CKEditor helper test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CKEditorHelperTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\CKEditorBundle\Helper\CKEditorHelper */
    protected $helper;

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

        $this->helper = new CKEditorHelper(
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
        unset($this->helper);
        unset($this->routerMock);
        unset($this->assetsVersionTrimerHelperMock);
        unset($this->assetsHelperMock);
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

    public function testInitialState()
    {
        $this->assertSame($this->assetsHelperMock, $this->helper->getAssetsHelper());
        $this->assertSame($this->assetsVersionTrimerHelperMock, $this->helper->getAssetsVersionTrimerHelper());
        $this->assertSame($this->routerMock, $this->helper->getRouter());
    }

    public function testAssetsHelper()
    {
        $assetsHelperMock = $this->getMockBuilder('Symfony\Component\Templating\Helper\CoreAssetsHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $this->helper->setAssetsHelper($assetsHelperMock);

        $this->assertSame($assetsHelperMock, $this->helper->getAssetsHelper());
    }

    public function testAssetsVersionTrimerHelper()
    {
        $assetsVersionTrimerHelperMock = $this->getMock('Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper');

        $this->helper->setAssetsVersionTrimerHelper($assetsVersionTrimerHelperMock);

        $this->assertSame($assetsVersionTrimerHelperMock, $this->helper->getAssetsVersionTrimerHelper());
    }

    public function testRouter()
    {
        $routerMock = $this->getMock('Symfony\Component\Routing\RouterInterface');

        $this->helper->setRouter($routerMock);

        $this->assertSame($routerMock, $this->helper->getRouter());
    }

    public function testRenderBasePath()
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

        $this->assertSame('baz', $this->helper->renderBasePath('foo'));
    }

    public function testRenderJsPath()
    {
        $this->assetsHelperMock
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo('foo'), $this->equalTo(null))
            ->will($this->returnValue('bar'));

        $this->assertSame('bar', $this->helper->renderJsPath('foo'));
    }

    public function testRenderReplaceWithStringContentsCss()
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

        $this->assertSame(
            'CKEDITOR.replace("foo", {"contentsCss":["baz"]});',
            $this->helper->renderReplace('foo', array('contentsCss' => 'foo'))
        );
    }

    public function testRenderReplaceWithArrayContentsCss()
    {
        $this->assetsHelperMock
            ->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValueMap(array(array('foo', null, 'foo1'), array('bar', null, 'bar1'))));

        $this->assetsVersionTrimerHelperMock
            ->expects($this->any())
            ->method('trim')
            ->will($this->returnValueMap(array(array('foo1', 'baz1'), array('bar1', 'baz2'))));

        $this->assertSame(
            'CKEDITOR.replace("foo", {"contentsCss":["baz1","baz2"]});',
            $this->helper->renderReplace('foo', array('contentsCss' => array('foo', 'bar')))
        );
    }

    /**
     * @dataProvider filebrowserProvider
     */
    public function testRenderReplaceWithFileBrowser($filebrowser)
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

        $this->assertSame(
            sprintf('CKEDITOR.replace("foo", {"filebrowser%sUrl":"browse_url"});', $filebrowser),
            $this->helper->renderReplace('foo', array(
                'filebrowser'.$filebrowser.'Route'           => 'browse_route',
                'filebrowser'.$filebrowser.'RouteParameters' => array('foo' => 'bar'),
                'filebrowser'.$filebrowser.'RouteAbsolute'   => true,
            ))
        );
    }

    /**
     * @dataProvider filebrowserProvider
     */
    public function testRenderReplaceWithFileBrowserHandler($filebrowser)
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

        $this->assertSame(
            sprintf('CKEDITOR.replace("foo", {"filebrowser%sUrl":"browse_url"});', $filebrowser),
            $this->helper->renderReplace('foo', array(
                'filebrowser'.$filebrowser.'Handler' => function (RouterInterface $router) {
                    return $router->generate('browse_route', array('foo' => 'bar'), true);
                },
            ))
        );
    }

    public function testRenderReplaceWithCKEditorConstants()
    {
        $this->assertSame(
            'CKEDITOR.replace("foo", {"config":{"enterMode":CKEDITOR.ENTER_BR,"shiftEnterMode":CKEDITOR.ENTER_BR}});',
            $this->helper->renderReplace('foo', array(
                'config' => array(
                    'enterMode'      => 'CKEDITOR.ENTER_BR',
                    'shiftEnterMode' => 'CKEDITOR.ENTER_BR',
                ),
            ))
        );
    }

    public function testRenderDestroy()
    {
        $expected = <<<EOF
if (CKEDITOR.instances["foo"]) {
    delete CKEDITOR.instances["foo"];
}
EOF;

        $this->assertSame($expected, $this->helper->renderDestroy('foo'));
    }

    public function testRenderPlugin()
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

        $this->assertSame(
            'CKEDITOR.plugins.addExternal("foo", "baz", "bat");',
            $this->helper->renderPlugin('foo', array('path' => 'foo', 'filename' => 'bat'))
        );
    }

    public function testRenderStylesSet()
    {
        $this->assertSame(
            'CKEDITOR.stylesSet.add("foo", {"foo":"bar"});',
            $this->helper->renderStylesSet('foo', array('foo' => 'bar'))
        );
    }

    public function testRenderTemplate()
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

        $this->assertSame(
            'CKEDITOR.addTemplates("foo", {"imagesPath":"baz","filename":"bat"});',
            $this->helper->renderTemplate('foo', array('imagesPath' => 'foo', 'filename' => 'bat'))
        );
    }

    public function testName()
    {
        $this->assertSame('ivory_ckeditor', $this->helper->getName());
    }
}
