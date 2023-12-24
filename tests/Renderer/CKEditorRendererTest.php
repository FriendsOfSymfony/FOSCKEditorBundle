<?php

/*
 * This file is part of the FOSCKEditor Bundle.
 *
 * (c) 2018 - present  Friends of Symfony
 * (c) 2009 - 2017     Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\CKEditorBundle\Tests\Renderer;

use FOS\CKEditorBundle\Builder\JsonBuilder;
use FOS\CKEditorBundle\Renderer\CKEditorRenderer;
use FOS\CKEditorBundle\Renderer\CKEditorRendererInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CKEditorRendererTest extends TestCase
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
     * @var Environment|\PHPUnit_Framework_MockObject_MockObject
     */
    private $twig;

    protected function setUp(): void
    {
        $this->request = $this->createMock(Request::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->packages = $this->getMockBuilder(Packages::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestStack = $this->createMock(RequestStack::class);
        $this->requestStack->expects($this->any())->method('getCurrentRequest')->will($this->returnValue($this->request));
        $this->twig = $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->renderer = new CKEditorRenderer(new JsonBuilder(new PropertyAccessor()), $this->router, $this->packages, $this->requestStack, $this->twig);
    }

    public function testDefaultState(): void
    {
        $this->assertInstanceOf(CKEditorRendererInterface::class, $this->renderer);
    }

    /**
     * @dataProvider directoryAssetProvider
     */
    public function testRenderBasePath(string $path, string $asset, string $url): void
    {
        $this->packages
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo($path))
            ->will($this->returnValue($asset));

        $this->assertSame($url, $this->renderer->renderBasePath($path));
    }

    public function testRenderJsPath(): void
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
    public function testRenderWidgetWithLocaleRequest(string $symfonyLocale, string $ckEditorLocale): void
    {
        $this->request
            ->expects($this->exactly(2))
            ->method('getLocale')
            ->will($this->returnValue($symfonyLocale));

        $this->assertSame(
            'CKEDITOR.replace("foo", {"language":"'.$ckEditorLocale.'"});',
            $this->renderer->renderWidget('foo', [])
        );
    }

    /**
     * @dataProvider languageProvider
     */
    public function testRenderWidgetWithLocaleParameter(string $symfonyLocale, string $ckEditorLocale): void
    {
        $this->request->expects($this->exactly(2))->method('getLocale')->will($this->returnValue($symfonyLocale));
        $this->assertSame(
            'CKEDITOR.replace("foo", {"language":"'.$ckEditorLocale.'"});',
            $this->renderer->renderWidget('foo', [])
        );
    }

    /**
     * @dataProvider languageProvider
     */
    public function testRenderWidgetWithExplicitLanguage(string $symfonyLocale, string $ckEditorLocale): void
    {
        $this->assertSame(
            'CKEDITOR.replace("foo", {"language":"'.$ckEditorLocale.'"});',
            $this->renderer->renderWidget('foo', ['language' => $symfonyLocale])
        );
    }

    public function testRenderWidgetWithoutLocale(): void
    {
        $this->request
            ->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue(''));

        $this->assertSame(
            'CKEDITOR.replace("foo", []);',
            $this->renderer->renderWidget('foo', [])
        );
    }

    /**
     * @dataProvider fileAssetProvider
     */
    public function testRenderWidgetWithStringContentsCss(string $path, string $asset, string $url): void
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
    public function testRenderWidgetWithArrayContentsCss(array $paths, array $assets, array $urls): void
    {
        $callMap = [];
        foreach (array_keys($paths) as $key) {
            $callMap[] = [$paths[$key], null, $assets[$key]];
        }

        $this->packages
            ->expects($this->exactly(count($paths)))
            ->method('getUrl')
            ->willReturnMap($callMap);

        $this->assertSame(
            'CKEDITOR.replace("foo", {"contentsCss":'.json_encode($urls).'});',
            $this->renderer->renderWidget('foo', ['contentsCss' => $paths])
        );
    }

    /**
     * @dataProvider filebrowserProvider
     */
    public function testRenderWidgetWithFileBrowser(string $filebrowser): void
    {
        $this->assertSame(
            'CKEDITOR.replace("foo", {"filebrowser'.$filebrowser.'Url":"'.($url = 'browse_url').'"});',
            $this->renderer->renderWidget('foo', ['filebrowser'.$filebrowser.'Url' => $url])
        );
    }

    public function testRenderWidgetWithCustomFileBrowser(): void
    {
        $this->assertSame(
            'CKEDITOR.replace("foo", {"filebrowser'.($filebrowser = 'VideoBrowse').'Url":"'.($url = 'browse_url').'"});',
            $this->renderer->renderWidget(
                'foo',
                ['filebrowser'.$filebrowser.'Url' => $url],
                ['filebrowsers' => [$filebrowser]]
            )
        );
    }

    /**
     * @dataProvider filebrowserProvider
     */
    public function testRenderWidgetWithMinimalRouteFileBrowser(string $filebrowser): void
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
     * @dataProvider filebrowserProvider
     */
    public function testRenderWidgetWithMaximalRouteFileBrowser(string $filebrowser): void
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
                'filebrowser'.$filebrowser.'Route' => $route,
                'filebrowser'.$filebrowser.'RouteParameters' => $routeParameters,
                'filebrowser'.$filebrowser.'RouteType' => $routeType,
            ])
        );
    }

    /**
     * @dataProvider filebrowserProvider
     */
    public function testRenderWidgetWithRouteFileBrowserHandler(string $filebrowser): void
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

    public function testRenderWidgetWithProtectedSource(): void
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

    public function testRenderWidgetWithStylesheetParserSkipSelectors(): void
    {
        $this->assertSame(
            'CKEDITOR.replace("foo", {"stylesheetParser_skipSelectors":/(^body\.|^caption\.|\.high|^\.)/i});',
            $this->renderer->renderWidget('foo', [
                'stylesheetParser_skipSelectors' => '/(^body\.|^caption\.|\.high|^\.)/i',
            ])
        );
    }

    public function testRenderWidgetWithStylesheetParserValidSelectors(): void
    {
        $this->assertSame(
            'CKEDITOR.replace("foo", {"stylesheetParser_validSelectors":/\^(p|span)\.\w+/});',
            $this->renderer->renderWidget('foo', [
                'stylesheetParser_validSelectors' => '/\^(p|span)\.\w+/',
            ])
        );
    }

    public function testRenderWidgetWithCKEditorConstants(): void
    {
        $this->assertSame(
            'CKEDITOR.replace("foo", {"config":{"enterMode":CKEDITOR.ENTER_BR,"shiftEnterMode":CKEDITOR.ENTER_BR}});',
            $this->renderer->renderWidget('foo', [
                'config' => [
                    'enterMode' => 'CKEDITOR.ENTER_BR',
                    'shiftEnterMode' => 'CKEDITOR.ENTER_BR',
                ],
            ])
        );
    }

    public function testRenderWidgetWithoutAutoInline(): void
    {
        $this->assertSame(
            'CKEDITOR.disableAutoInline = true;'."\n".'CKEDITOR.replace("foo", []);',
            $this->renderer->renderWidget('foo', [], ['auto_inline' => false])
        );
    }

    public function testRenderWidgetWithInline(): void
    {
        $this->assertSame(
            'CKEDITOR.inline("foo", []);',
            $this->renderer->renderWidget('foo', [], ['inline' => true])
        );
    }

    public function testRenderWidgetWithInputSync(): void
    {
        $this->assertSame(
            'var fos_ckeditor_foo = CKEDITOR.replace("foo", []);'."\n".
            'fos_ckeditor_foo.on(\'change\', function() { fos_ckeditor_foo.updateElement(); });',
            $this->renderer->renderWidget('foo', [], ['input_sync' => true])
        );
    }

    public function testRenderDestroy(): void
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
     * @dataProvider directoryAssetProvider
     */
    public function testRenderPlugin(string $path, string $asset, string $url): void
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
     * @dataProvider directoryAssetProvider
     */
    public function testRenderTemplate(string $path, string $asset, string $url): void
    {
        $templates = [
            [
                'title' => 'Template title',
                'html' => '<p>Template content</p>',
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

    public function languageProvider(): array
    {
        return [
            ['en', 'en'],
            ['pt_BR', 'pt-br'],
        ];
    }

    public function directoryAssetProvider(): array
    {
        return [
            ['directory/', 'url/', 'url/'],
            ['directory/', 'url/?v=2', 'url/'],
        ];
    }

    public function fileAssetProvider(): array
    {
        return [
            ['file.js', 'url.js', 'url.js'],
            ['file.js', 'url.js?v=2', 'url.js?v=2'],
        ];
    }

    public function filesAssetProvider(): array
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

    public function filebrowserProvider(): array
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
