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

/**
 * Abstract template test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractTemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Renders a template.
     *
     * @param array $context The template context.
     *
     * @return string The template output.
     */
    abstract protected function renderTemplate(array $context = array());

    /**
     * Normalizes the output by removing the heading whitespaces.
     *
     * @param string $output The output.
     *
     * @return string The normalized output.
     */
    protected function normalizeOutput($output)
    {
        return preg_replace('/^\s+/m', '', $output);
    }

    public function testRenderWithSimpleWidget()
    {
        $output = $this->renderTemplate(
            array(
                'form'      => $this->getMock('Symfony\Component\Form\FormView'),
                'id'        => 'id',
                'value'     => 'value',
                'enable'    => true,
                'base_path' => 'base_path',
                'js_path'   => 'js_path',
                'config'    => json_encode(array()),
                'plugins'   => array(),
                'styles'    => array(),
                'templates' => array(),
            )
        );

        $expected = <<<EOF
<textarea >value</textarea>
<script type="text/javascript">
var CKEDITOR_BASEPATH = 'base_path';
</script>
<script type="text/javascript" src="js_path"></script>
<script type="text/javascript">
if (CKEDITOR.instances['id']) {
delete CKEDITOR.instances['id'];
}
CKEDITOR.replace('id', []);
</script>

EOF;

        $this->assertSame($expected, $this->normalizeOutput($output));
    }

    public function testRenderWithFullWidget()
    {
        $output = $this->renderTemplate(
            array(
                'form'      => $this->getMock('Symfony\Component\Form\FormView'),
                'id'        => 'id',
                'value'     => 'value',
                'enable'    => true,
                'base_path' => 'base_path',
                'js_path'   => 'js_path',
                'config'    => json_encode(array('foo' => 'bar')),
                'plugins'   => array(
                    'foo' => array('path' => 'path', 'filename' => 'filename'),
                ),
                'styles'    => array(
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
                    )
                ),
            )
        );

        $expected = <<<EOF
<textarea >value</textarea>
<script type="text/javascript">
var CKEDITOR_BASEPATH = 'base_path';
</script>
<script type="text/javascript" src="js_path"></script>
<script type="text/javascript">
if (CKEDITOR.instances['id']) {
delete CKEDITOR.instances['id'];
}
CKEDITOR.plugins.addExternal('foo', 'path', 'filename');
CKEDITOR.stylesSet.add('default', [{"name":"Blue Title","element":"h2","styles":{"color":"Blue"}}]);
CKEDITOR.addTemplates('foo', {"imagesPath":"path","templates":[{"title":"My Template","html":"<h1>Template<\/h1>"}]});
CKEDITOR.replace('id', {"foo":"bar"});
</script>

EOF;

        $this->assertSame($expected, $this->normalizeOutput($output));
    }

    public function testRenderWithDisableWidget()
    {
        $output = $this->renderTemplate(
            array(
                'form'   => $this->getMock('Symfony\Component\Form\FormView'),
                'id'     => 'id',
                'value'  => 'value',
                'enable' => false,
            )
        );

        $expected = <<<EOF
<textarea >value</textarea>

EOF;

        $this->assertSame($expected, $this->normalizeOutput($output));
    }
}
