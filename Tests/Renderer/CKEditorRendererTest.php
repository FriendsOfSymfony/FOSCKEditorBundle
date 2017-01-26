<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Tests\Renderer;

use Ivory\CKEditorBundle\Renderer\CKEditorRenderer;
use Ivory\CKEditorBundle\Tests\AbstractTestCase;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\Helper\CoreAssetsHelper;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CKEditorRendererTest extends AbstractTestCase
{
    /**
     * @var CKEditorRenderer
     */
    private $renderer;

    /**
     * @var ContainerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $container;

    /**
     * @var Packages|CoreAssetsHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $packages;

    /**
     * @var Request|\PHPUnit_Framework_MockObject_MockObject
     */
    private $request;

    /**
     * @var RequestStack|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestStack;

    /**
     * @var RouterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $router;

    /**
     * @var EngineInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $templating;

    /**
     * @var \Twig_Environment|\PHPUnit_Framework_MockObject_MockObject
     */
    private $twig;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (class_exists('Symfony\Component\Asset\Packages')) {
            $this->packages = $this->getMockBuilder('Symfony\Component\Asset\Packages')
                ->disableOriginalConstructor()
                ->getMock();
        } else {
            $this->packages = $this->getMockBuilder('Symfony\Component\Templating\Helper\CoreAssetsHelper')
                ->disableOriginalConstructor()
                ->getMock();
        }

        if (class_exists('Symfony\Component\HttpFoundation\RequestStack')) {
            $this->requestStack = $this->createMock('Symfony\Component\HttpFoundation\RequestStack');
        }

        $this->request = $this->createMock('Symfony\Component\HttpFoundation\Request');
        $this->router = $this->createMock('Symfony\Component\Routing\RouterInterface');
        $this->templating = $this->createMock('Symfony\Component\Templating\EngineInterface');
        $this->twig = $this->getMockBuilder('\Twig_Environment')
            ->disableOriginalConstructor()
            ->getMock();

        $this->container = $this->createMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->container
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                array(
                    'assets.packages',
                    ContainerInterface::NULL_ON_INVALID_REFERENCE,
                    $this->packages,
                ),
                array(
                    'request',
                    ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE,
                    $this->request,
                ),
                array(
                    'request_stack',
                    ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE,
                    $this->requestStack,
                ),
                array(
                    'router',
                    ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE,
                    $this->router,
                ),
                array(
                    'templating',
                    ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE,
                    $this->templating,
                ),
                array(
                    'twig',
                    ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE,
                    $this->twig,
                ),
            )));

        $this->renderer = new CKEditorRenderer($this->container);
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf('Ivory\CKEditorBundle\Renderer\CKEditorRendererInterface', $this->renderer);
    }

    /**
     * @dataProvider pathProvider
     */
    public function testRenderBasePath($path, $asset, $url)
    {
        $this->packages
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo($path))
            ->will($this->returnValue($asset));

        $this->assertSame($url, $this->renderer->renderBasePath($path));
    }

    public function testRenderJsPath()
    {
        $this->packages
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo('foo'))
            ->will($this->returnValue('bar'));

        $this->assertSame('bar', $this->renderer->renderJsPath('foo'));
    }

    /**
     * @dataProvider languageProvider
     */
    public function testRenderWidgetWithRequestStack($symfonyLocale, $ckEditorLocale)
    {
        if (!class_exists('Symfony\Component\HttpFoundation\RequestStack')) {
            $this->markTestSkipped();
        }

        $this->container
            ->expects($this->once())
            ->method('has')
            ->with($this->identicalTo('request_stack'))
            ->will($this->returnValue(true));

        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createMock('Symfony\Component\HttpFoundation\Request')));

        $request
            ->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue($symfonyLocale));

        $this->assertSame(
            'CKEDITOR.replace("foo", {"language":"'.$ckEditorLocale.'"});',
            $this->renderer->renderWidget('foo', array())
        );
    }

    /**
     * @dataProvider languageProvider
     */
    public function testRenderWidgetWithRequest($symfonyLocale, $ckEditorLocale)
    {
        $this->container
            ->expects($this->exactly(2))
            ->method('has')
            ->will($this->returnValueMap(array(
                array('request_stack', false),
                array('request', true),
            )));

        $this->request
            ->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue($symfonyLocale));

        $this->assertSame(
            'CKEDITOR.replace("foo", {"language":"'.$ckEditorLocale.'"});',
            $this->renderer->renderWidget('foo', array())
        );
    }

    /**
     * @dataProvider languageProvider
     */
    public function testRenderWidgetWithLocaleParameter($symfonyLocale, $ckEditorLocale)
    {
        $this->container
            ->expects($this->exactly(2))
            ->method('has')
            ->will($this->returnValueMap(array(
                array('request_stack', false),
                array('request', false),
            )));

        $this->container
            ->expects($this->once())
            ->method('hasParameter')
            ->with($this->identicalTo('locale'))
            ->will($this->returnValue(true));

        $this->container
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->identicalTo('locale'))
            ->will($this->returnValue($symfonyLocale));

        $this->assertSame(
            'CKEDITOR.replace("foo", {"language":"'.$ckEditorLocale.'"});',
            $this->renderer->renderWidget('foo', array())
        );
    }

    /**
     * @dataProvider languageProvider
     */
    public function testRenderWidgetWithExplicitLanguage($symfonyLocale, $ckEditorLocale)
    {
        $this->assertSame(
            'CKEDITOR.replace("foo", {"language":"'.$ckEditorLocale.'"});',
            $this->renderer->renderWidget('foo', array('language' => $symfonyLocale))
        );
    }

    public function testRenderWidgetWithoutLocale()
    {
        $this->container
            ->expects($this->exactly(2))
            ->method('has')
            ->will($this->returnValueMap(array(
                array('request_stack', false),
                array('request', false),
            )));

        $this->container
            ->expects($this->once())
            ->method('hasParameter')
            ->with($this->identicalTo('locale'))
            ->will($this->returnValue(false));

        $this->assertSame(
            'CKEDITOR.replace("foo", []);',
            $this->renderer->renderWidget('foo', array())
        );
    }

    /**
     * @dataProvider pathProvider
     */
    public function testRenderWidgetWithStringContentsCss($path, $asset, $url)
    {
        $this->packages
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo($path))
            ->will($this->returnValue($asset));

        $this->assertSame(
            'CKEDITOR.replace("foo", {"contentsCss":['.json_encode($url).']});',
            $this->renderer->renderWidget('foo', array('contentsCss' => $path))
        );
    }

    /**
     * @dataProvider pathsProvider
     */
    public function testRenderWidgetWithArrayContentsCss(array $paths, array $assets, array $urls)
    {
        foreach (array_keys($paths) as $key) {
            $this->packages
                ->expects($this->at($key))
                ->method('getUrl')
                ->with($this->equalTo($paths[$key]))
                ->will($this->returnValue($assets[$key]));
        }

        $this->assertSame(
            'CKEDITOR.replace("foo", {"contentsCss":'.json_encode($urls).'});',
            $this->renderer->renderWidget('foo', array('contentsCss' => $paths))
        );
    }

    /**
     * @dataProvider filebrowserProvider
     */
    public function testRenderWidgetWithFileBrowser($filebrowser)
    {
        $this->assertSame(
            'CKEDITOR.replace("foo", {"filebrowser'.$filebrowser.'Url":"'.($url = 'browse_url').'"});',
            $this->renderer->renderWidget('foo', array('filebrowser'.$filebrowser.'Url' => $url))
        );
    }

    public function testRenderWidgetWithCustomFileBrowser()
    {
        $this->assertSame(
            'CKEDITOR.replace("foo", {"filebrowser'.($filebrowser = 'VideoBrowse').'Url":"'.($url = 'browse_url').'"});',
            $this->renderer->renderWidget(
                'foo',
                array('filebrowser'.$filebrowser.'Url' => $url),
                array('filebrowsers'                   => array($filebrowser))
            )
        );
    }

    /**
     * @dataProvider filebrowserProvider
     */
    public function testRenderWidgetWithMinimalRouteFileBrowser($filebrowser)
    {
        $this->router
            ->expects($this->once())
            ->method('generate')
            ->with(
                $this->identicalTo($route = 'browse_route'),
                $this->identicalTo(array()),
                $this->identicalTo(
                    defined('Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_PATH')
                        ? UrlGeneratorInterface::ABSOLUTE_PATH
                        : false
                )
            )
            ->will($this->returnValue('browse_url'));

        $this->assertSame(
            'CKEDITOR.replace("foo", {"filebrowser'.$filebrowser.'Url":"browse_url"});',
            $this->renderer->renderWidget('foo', array('filebrowser'.$filebrowser.'Route' => $route))
        );
    }

    /**
     * @dataProvider filebrowserProvider
     */
    public function testRenderWidgetWithMaximalRouteFileBrowser($filebrowser)
    {
        $this->router
            ->expects($this->once())
            ->method('generate')
            ->with(
                $this->identicalTo($route = 'browse_route'),
                $this->identicalTo($routeParameters = array('foo' => 'bar')),
                $this->identicalTo(
                    $routeType = defined('Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_PATH')
                        ? UrlGeneratorInterface::ABSOLUTE_PATH
                        : true
                )
            )
            ->will($this->returnValue('browse_url'));

        $this->assertSame(
            'CKEDITOR.replace("foo", {"filebrowser'.$filebrowser.'Url":"browse_url"});',
            $this->renderer->renderWidget('foo', array(
                'filebrowser'.$filebrowser.'Route'           => $route,
                'filebrowser'.$filebrowser.'RouteParameters' => $routeParameters,
                'filebrowser'.$filebrowser.'RouteAbsolute'   => true,
            ))
        );
    }

    /**
     * @dataProvider filebrowserProvider
     */
    public function testRenderWidgetWithMaximalRelativeFileBrowser($filebrowser)
    {
        $this->router
            ->expects($this->once())
            ->method('generate')
            ->with(
                $this->identicalTo($route = 'browse_route'),
                $this->identicalTo($routeParameters = array('foo' => 'bar')),
                $this->identicalTo(
                    $routeType = defined('Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_PATH')
                        ? UrlGeneratorInterface::RELATIVE_PATH
                        : false
                )
            )
            ->will($this->returnValue('browse_url'));

        $this->assertSame(
            'CKEDITOR.replace("foo", {"filebrowser'.$filebrowser.'Url":"browse_url"});',
            $this->renderer->renderWidget('foo', array(
                'filebrowser'.$filebrowser.'Route'           => $route,
                'filebrowser'.$filebrowser.'RouteParameters' => $routeParameters,
                'filebrowser'.$filebrowser.'RouteAbsolute'   => false,
            ))
        );
    }

    /**
     * @dataProvider filebrowserProvider
     */
    public function testRenderWidgetWithRouteFileBrowserHandler($filebrowser)
    {
        $this->router
            ->expects($this->once())
            ->method('generate')
            ->with(
                $this->equalTo('browse_route'),
                $this->equalTo(array('foo' => 'bar')),
                $this->equalTo(true)
            )
            ->will($this->returnValue('browse_url'));

        $this->assertSame(
            'CKEDITOR.replace("foo", {"filebrowser'.$filebrowser.'Url":"browse_url"});',
            $this->renderer->renderWidget('foo', array(
                'filebrowser'.$filebrowser.'Handler' => function (RouterInterface $router) {
                    return $router->generate('browse_route', array('foo' => 'bar'), true);
                },
            ))
        );
    }

    public function testRenderWidgetWithProtectedSource()
    {
        $this->assertSame(
            'CKEDITOR.replace("foo", {"protectedSource":[/<\?[\s\S]*?\?>/g,/<%[\s\S]*?%>/g]});',
            $this->renderer->renderWidget('foo', array(
                'protectedSource' => array(
                    '/<\?[\s\S]*?\?>/g',
                    '/<%[\s\S]*?%>/g',
                ),
            ))
        );
    }

    public function testRenderWidgetWithStylesheetParserSkipSelectors()
    {
        $this->assertSame(
            'CKEDITOR.replace("foo", {"stylesheetParser_skipSelectors":/(^body\.|^caption\.|\.high|^\.)/i});',
            $this->renderer->renderWidget('foo', array(
                'stylesheetParser_skipSelectors' => '/(^body\.|^caption\.|\.high|^\.)/i',
            ))
        );
    }

    public function testRenderWidgetWithStylesheetParserValidSelectors()
    {
        $this->assertSame(
            'CKEDITOR.replace("foo", {"stylesheetParser_validSelectors":/\^(p|span)\.\w+/});',
            $this->renderer->renderWidget('foo', array(
                'stylesheetParser_validSelectors' => '/\^(p|span)\.\w+/',
            ))
        );
    }

    public function testRenderWidgetWithCKEditorConstants()
    {
        $this->assertSame(
            'CKEDITOR.replace("foo", {"config":{"enterMode":CKEDITOR.ENTER_BR,"shiftEnterMode":CKEDITOR.ENTER_BR}});',
            $this->renderer->renderWidget('foo', array(
                'config' => array(
                    'enterMode'      => 'CKEDITOR.ENTER_BR',
                    'shiftEnterMode' => 'CKEDITOR.ENTER_BR',
                ),
            ))
        );
    }

    public function testRenderWidgetWithoutAutoInline()
    {
        $expected = <<<'EOF'
CKEDITOR.disableAutoInline = true;
CKEDITOR.replace("foo", []);
EOF;

        $this->assertSame(
            $expected,
            $this->renderer->renderWidget('foo', array(), array('auto_inline' => false))
        );
    }

    public function testRenderWidgetWithInline()
    {
        $this->assertSame(
            'CKEDITOR.inline("foo", []);',
            $this->renderer->renderWidget('foo', array(), array('inline' => true))
        );
    }

    public function testRenderWidgetWithInputSync()
    {
        $expected = <<<'EOF'
var ivory_ckeditor_foo = CKEDITOR.replace("foo", []);
ivory_ckeditor_foo.on('change', function() { ivory_ckeditor_foo.updateElement(); });
EOF;

        $this->assertSame($expected, $this->renderer->renderWidget('foo', array(), array('input_sync' => true)));
    }

    public function testRenderDestroy()
    {
        $this->assertSame(
            'if (CKEDITOR.instances["foo"]) { CKEDITOR.instances["foo"].destroy(true); delete CKEDITOR.instances["foo"]; }',
            $this->renderer->renderDestroy('foo')
        );
    }

    /**
     * @dataProvider pathProvider
     */
    public function testRenderPlugin($path, $asset, $url)
    {
        $this->packages
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo($path))
            ->will($this->returnValue($asset));

        $this->assertSame(
            'CKEDITOR.plugins.addExternal("foo", '.json_encode($url).', "bat");',
            $this->renderer->renderPlugin('foo', array('path' => $path, 'filename' => 'bat'))
        );
    }

    public function testRenderStylesSet()
    {
        $this->assertSame(
            'if (CKEDITOR.stylesSet.get("foo") === null) { CKEDITOR.stylesSet.add("foo", {"foo":"bar"}); }',
            $this->renderer->renderStylesSet('foo', array('foo' => 'bar'))
        );
    }

    /**
     * @dataProvider pathProvider
     */
    public function testRenderTemplate($path, $asset, $url)
    {
        $templates = array(
            array(
                'title' => 'Template title',
                'html'  => '<p>Template content</p>',
            ),
        );

        $this->packages
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo($path))
            ->will($this->returnValue($asset));

        $json = json_encode(array('imagesPath' => $url, 'templates' => $templates));

        $this->assertSame(
            'CKEDITOR.addTemplates("foo", '.$json.');',
            $this->renderer->renderTemplate('foo', array('imagesPath' => $path, 'templates' => $templates))
        );
    }

    /**
     * @dataProvider pathProvider
     */
    public function testRenderTemplateWithTwigTemplating($path, $asset, $url)
    {
        $templates = array(
            array(
                'title'               => 'Template title',
                'template'            => $template = 'template_name',
                'template_parameters' => $templateParameters = array('foo' => 'bar'),
            ),
        );

        $processedTemplates = array(
            array(
                'title' => 'Template title',
                'html'  => $html = '<p>Template content</p>',
            ),
        );

        $this->packages
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo($path))
            ->will($this->returnValue($asset));

        $this->container
            ->expects($this->once())
            ->method('has')
            ->with($this->identicalTo('templating'))
            ->will($this->returnValue(false));

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with($this->identicalTo($template), $this->identicalTo($templateParameters))
            ->will($this->returnValue($html));

        $json = json_encode(array('imagesPath' => $url, 'templates'  => $processedTemplates));

        $this->assertSame(
            'CKEDITOR.addTemplates("foo", '.$json.');',
            $this->renderer->renderTemplate('foo', array('imagesPath' => $path, 'templates' => $templates))
        );
    }

    /**
     * @dataProvider pathProvider
     */
    public function testRenderTemplateWithPhpTemplating($path, $asset, $url)
    {
        $templates = array(
            array(
                'title'    => 'Template title',
                'template' => $template = 'template_name',
            ),
        );

        $processedTemplates = array(
            array(
                'title' => 'Template title',
                'html'  => $html = '<p>Template content</p>',
            ),
        );

        $this->packages
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo($path))
            ->will($this->returnValue($asset));

        $this->container
            ->expects($this->once())
            ->method('has')
            ->with($this->identicalTo('templating'))
            ->will($this->returnValue(true));

        $this->templating
            ->expects($this->once())
            ->method('render')
            ->with($this->identicalTo($template), $this->identicalTo(array()))
            ->will($this->returnValue($html));

        $json = json_encode(array('imagesPath' => $url, 'templates'  => $processedTemplates));

        $this->assertSame(
            'CKEDITOR.addTemplates("foo", '.$json.');',
            $this->renderer->renderTemplate('foo', array('imagesPath' => $path, 'templates' => $templates))
        );
    }

    /**
     * @return array
     */
    public function languageProvider()
    {
        return array(
            array('en', 'en'),
            array('pt_BR', 'pt-br'),
        );
    }

    /**
     * @return array
     */
    public function pathProvider()
    {
        return array(
            array('path', 'url', 'url'),
            array('path', 'url?v=2', 'url'),
        );
    }

    /**
     * @return array
     */
    public function pathsProvider()
    {
        return array(
            array(array('path'), array('url'), array('url')),
            array(array('path'), array('url?v=2'), array('url')),
            array(array('path1', 'path2'), array('url1', 'url2'), array('url1', 'url2')),
            array(array('path1', 'path2'), array('url1?v=2', 'url2'), array('url1', 'url2')),
            array(array('path1', 'path2'), array('url1', 'url2?v=2'), array('url1', 'url2')),
            array(array('path1', 'path2'), array('url1?v=2', 'url2?v=2'), array('url1', 'url2')),
        );
    }

    /**
     * @return array
     */
    public function filebrowserProvider()
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
}
