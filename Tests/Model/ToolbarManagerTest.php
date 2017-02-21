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
 * @author GeLo <geloen.eric@gmail.com>
 */
class ToolbarManagerTest extends AbstractTestCase
{
    /**
     * @var ToolbarManager
     */
    private $toolbarManager;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->toolbarManager = new ToolbarManager();
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
        $items = [
            'document' => ['Source', '-', 'Save'],
            'tools'    => ['Maximize'],
        ];

        $toolbars = [
            'default' => ['@document', '/', ['Anchor'], '/', '@tools'],
            'custom'  => ['@document', '/', ['Anchor']],
        ];

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
        return [
            'basic' => ['basic', [
                ['Bold', 'Italic'],
                ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'],
                ['Link', 'Unlink'],
                ['About'],
            ]],
            'standard' => ['standard', [
                ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'],
                ['Scayt'],
                ['Link', 'Unlink', 'Anchor'],
                ['Image', 'Table', 'HorizontalRule', 'SpecialChar'],
                ['Maximize'],
                ['Source'],
                '/',
                ['Bold', 'Italic', 'Strike', '-', 'RemoveFormat'],
                ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote'],
                ['Styles', 'Format', 'About'],
            ]],
            'full' => ['full', [
                ['Source', '-', 'NewPage', 'Preview', 'Print', '-', 'Templates'],
                ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'],
                ['Find', 'Replace', '-', 'SelectAll', '-', 'Scayt'],
                ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'SelectField', 'Button', 'ImageButton', 'HiddenField'],
                '/',
                ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'],
                ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl'],
                ['Link', 'Unlink', 'Anchor'],
                ['Image', 'Flash', 'Table', 'HorizontalRule', 'SpecialChar', 'Smiley', 'PageBreak', 'Iframe'],
                '/',
                ['Styles', 'Format', 'Font', 'FontSize', 'TextColor', 'BGColor'],
                ['TextColor', 'BGColor'],
                ['Maximize', 'ShowBlocks'],
                ['About'],
            ]],
        ];
    }
}
