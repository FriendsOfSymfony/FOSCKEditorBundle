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
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\Helper\CoreAssetsHelper;

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
     * @var Packages|CoreAssetsHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $packages;

    /**
     * @var RouterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $router;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->router = $this->createMock('Symfony\Component\Routing\RouterInterface');

        if (class_exists('Symfony\Component\Asset\Packages')) {
            $this->packages = $this->getMockBuilder('Symfony\Component\Asset\Packages')
                ->disableOriginalConstructor()
                ->getMock();
        } else {
            $this->packages = $this->getMockBuilder('Symfony\Component\Templating\Helper\CoreAssetsHelper')
                ->disableOriginalConstructor()
                ->getMock();
        }

        $this->packages
            ->expects($this->any())
            ->method('getUrl')
            ->will($this->returnArgument(0));

        $this->container = $this->createMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->container
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                array(
                    'assets.packages',
                    ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE,
                    $this->packages,
                ),
                array(
                    'router',
                    ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE,
                    $this->router,
                ),
            )));

        $this->renderer = new CKEditorRenderer($this->container);
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf('Ivory\CKEditorBundle\Renderer\CKEditorRendererInterface', $this->renderer);
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
        $context = array(
            'auto_inline' => false,
            'inline'      => true,
            'input_sync'  => true,
            'config'      => array('foo' => 'bar'),
            'plugins'     => array(
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
                    'templates'  => array(
                        array(
                            'title' => 'My Template',
                            'html'  => '<h1>Template</h1>',
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
        $expected = <<<'EOF'
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
        $expected = <<<'EOF'
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

        $this->assertTemplate($expected, array_merge($this->getContext(), array('autoload' => false)));
    }

    public function testRenderWithDisableWidget()
    {
        $expected = <<<'EOF'
<textarea >&lt;p&gt;value&lt;/p&gt;</textarea>

EOF;

        $this->assertTemplate($expected, array_merge($this->getContext(), array('enable' => false)));
    }

    /**
     * @param array $context
     *
     * @return string
     */
    abstract protected function renderTemplate(array $context = array());

    /**
     * @return array
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
        return str_replace(PHP_EOL, '', str_replace(' ', '', $output));
    }
}
