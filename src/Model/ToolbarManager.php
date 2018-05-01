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

namespace FOS\CKEditorBundle\Model;

use FOS\CKEditorBundle\Exception\ToolbarManagerException;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ToolbarManager implements ToolbarManagerInterface
{
    /**
     * @var array
     */
    private $items = [
        'basic.about' => ['About'],
        'basic.basic_styles' => ['Bold', 'Italic'],
        'basic.links' => ['Link', 'Unlink'],
        'basic.paragraph' => ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'],
        'standard.about' => ['Styles', 'Format', 'About'],
        'standard.basic_styles' => ['Bold', 'Italic', 'Strike', '-', 'RemoveFormat'],
        'standard.clipboard' => ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'],
        'standard.document' => ['Source'],
        'standard.editing' => ['Scayt'],
        'standard.links' => ['Link', 'Unlink', 'Anchor'],
        'standard.insert' => ['Image', 'Table', 'HorizontalRule', 'SpecialChar'],
        'standard.paragraph' => ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote'],
        'standard.tools' => ['Maximize'],
        'full.about' => ['About'],
        'full.basic_styles' => [
            'Bold',
            'Italic',
            'Underline',
            'Strike',
            'Subscript',
            'Superscript',
            '-',
            'RemoveFormat',
        ],
        'full.clipboard' => ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'],
        'full.colors' => ['TextColor', 'BGColor'],
        'full.document' => ['Source', '-', 'NewPage', 'Preview', 'Print', '-', 'Templates'],
        'full.editing' => ['Find', 'Replace', '-', 'SelectAll', '-', 'Scayt'],
        'full.forms' => [
            'Form',
            'Checkbox',
            'Radio',
            'TextField',
            'Textarea',
            'SelectField',
            'Button',
            'ImageButton',
            'HiddenField',
        ],
        'full.insert' => ['Image', 'Flash', 'Table', 'HorizontalRule', 'SpecialChar', 'Smiley', 'PageBreak', 'Iframe'],
        'full.links' => ['Link', 'Unlink', 'Anchor'],
        'full.paragraph' => [
            'NumberedList',
            'BulletedList',
            '-',
            'Outdent',
            'Indent',
            '-',
            'Blockquote',
            'CreateDiv',
            '-',
            'JustifyLeft',
            'JustifyCenter',
            'JustifyRight',
            'JustifyBlock',
            '-',
            'BidiLtr',
            'BidiRtl',
        ],
        'full.styles' => ['Styles', 'Format', 'Font', 'FontSize', 'TextColor', 'BGColor'],
        'full.tools' => ['Maximize', 'ShowBlocks'],
    ];

    /**
     * @var array
     */
    private $toolbars = [
        'basic' => [
            '@basic.basic_styles',
            '@basic.paragraph',
            '@basic.links',
            '@basic.about',
        ],
        'standard' => [
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
        ],
        'full' => [
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
        ],
    ];

    /**
     * @param array $items
     * @param array $toolbars
     */
    public function __construct(array $items = [], array $toolbars = [])
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
        $toolbars = [];

        foreach ($this->getToolbar($name) as $name => $item) {
            $toolbars[] = is_string($item) && '@' === substr($item, 0, 1)
                ? $this->getItem(substr($item, 1))
                : $item;
        }

        return $toolbars;
    }
}
