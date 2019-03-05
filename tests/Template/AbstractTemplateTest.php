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

namespace FOS\CKEditorBundle\Tests\Template;

use FOS\CKEditorBundle\Builder\JsonBuilder;
use FOS\CKEditorBundle\Renderer\CKEditorRenderer;
use FOS\CKEditorBundle\Renderer\CKEditorRendererInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * @author GeLo <geloen.eric@gmail.com>
 * @author Adam Misiorny <adam.misiorny@gmail.com>
 */
abstract class AbstractTemplateTest extends TestCase
{
    /**
     * @var CKEditorRenderer
     */
    protected $renderer;

    /**
     * @var Packages|MockObject
     */
    private $packages;

    /**
     * @var RequestStack|MockObject
     */
    private $requestStack;

    /**
     * @var RouterInterface|MockObject
     */
    private $router;

    /**
     * @var Environment|MockObject
     */
    private $twig;

    /**
     * @var Request|MockObject
     */
    private $request;

    protected function setUp(): void
    {
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->request = $this->createMock(Request::class);
        $this->requestStack->expects($this->any())->method('getCurrentRequest')->will($this->returnValue($this->request));
        $this->router = $this->createMock(RouterInterface::class);
        $this->packages = $this->createMock(Packages::class);
        $this->packages
            ->expects($this->any())
            ->method('getUrl')
            ->will($this->returnArgument(0));
        $this->twig = $this->createMock(Environment::class);

        $this->renderer = new CKEditorRenderer(new JsonBuilder(new PropertyAccessor()), $this->router, $this->packages, $this->requestStack, $this->twig);
    }

    public function testDefaultState(): void
    {
        $this->assertInstanceOf(CKEditorRendererInterface::class, $this->renderer);
    }

    public function testRenderWithSimpleWidget(): void
    {
        $expected = <<<'EOF'
<textarea >&lt;p&gt;value&lt;/p&gt;</textarea>
<script type="text/javascript">
    var CKEDITOR_BASEPATH = "base_path";
</script>
<script type="text/javascript" src="js_path"></script>
<script type="text/javascript">
    if (CKEDITOR.instances["id"]) {
        CKEDITOR.instances["id"].destroy(true);
        delete CKEDITOR.instances["id"];
    }

    CKEDITOR.replace("id", []);
</script>

EOF;

        $this->assertTemplate($expected, $this->getContext());
    }

    public function testRenderWithFullWidget(): void
    {
        $context = [
            'auto_inline' => false,
            'inline' => true,
            'input_sync' => true,
            'config' => ['foo' => 'bar'],
            'plugins' => [
                'foo' => ['path' => 'path', 'filename' => 'filename'],
            ],
            'styles' => [
                'default' => [
                    ['name' => 'Blue Title', 'element' => 'h2', 'styles' => ['color' => 'Blue']],
                ],
            ],
            'templates' => [
                'foo' => [
                    'imagesPath' => 'path',
                    'templates' => [
                        [
                            'title' => 'My Template',
                            'html' => '<h1>Template</h1>',
                        ],
                    ],
                ],
            ],
        ];

        $expected = <<<EOF
<textarea >&lt;p&gt;value&lt;/p&gt;</textarea>
<script type="text/javascript">
    var CKEDITOR_BASEPATH = "base_path";
</script>
<script type="text/javascript" src="js_path"></script>
<script type="text/javascript">
    if (CKEDITOR.instances["id"]) {
        CKEDITOR.instances["id"].destroy(true);
        delete CKEDITOR.instances["id"];
    }

    CKEDITOR.plugins.addExternal("foo", "path", "filename");

    if (CKEDITOR.stylesSet.get("default") === null) {
        CKEDITOR.stylesSet.add("default", [{
            "name": "Blue Title",
            "element": "h2",
            "styles": {
                "color": "Blue"
            }
        }]);
    }

    CKEDITOR.addTemplates("foo", {
        "imagesPath": "path",
        "templates": [{
            "title": "My Template",
            "html": "<h1>Template<\/h1>"
        }]
    });

    CKEDITOR.disableAutoInline = true;

    var fos_ckeditor_id = CKEDITOR.inline("id", {"foo": "bar"});

    fos_ckeditor_id.on('change', function() {
        fos_ckeditor_id.updateElement();
    });
</script>
EOF;

        $this->assertTemplate($expected, array_merge($this->getContext(), $context));
    }

    public function testRenderWithJQuery(): void
    {
        $expected = <<<'EOF'
<textarea >&lt;p&gt;value&lt;/p&gt;</textarea>
<script type="text/javascript">
    var CKEDITOR_BASEPATH = "base_path";
</script>
<script type="text/javascript" src="js_path"></script>
<script type="text/javascript" src="jquery_path"></script>
<script type="text/javascript">
    $(function () {
        if (CKEDITOR.instances["id"]) {
            CKEDITOR.instances["id"].destroy(true);
            delete CKEDITOR.instances["id"];
        }

        CKEDITOR.replace("id", []);
    });
</script>
EOF;

        $this->assertTemplate($expected, array_merge($this->getContext(), ['jquery' => true]));
    }

    public function testRenderWithRequireJs(): void
    {
        $expected = <<<'EOF'
<textarea >&lt;p&gt;value&lt;/p&gt;</textarea>
<script type="text/javascript">
    var CKEDITOR_BASEPATH = "base_path";
</script>
<script type="text/javascript" src="js_path"></script>
<script type="text/javascript">
    require(['ckeditor'], function() {
        if (CKEDITOR.instances["id"]) {
            CKEDITOR.instances["id"].destroy(true);
            delete CKEDITOR.instances["id"];
        }

        CKEDITOR.replace("id", []);
    });
</script>
EOF;

        $this->assertTemplate($expected, array_merge($this->getContext(), ['require_js' => true]));
    }

    public function testRenderWithNotAutoloadedWidget(): void
    {
        $expected = <<<'EOF'
<textarea >&lt;p&gt;value&lt;/p&gt;</textarea>
<script type="text/javascript">
    if (CKEDITOR.instances["id"]) {
        CKEDITOR.instances["id"].destroy(true);
        delete CKEDITOR.instances["id"];
    }

    CKEDITOR.replace("id", []);
</script>
EOF;

        $this->assertTemplate($expected, array_merge($this->getContext(), ['autoload' => false]));
    }

    public function testRenderWithDisableWidget(): void
    {
        $this->assertTemplate(
            '<textarea >&lt;p&gt;value&lt;/p&gt;</textarea>',
            array_merge($this->getContext(), ['enable' => false])
        );
    }

    abstract protected function renderTemplate(array $context = []): string;

    private function getContext(): array
    {
        return [
            'form' => $this->createMock(FormView::class),
            'id' => 'id',
            'value' => '<p>value</p>',
            'enable' => true,
            'async' => false,
            'autoload' => true,
            'auto_inline' => true,
            'inline' => false,
            'jquery' => false,
            'input_sync' => false,
            'require_js' => false,
            'base_path' => 'base_path',
            'js_path' => 'js_path',
            'jquery_path' => 'jquery_path',
            'filebrowsers' => [],
            'config' => [],
            'plugins' => [],
            'styles' => [],
            'templates' => [],
        ];
    }

    private function assertTemplate(string $expected, array $context): void
    {
        $this->assertSame($this->normalizeOutput($expected), $this->normalizeOutput($this->renderTemplate($context)));
    }

    private function normalizeOutput(string $output): string
    {
        $mapping = [
            "\n" => '',
            '  ' => '',
            '{ ' => '{',
            ': ' => ':',
            '; ' => ';',
        ];

        return str_replace(array_keys($mapping), array_values($mapping), $output);
    }
}
