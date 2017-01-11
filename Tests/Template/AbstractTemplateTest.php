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
use Ivory\CKEditorBundle\Tests\AbstractTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Abstract template test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 * @author Adam Misiorny <adam.misiorny@gmail.com>
 */
abstract class AbstractTemplateTest extends AbstractTestCase
{
    /** @var \Ivory\CKEditorBundle\Renderer\CKEditorRenderer */
    protected $renderer;

    /** @var \Symfony\Component\DependencyInjection\ContainerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $containerMock;

    /** @var \Symfony\Component\Asset\Packages|\Symfony\Component\Templating\Helper\CoreAssetsHelper|\PHPUnit_Framework_MockObject_MockObject */
    private $assetsHelperMock;

    /** @var \Symfony\Component\Routing\RouterInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $routerMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->routerMock = $this->createMock('Symfony\Component\Routing\RouterInterface');

        if (class_exists('Symfony\Component\Asset\Packages')) {
            $this->assetsHelperMock = $this->getMockBuilder('Symfony\Component\Asset\Packages')
                ->disableOriginalConstructor()
                ->getMock();
        } else {
            $this->assetsHelperMock = $this->getMockBuilder('Symfony\Component\Templating\Helper\CoreAssetsHelper')
                ->disableOriginalConstructor()
                ->getMock();
        }

        $this->assetsHelperMock
            ->expects($this->any())
            ->method('getUrl')
            ->will($this->returnArgument(0));

        $this->containerMock = $this->createMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->containerMock
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                array(
                    'assets.packages',
                    ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE,
                    $this->assetsHelperMock,
                ),
                array(
                    'router',
                    ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE,
                    $this->routerMock,
                ),
            )));

        $this->renderer = new CKEditorRenderer($this->containerMock);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->routerMock);
        unset($this->assetsHelperMock);
        unset($this->containerMock);
        unset($this->renderer);
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf('Ivory\CKEditorBundle\Renderer\CKEditorRendererInterface', $this->renderer);
    }

    public function testRenderWithSimpleWidget()
    {
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
CKEDITOR.replace("id", []);
</script>

EOF;

        $this->assertTemplate($expected, $this->getContext());
    }

    public function testRenderWithFullWidget()
    {
        $context = array(
            'auto_inline' => false,
            'inline' => true,
            'input_sync' => true,
            'config' => array('foo' => 'bar'),
            'plugins' => array(
                'foo' => array('path' => 'path', 'filename' => 'filename'),
            ),
            'styles' => array(
                'default' => array(
                    array('name' => 'Blue Title', 'element' => 'h2', 'styles' => array('color' => 'Blue')),
                ),
            ),
            'templates' => array(
                'foo' => array(
                    'imagesPath' => 'path',
                    'templates' => array(
                        array(
                            'title' => 'My Template',
                            'html' => '<h1>Template</h1>',
                        ),
                    ),
                ),
            ),
        );

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
if (CKEDITOR.stylesSet.get("default") === null) { CKEDITOR.stylesSet.add("default", [{"name":"Blue Title","element":"h2","styles":{"color":"Blue"}}]); }
CKEDITOR.addTemplates("foo", {"imagesPath":"path","templates":[{"title":"My Template","html":"<h1>Template<\/h1>"}]});
CKEDITOR.disableAutoInline = true;
var ivory_ckeditor_id = CKEDITOR.inline("id", {"foo":"bar"});
ivory_ckeditor_id.on('change', function(){ ivory_ckeditor_id.updateElement(); });
</script>

EOF;

        $this->assertTemplate($expected, array_merge($this->getContext(), $context));
    }

    public function testRenderWithJQuery()
    {
        $expected = <<<EOF
<textarea>&lt;p&gt;value&lt;/p&gt;</textarea>
<script type="text/javascript">
var CKEDITOR_BASEPATH="base_path";
</script>
<script type="text/javascript" src="js_path"></script>
<script type="text/javascript" src="jquery_path"></script>
<script type="text/javascript">
$(function() {
if (CKEDITOR.instances["id"]) {
CKEDITOR.instances["id"].destroy(true);
deleteCKEDITOR.instances["id"];
}
CKEDITOR.replace("id",[]);
});
</script>
EOF;

        $this->assertTemplate($expected, array_merge($this->getContext(), array('jquery' => true)));
    }

    public function testRenderWithRequireJs()
    {
        $expected = <<<EOF
<textarea>&lt;p&gt;value&lt;/p&gt;</textarea>
<script type="text/javascript">
var CKEDITOR_BASEPATH = "base_path";
</script>
<script type="text/javascript" src="js_path"></script>
<script type="text/javascript">
require(['ckeditor'], function() {
if (CKEDITOR.instances["id"]) {
CKEDITOR.instances["id"].destroy(true);
deleteCKEDITOR.instances["id"];
}
CKEDITOR.replace("id",[]);
});
</script>
EOF;

        $this->assertTemplate($expected, array_merge($this->getContext(), array('require_js' => true)));
    }

    public function testRenderWithNotAutoloadedWidget()
    {
        $expected = <<<EOF
<textarea >&lt;p&gt;value&lt;/p&gt;</textarea>
<script type="text/javascript">
if (CKEDITOR.instances["id"]) {
CKEDITOR.instances["id"].destroy(true);
delete CKEDITOR.instances["id"];
}
CKEDITOR.replace("id", []);
</script>

EOF;

        $this->assertTemplate($expected, array_merge($this->getContext(), array('autoload' => false)));
    }

    public function testRenderWithDisableWidget()
    {
        $expected = <<<EOF
<textarea >&lt;p&gt;value&lt;/p&gt;</textarea>

EOF;

        $this->assertTemplate($expected, array_merge($this->getContext(), array('enable' => false)));
    }

    /**
     * Renders a template.
     *
     * @param array $context The template context.
     *
     * @return string The template output.
     */
    abstract protected function renderTemplate(array $context = array());

    /**
     * Gets the context.
     *
     * @return array The context.
     */
    private function getContext()
    {
        return array(
            'form'         => $this->createMock('Symfony\Component\Form\FormView'),
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
            'filebrowsers' => array(),
            'config'       => array(),
            'plugins'      => array(),
            'styles'       => array(),
            'templates'    => array(),
        );
    }

    /**
     * Asserts a template.
     *
     * @param string $expected The expected template.
     * @param array  $context  The context.
     */
    private function assertTemplate($expected, array $context)
    {
        $this->assertSame($this->normalizeOutput($expected), $this->normalizeOutput($this->renderTemplate($context)));
    }

    /**
     * Normalizes the output by removing the heading whitespaces.
     *
     * @param string $output The output.
     *
     * @return string The normalized output.
     */
    private function normalizeOutput($output)
    {
        return str_replace(PHP_EOL, '', str_replace(' ', '', $output));
    }
}
