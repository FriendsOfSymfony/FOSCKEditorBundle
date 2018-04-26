<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Tests\Template;

use Ivory\CKEditorBundle\Renderer\CKEditorRenderer;
use Ivory\CKEditorBundle\Renderer\CKEditorRendererInterface;
use Ivory\CKEditorBundle\Tests\AbstractTestCase;
use Ivory\JsonBuilder\JsonBuilder;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 * @author Adam Misiorny <adam.misiorny@gmail.com>
 */
abstract class AbstractTemplateTest extends AbstractTestCase
{
    /**
     * @var CKEditorRenderer
     */
    protected $renderer;

    /**
     * @var ContainerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $container;

    /**
     * @var Packages|\PHPUnit_Framework_MockObject_MockObject
     */
    private $packages;

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
     * @var Request|\PHPUnit_Framework_MockObject_MockObject
     */
    private $request;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
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
        $this->templating = $this->createMock(EngineInterface::class);

        $this->renderer = new CKEditorRenderer(new JsonBuilder(), $this->router, $this->packages, $this->requestStack, $this->templating);
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf(CKEditorRendererInterface::class, $this->renderer);
    }

    public function testRenderWithSimpleWidget()
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

    public function testRenderWithFullWidget()
    {
        $context = [
            'auto_inline' => false,
            'inline'      => true,
            'input_sync'  => true,
            'config'      => ['foo' => 'bar'],
            'plugins'     => [
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
                    'templates'  => [
                        [
                            'title' => 'My Template',
                            'html'  => '<h1>Template</h1>',
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

    var ivory_ckeditor_id = CKEDITOR.inline("id", {"foo": "bar"});

    ivory_ckeditor_id.on('change', function() {
        ivory_ckeditor_id.updateElement();
    });
</script>
EOF;

        $this->assertTemplate($expected, array_merge($this->getContext(), $context));
    }

    public function testRenderWithJQuery()
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

    public function testRenderWithRequireJs()
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

    public function testRenderWithNotAutoloadedWidget()
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

    public function testRenderWithDisableWidget()
    {
        $this->assertTemplate(
            '<textarea >&lt;p&gt;value&lt;/p&gt;</textarea>',
            array_merge($this->getContext(), ['enable' => false])
        );
    }

    /**
     * @param array $context
     *
     * @return string
     */
    abstract protected function renderTemplate(array $context = []);

    /**
     * @return array
     */
    private function getContext()
    {
        return [
            'form'         => $this->createMock(FormView::class),
            'id'           => 'id',
            'value'        => '<p>value</p>',
            'enable'       => true,
            'async'        => false,
            'autoload'     => true,
            'auto_inline'  => true,
            'inline'       => false,
            'jquery'       => false,
            'input_sync'   => false,
            'require_js'   => false,
            'base_path'    => 'base_path',
            'js_path'      => 'js_path',
            'jquery_path'  => 'jquery_path',
            'filebrowsers' => [],
            'config'       => [],
            'plugins'      => [],
            'styles'       => [],
            'templates'    => [],
        ];
    }

    /**
     * @param string $expected
     * @param array  $context
     */
    private function assertTemplate($expected, array $context)
    {
        $this->assertSame($this->normalizeOutput($expected), $this->normalizeOutput($this->renderTemplate($context)));
    }

    /**
     * @param string $output
     *
     * @return string
     */
    private function normalizeOutput($output)
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
