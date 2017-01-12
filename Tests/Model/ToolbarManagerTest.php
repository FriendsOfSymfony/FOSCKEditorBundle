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

use Ivory\CKEditorBundle\Model\ToolbarManager;
use Ivory\CKEditorBundle\Tests\AbstractTestCase;

/**
 * Toolbar manager test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ToolbarManagerTest extends AbstractTestCase
{
    /** @var \Ivory\CKEditorBundle\Model\ToolbarManager */
    private $toolbarManager;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->toolbarManager = new ToolbarManager();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->toolbarManager);
    }

    public function testDefaultState()
    {
        $this->assertTrue($this->toolbarManager->hasItems());
        $this->assertCount(25, $this->toolbarManager->getItems());

        $this->assertTrue($this->toolbarManager->hasToolbars());
        $this->assertCount(3, $this->toolbarManager->getToolbars());
    }

    public function testInitialState()
    {
        $items = array(
            'document' => array('Source', '-', 'Save'),
            'tools'    => array('Maximize'),
        );

        $toolbars = array(
            'default' => array('@document', '/', array('Anchor'), '/', '@tools'),
            'custom'  => array('@document', '/', array('Anchor')),
        );

        $this->toolbarManager = new ToolbarManager($items, $toolbars);

        $this->assertTrue($this->toolbarManager->hasItems());
        $this->assertCount(27, $this->toolbarManager->getItems());

        $this->assertTrue($this->toolbarManager->hasToolbars());
        $this->assertCount(5, $this->toolbarManager->getToolbars());
    }

    /**
     * @expectedException \Ivory\CKEditorBundle\Exception\ToolbarManagerException
     * @expectedExceptionMessage The CKEditor toolbar item "foo" does not exist.
     */
    public function testInvalidItem()
    {
        $this->toolbarManager->getItem('foo');
    }

    /**
     * @expectedException \Ivory\CKEditorBundle\Exception\ToolbarManagerException
     * @expectedExceptionMessage The CKEditor toolbar "foo" does not exist.
     */
    public function testInvalidToolbar()
    {
        $this->toolbarManager->getToolbar('foo');
    }

    /**
     * @expectedException \Ivory\CKEditorBundle\Exception\ToolbarManagerException
     * @expectedExceptionMessage The CKEditor toolbar "foo" does not exist.
     */
    public function testInvalidResolvedToolbar()
    {
        $this->toolbarManager->resolveToolbar('foo');
    }

    /**
     * @param string $name
     * @param array  $expected
     *
     * @dataProvider toolbarProvider
     */
    public function testResolveToolbar($name, array $expected)
    {
        $this->assertEquals($expected, $this->toolbarManager->resolveToolbar($name));
    }

    /**
     * @return array
     */
    public function toolbarProvider()
    {
        return array(
            'basic' => array('basic', array(
                array('Bold', 'Italic'),
                array('NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'),
                array('Link', 'Unlink'),
                array('About'),
            )),
            'standard' => array('standard', array(
                array('Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'),
                array('Scayt'),
                array('Link', 'Unlink', 'Anchor'),
                array('Image', 'Table', 'HorizontalRule', 'SpecialChar'),
                array('Maximize'),
                array('Source'),
                '/',
                array('Bold', 'Italic', 'Strike', '-', 'RemoveFormat'),
                array('NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote'),
                array('Styles', 'Format', 'About'),
            )),
            'full' => array('full', array(
                array('Source', '-', 'NewPage', 'Preview', 'Print', '-', 'Templates'),
                array('Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'),
                array('Find', 'Replace', '-', 'SelectAll', '-', 'Scayt'),
                array('Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'SelectField', 'Button', 'ImageButton', 'HiddenField'),
                '/',
                array('Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'),
                array('NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl'),
                array('Link', 'Unlink', 'Anchor'),
                array('Image', 'Flash', 'Table', 'HorizontalRule', 'SpecialChar', 'Smiley', 'PageBreak', 'Iframe'),
                '/',
                array('Styles', 'Format', 'Font', 'FontSize', 'TextColor', 'BGColor'),
                array('TextColor', 'BGColor'),
                array('Maximize', 'ShowBlocks'),
                array('About'),
            )),
        );
    }
}
