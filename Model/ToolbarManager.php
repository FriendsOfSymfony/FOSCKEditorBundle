<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Model;

use Ivory\CKEditorBundle\Exception\ToolbarManagerException;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ToolbarManager implements ToolbarManagerInterface
{
    /**
     * @var array
     */
    private $items = array(
        'basic.about'           => array('About'),
        'basic.basic_styles'    => array('Bold', 'Italic'),
        'basic.links'           => array('Link', 'Unlink'),
        'basic.paragraph'       => array('NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'),
        'standard.about'        => array('Styles', 'Format', 'About'),
        'standard.basic_styles' => array('Bold', 'Italic', 'Strike', '-', 'RemoveFormat'),
        'standard.clipboard'    => array('Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'),
        'standard.document'     => array('Source'),
        'standard.editing'      => array('Scayt'),
        'standard.links'        => array('Link', 'Unlink', 'Anchor'),
        'standard.insert'       => array('Image', 'Table', 'HorizontalRule', 'SpecialChar'),
        'standard.paragraph'    => array('NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote'),
        'standard.tools'        => array('Maximize'),
        'full.about'            => array('About'),
        'full.basic_styles'     => array('Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'),
        'full.clipboard'        => array('Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'),
        'full.colors'           => array('TextColor', 'BGColor'),
        'full.document'         => array('Source', '-', 'NewPage', 'Preview', 'Print', '-', 'Templates'),
        'full.editing'          => array('Find', 'Replace', '-', 'SelectAll', '-', 'Scayt'),
        'full.forms'            => array('Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'SelectField', 'Button', 'ImageButton', 'HiddenField'),
        'full.insert'           => array('Image', 'Flash', 'Table', 'HorizontalRule', 'SpecialChar', 'Smiley', 'PageBreak', 'Iframe'),
        'full.links'            => array('Link', 'Unlink', 'Anchor'),
        'full.paragraph'        => array('NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl'),
        'full.styles'           => array('Styles', 'Format', 'Font', 'FontSize', 'TextColor', 'BGColor'),
        'full.tools'            => array('Maximize', 'ShowBlocks'),
    );

    /**
     * @var array
     */
    private $toolbars = array(
        'basic' => array(
            '@basic.basic_styles',
            '@basic.paragraph',
            '@basic.links',
            '@basic.about',
        ),
        'standard' => array(
            '@standard.clipboard',
            '@standard.editing',
            '@standard.links',
            '@standard.insert',
            '@standard.tools',
            '@standard.document',
            '/',
            '@standard.basic_styles',
            '@standard.paragraph',
            '@standard.about',
        ),
        'full' => array(
            '@full.document',
            '@full.clipboard',
            '@full.editing',
            '@full.forms',
            '/',
            '@full.basic_styles',
            '@full.paragraph',
            '@full.links',
            '@full.insert',
            '/',
            '@full.styles',
            '@full.colors',
            '@full.tools',
            '@full.about',
        ),
    );

    /**
     * @param array $items
     * @param array $toolbars
     */
    public function __construct(array $items = array(), array $toolbars = array())
    {
        $this->setItems($items);
        $this->setToolbars($toolbars);
    }

    /**
     * {@inheritdoc}
     */
    public function hasItems()
    {
        return !empty($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function setItems(array $items)
    {
        foreach ($items as $name => $item) {
            $this->setItem($name, $item);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem($name)
    {
        return isset($this->items[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($name)
    {
        if (!$this->hasItem($name)) {
            throw ToolbarManagerException::itemDoesNotExist($name);
        }

        return $this->items[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function setItem($name, array $item)
    {
        $this->items[$name] = $item;
    }

    /**
     * {@inheritdoc}
     */
    public function hasToolbars()
    {
        return !empty($this->toolbars);
    }

    /**
     * {@inheritdoc}
     */
    public function getToolbars()
    {
        return $this->toolbars;
    }

    /**
     * {@inheritdoc}
     */
    public function setToolbars(array $toolbars)
    {
        foreach ($toolbars as $name => $toolbar) {
            $this->setToolbar($name, $toolbar);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasToolbar($name)
    {
        return isset($this->toolbars[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getToolbar($name)
    {
        if (!$this->hasToolbar($name)) {
            throw ToolbarManagerException::toolbarDoesNotExist($name);
        }

        return $this->toolbars[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function setToolbar($name, array $toolbar)
    {
        $this->toolbars[$name] = $toolbar;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveToolbar($name)
    {
        $toolbars = array();

        foreach ($this->getToolbar($name) as $name => $item) {
            $toolbars[] = is_string($item) && substr($item, 0, 1) === '@'
                ? $this->getItem(substr($item, 1))
                : $item;
        }

        return $toolbars;
    }
}
