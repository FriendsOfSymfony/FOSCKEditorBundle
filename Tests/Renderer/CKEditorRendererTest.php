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
use Ivory\CKEditorBundle\Renderer\CKEditorRendererInterface;
use Ivory\CKEditorBundle\Tests\AbstractTestCase;
use Ivory\JsonBuilder\JsonBuilder;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;

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
     * @var Packages|\PHPUnit_Framework_MockObject_MockObject
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
        $this->request = $this->createMock(Request::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->packages = $this->getMockBuilder(Packages::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestStack = $this->createMock(RequestStack::class);
        $this->templating = $this->createMock(EngineInterface::class);
        $this->twig = $this->getMockBuilder(\Twig_Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->container = $this->createMock(ContainerInterface::class);
        $this->container
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap([
                [
                    'assets.packages',
                    ContainerInterface::NULL_ON_INVALID_REFERENCE,
                    $this->packages,
                ],
                [
                    'ivory_ck_editor.renderer.json_builder',
                    ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE,
                    new JsonBuilder(),
                ],
                [
                    'request_stack',
                    ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE,
                    $this->requestStack,
                ],
                [
                    'router',
                    ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE,
                    $this->router,
                ],
                [
                    'templating',
                    ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE,
                    $this->templating,
                ],
                [
                    'twig',
                    ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE,
                    $this->twig,
                ],
            ]));

        $this->renderer = new CKEditorRenderer($this->container);
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf(CKEditorRendererInterface::class, $this->renderer);
    }

    /**
     * @param string $path
     * @param string $asset
     * @param string $url
     *
     * @dataProvider directoryAssetProvider
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
     * @param string $symfonyLocale
     * @param string $ckEditorLocale
     *
     * @dataProvider languageProvider
     */
    public function testRenderWidgetWithLocaleRequest($symfonyLocale, $ckEditorLocale)
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($this->request));

        $this->request
            ->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue($symfonyLocale));

        $this->assertSame(
            'CKEDITOR.replace("foo", {"language":"'.$ckEditorLocale.'"});',
            $this->renderer->renderWidget('foo', [])
        );
    }

    /**
     * @param string $symfonyLocale
     * @param string $ckEditorLocale
     *
     * @dataProvider languageProvider
     */
    public function testRenderWidgetWithLocaleParameter($symfonyLocale, $ckEditorLocale)
    {
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
            $this->renderer->renderWidget('foo', [])
        );
    }

    /**
     * @param string $symfonyLocale
     * @param string $ckEditorLocale
     *
     * @dataProvider languageProvider
     */
    public function testRenderWidgetWithExplicitLanguage($symfonyLocale, $ckEditorLocale)
    {
        $this->assertSame(
            'CKEDITOR.replace("foo", {"language":"'.$ckEditorLocale.'"});',
            $this->renderer->renderWidget('foo', ['language' => $symfonyLocale])
        );
    }

    public function testRenderWidgetWithoutLocale()
    {
        $this->container
            ->expects($this->once())
            ->method('hasParameter')
            ->with($this->identicalTo('locale'))
            ->will($this->returnValue(false));

        $this->assertSame(
            'CKEDITOR.replace("foo", []);',
            $this->renderer->renderWidget('foo', [])
        );
    }

    /**
     * @param string $path
     * @param string $asset
     * @param string $url
     *
     * @dataProvider fileAssetProvider
     */
    public function testRenderWidgetWithStringContentsCss($path, $asset, $url)
    {
        $this->packages
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo($path))
            ->will($this->returnValue($asset));

        $this->assertSame(
            'CKEDITOR.replace("foo", {"contentsCss":["'.$url.'"]});',
            $this->renderer->renderWidget('foo', ['contentsCss' => $path])
        );
    }

    /**
     * @param string[] $paths
     * @param string[] $assets
     * @param string[] $urls
     *
     * @dataProvider filesAssetProvider
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
            $this->renderer->renderWidget('foo', ['contentsCss' => $paths])
        );
    }

    /**
     * @param string $filebrowser
     *
     * @dataProvider filebrowserProvider
     */
    public function testRenderWidgetWithFileBrowser($filebrowser)
    {
        $this->assertSame(
            'CKEDITOR.replace("foo", {"filebrowser'.$filebrowser.'Url":"'.($url = 'browse_url').'"});',
            $this->renderer->renderWidget('foo', ['filebrowser'.$filebrowser.'Url' => $url])
        );
    }

    public function testRenderWidgetWithCustomFileBrowser()
    {
        $this->assertSame(
            'CKEDITOR.replace("foo", {"filebrowser'.($filebrowser = 'VideoBrowse').'Url":"'.($url = 'browse_url').'"});',
            $this->renderer->renderWidget(
                'foo',
                ['filebrowser'.$filebrowser.'Url' => $url],
                ['filebrowsers'                   => [$filebrowser]]
            )
        );
    }

    /**
     * @param string $filebrowser
     *
     * @dataProvider filebrowserProvider
     */
    public function testRenderWidgetWithMinimalRouteFileBrowser($filebrowser)
    {
        $this->router
            ->expects($this->once())
            ->method('generate')
            ->with(
                $this->identicalTo($route = 'browse_route'),
                $this->identicalTo([]),
                $this->identicalTo(UrlGeneratorInterface::ABSOLUTE_PATH)
            )
            ->will($this->returnValue('browse_url'));

        $this->assertSame(
            'CKEDITOR.replace("foo", {"filebrowser'.$filebrowser.'Url":"browse_url"});',
            $this->renderer->renderWidget('foo', ['filebrowser'.$filebrowser.'Route' => $route])
        );
    }

    /**
     * @param string $filebrowser
     *
     * @dataProvider filebrowserProvider
     */
    public function testRenderWidgetWithMaximalRouteFileBrowser($filebrowser)
    {
        $this->router
            ->expects($this->once())
            ->method('generate')
            ->with(
                $this->identicalTo($route = 'browse_route'),
                $this->identicalTo($routeParameters = ['foo' => 'bar']),
                $this->identicalTo($routeType = UrlGeneratorInterface::ABSOLUTE_URL)
            )
            ->will($this->returnValue('browse_url'));

        $this->assertSame(
            'CKEDITOR.replace("foo", {"filebrowser'.$filebrowser.'Url":"browse_url"});',
            $this->renderer->renderWidget('foo', [
                'filebrowser'.$filebrowser.'Route'           => $route,
                'filebrowser'.$filebrowser.'RouteParameters' => $routeParameters,
                'filebrowser'.$filebrowser.'RouteType'       => $routeType,
            ])
        );
    }

    /**
     * @param string $filebrowser
     *
     * @dataProvider filebrowserProvider
     */
    public function testRenderWidgetWithRouteFileBrowserHandler($filebrowser)
    {
        $this->router
            ->expects($this->once())
            ->method('generate')
            ->with(
                $this->equalTo('browse_route'),
                $this->equalTo(['foo' => 'bar']),
                $this->equalTo(UrlGeneratorInterface::ABSOLUTE_URL)
            )
            ->will($this->returnValue('browse_url'));

        $this->assertSame(
            'CKEDITOR.replace("foo", {"filebrowser'.$filebrowser.'Url":"browse_url"});',
            $this->renderer->renderWidget('foo', [
                'filebrowser'.$filebrowser.'Handler' => function (RouterInterface $router) {
                    return $router->generate('browse_route', ['foo' => 'bar'], UrlGeneratorInterface::ABSOLUTE_URL);
                },
            ])
        );
    }

    public function testRenderWidgetWithProtectedSource()
    {
        $this->assertSame(
            'CKEDITOR.replace("foo", {"protectedSource":[/<\?[\s\S]*?\?>/g,/<%[\s\S]*?%>/g]});',
            $this->renderer->renderWidget('foo', [
                'protectedSource' => [
                    '/<\?[\s\S]*?\?>/g',
                    '/<%[\s\S]*?%>/g',
                ],
            ])
        );
    }

    public function testRenderWidgetWithStylesheetParserSkipSelectors()
    {
        $this->assertSame(
            'CKEDITOR.replace("foo", {"stylesheetParser_skipSelectors":/(^body\.|^caption\.|\.high|^\.)/i});',
            $this->renderer->renderWidget('foo', [
                'stylesheetParser_skipSelectors' => '/(^body\.|^caption\.|\.high|^\.)/i',
            ])
        );
    }

    public function testRenderWidgetWithStylesheetParserValidSelectors()
    {
        $this->assertSame(
            'CKEDITOR.replace("foo", {"stylesheetParser_validSelectors":/\^(p|span)\.\w+/});',
            $this->renderer->renderWidget('foo', [
                'stylesheetParser_validSelectors' => '/\^(p|span)\.\w+/',
            ])
        );
    }

    public function testRenderWidgetWithCKEditorConstants()
    {
        $this->assertSame(
            'CKEDITOR.replace("foo", {"config":{"enterMode":CKEDITOR.ENTER_BR,"shiftEnterMode":CKEDITOR.ENTER_BR}});',
            $this->renderer->renderWidget('foo', [
                'config' => [
                    'enterMode'      => 'CKEDITOR.ENTER_BR',
                    'shiftEnterMode' => 'CKEDITOR.ENTER_BR',
                ],
            ])
        );
    }

    public function testRenderWidgetWithoutAutoInline()
    {
        $this->assertSame(
            'CKEDITOR.disableAutoInline = true;'."\n".'CKEDITOR.replace("foo", []);',
            $this->renderer->renderWidget('foo', [], ['auto_inline' => false])
        );
    }

    public function testRenderWidgetWithInline()
    {
        $this->assertSame(
            'CKEDITOR.inline("foo", []);',
            $this->renderer->renderWidget('foo', [], ['inline' => true])
        );
    }

    public function testRenderWidgetWithInputSync()
    {
        $this->assertSame(
            'var ivory_ckeditor_foo = CKEDITOR.replace("foo", []);'."\n".
            'ivory_ckeditor_foo.on(\'change\', function() { ivory_ckeditor_foo.updateElement(); });',
            $this->renderer->renderWidget('foo', [], ['input_sync' => true])
        );
    }

    public function testRenderDestroy()
    {
        $this->assertSame(
            'if (CKEDITOR.instances["foo"]) { '.
            'CKEDITOR.instances["foo"].destroy(true); '.
            'delete CKEDITOR.instances["foo"]; '.
            '}',
            $this->renderer->renderDestroy('foo')
        );
    }

    /**
     * @param string $path
     * @param string $asset
     * @param string $url
     *
     * @dataProvider directoryAssetProvider
     */
    public function testRenderPlugin($path, $asset, $url)
    {
        $this->packages
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo($path))
            ->will($this->returnValue($asset));

        $this->assertSame(
            'CKEDITOR.plugins.addExternal("foo", "'.$url.'", "bat");',
            $this->renderer->renderPlugin('foo', ['path' => $path, 'filename' => 'bat'])
        );
    }

    public function testRenderStylesSet()
    {
        $this->assertSame(
            'if (CKEDITOR.stylesSet.get("foo") === null) { CKEDITOR.stylesSet.add("foo", {"foo":"bar"}); }',
            $this->renderer->renderStylesSet('foo', ['foo' => 'bar'])
        );
    }

    /**
     * @param string $path
     * @param string $asset
     * @param string $url
     *
     * @dataProvider directoryAssetProvider
     */
    public function testRenderTemplate($path, $asset, $url)
    {
        $templates = [
            [
                'title' => 'Template title',
                'html'  => '<p>Template content</p>',
            ],
        ];

        $this->packages
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo($path))
            ->will($this->returnValue($asset));

        $json = json_encode(['imagesPath' => $url, 'templates' => $templates]);

        $this->assertSame(
            'CKEDITOR.addTemplates("foo", '.$json.');',
            $this->renderer->renderTemplate('foo', ['imagesPath' => $path, 'templates' => $templates])
        );
    }

    /**
     * @param string $path
     * @param string $asset
     * @param string $url
     *
     * @dataProvider directoryAssetProvider
     */
    public function testRenderTemplateWithTwigTemplating($path, $asset, $url)
    {
        $templates = [
            [
                'title'               => 'Template title',
                'template'            => $template = 'template_name',
                'template_parameters' => $templateParameters = ['foo' => 'bar'],
            ],
        ];

        $processedTemplates = [
            [
                'title' => 'Template title',
                'html'  => $html = '<p>Template content</p>',
            ],
        ];

        $this->packages
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo($path))
            ->will($this->returnValue($asset));

        $this->container
            ->expects($this->once())
            ->method('has')
            ->with($this->identicalTo('twig'))
            ->will($this->returnValue(true));

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with($this->identicalTo($template), $this->identicalTo($templateParameters))
            ->will($this->returnValue($html));

        $json = json_encode(['imagesPath' => $url, 'templates'  => $processedTemplates]);

        $this->assertSame(
            'CKEDITOR.addTemplates("foo", '.$json.');',
            $this->renderer->renderTemplate('foo', ['imagesPath' => $path, 'templates' => $templates])
        );
    }

    /**
     * @param string $path
     * @param string $asset
     * @param string $url
     *
     * @dataProvider directoryAssetProvider
     */
    public function testRenderTemplateWithPhpTemplating($path, $asset, $url)
    {
        $templates = [
            [
                'title'    => 'Template title',
                'template' => $template = 'template_name',
            ],
        ];

        $processedTemplates = [
            [
                'title' => 'Template title',
                'html'  => $html = '<p>Template content</p>',
            ],
        ];

        $this->packages
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo($path))
            ->will($this->returnValue($asset));

        $this->container
            ->expects($this->once())
            ->method('has')
            ->with($this->identicalTo('twig'))
            ->will($this->returnValue(false));

        $this->templating
            ->expects($this->once())
            ->method('render')
            ->with($this->identicalTo($template), $this->identicalTo([]))
            ->will($this->returnValue($html));

        $json = json_encode(['imagesPath' => $url, 'templates'  => $processedTemplates]);

        $this->assertSame(
            'CKEDITOR.addTemplates("foo", '.$json.');',
            $this->renderer->renderTemplate('foo', ['imagesPath' => $path, 'templates' => $templates])
        );
    }

    /**
     * @return array
     */
    public function languageProvider()
    {
        return [
            ['en', 'en'],
            ['pt_BR', 'pt-br'],
        ];
    }

    /**
     * @return array
     */
    public function directoryAssetProvider()
    {
        return [
            ['directory/', 'url/', 'url/'],
            ['directory/', 'url/?v=2', 'url/'],
        ];
    }

    /**
     * @return array
     */
    public function fileAssetProvider()
    {
        return [
            ['file.js', 'url.js', 'url.js'],
            ['file.js', 'url.js?v=2', 'url.js?v=2'],
        ];
    }

    /**
     * @return array
     */
    public function filesAssetProvider()
    {
        return [
            [['file'], ['url'], ['url']],
            [['file'], ['url?v=2'], ['url?v=2']],
            [['file1', 'file2'], ['url1', 'url2'], ['url1', 'url2']],
            [['file1', 'file2'], ['url1?v=2', 'url2'], ['url1?v=2', 'url2']],
            [['file1', 'file2'], ['url1', 'url2?v=2'], ['url1', 'url2?v=2']],
            [['file1', 'file2'], ['url1?v=2', 'url2?v=2'], ['url1?v=2', 'url2?v=2']],
        ];
    }

    /**
     * @return array
     */
    public function filebrowserProvider()
    {
        return [
            ['Browse'],
            ['FlashBrowse'],
            ['ImageBrowse'],
            ['ImageBrowseLink'],
            ['Upload'],
            ['FlashUpload'],
            ['ImageUpload'],
        ];
    }
}
