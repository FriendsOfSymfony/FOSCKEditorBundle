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

namespace FOS\CKEditorBundle\Config;

use FOS\CKEditorBundle\Exception\ConfigException;

final class CKEditorConfiguration implements CKEditorConfigurationInterface
{
    private $toolbarItems = [
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

    private $toolbarConfigs = [
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
     * @var bool
     */
    private $enable;

    /**
     * @var bool
     */
    private $async;

    /**
     * @var bool
     */
    private $autoload;

    /**
     * @var bool
     */
    private $autoInline;

    /**
     * @var bool
     */
    private $inline;

    /**
     * @var bool
     */
    private $jquery;

    /**
     * @var bool
     */
    private $requireJs;

    /**
     * @var bool
     */
    private $inputSync;

    /**
     * @var array
     */
    private $filebrowsers;

    /**
     * @var string
     */
    private $basePath;

    /**
     * @var string
     */
    private $jsPath;

    /**
     * @var string
     */
    private $jqueryPath;

    /**
     * @var string|null
     */
    private $defaultConfig;

    /**
     * @var array
     */
    private $configs;

    /**
     * @var array
     */
    private $templates;

    /**
     * @var array
     */
    private $styles;

    /**
     * @var array
     */
    private $plugins;

    public function __construct(array $config)
    {
        if ($config['enable']) {
            $config = $this->resolveConfigs($config);
            $config = $this->resolveStylesSet($config);
        }

        $this->enable = $config['enable'];
        $this->async = $config['async'];
        $this->autoload = $config['autoload'];
        $this->autoInline = $config['auto_inline'];
        $this->inline = $config['inline'];
        $this->jquery = $config['jquery'];
        $this->requireJs = $config['require_js'];
        $this->inputSync = $config['input_sync'];
        $this->filebrowsers = $config['filebrowsers'];
        $this->basePath = $config['base_path'];
        $this->jsPath = $config['js_path'];
        $this->jqueryPath = $config['jquery_path'];
        $this->defaultConfig = $config['default_config'];
        $this->plugins = $config['plugins'];
        $this->styles = $config['styles'];
        $this->templates = $config['templates'];
        $this->plugins = $config['plugins'];
        $this->configs = $config['configs'];
        $this->toolbarConfigs = array_merge($this->toolbarConfigs, $config['toolbars']['configs']);
        $this->toolbarItems = array_merge($this->toolbarItems, $config['toolbars']['items']);
    }

    /**
     * @throws ConfigException
     */
    private function resolveConfigs(array $config): array
    {
        if (empty($config['configs'])) {
            return $config;
        }

        if (!isset($config['default_config']) && !empty($config['configs'])) {
            reset($config['configs']);
            $config['default_config'] = key($config['configs']);
        }

        if (isset($config['default_config']) && !isset($config['configs'][$config['default_config']])) {
            throw ConfigException::invalidDefaultConfig($config['default_config']);
        }

        return $config;
    }

    private function resolveStylesSet(array $config): array
    {
        if (empty($config['styles'])) {
            return $config;
        }

        $stylesSets = $config['styles'];

        foreach ($stylesSets as &$stylesSet) {
            foreach ($stylesSet as &$value) {
                $value = array_filter($value);
            }
        }

        return $config;
    }

    public function getToolbar(string $name): array
    {
        $items = [];

        foreach ($this->toolbarConfigs[$name] as $name => $item) {
            $items[] = is_string($item) && '@' === substr($item, 0, 1)
                ? $this->toolbarItems[(substr($item, 1))]
                : $item;
        }

        return $items;
    }

    public function getStyles(): array
    {
        return $this->styles;
    }

    public function getPlugins(): array
    {
        return $this->plugins;
    }

    public function getTemplates(): array
    {
        return $this->templates;
    }

    public function isEnable(): bool
    {
        return $this->enable;
    }

    public function isAsync(): bool
    {
        return $this->async;
    }

    public function isAutoload(): bool
    {
        return $this->autoload;
    }

    public function isAutoInline(): bool
    {
        return $this->autoInline;
    }

    public function isInline(): bool
    {
        return $this->inline;
    }

    public function isJquery(): bool
    {
        return $this->jquery;
    }

    public function isRequireJs(): bool
    {
        return $this->requireJs;
    }

    public function isInputSync(): bool
    {
        return $this->inputSync;
    }

    public function getFilebrowsers(): array
    {
        return $this->filebrowsers;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function getJsPath(): string
    {
        return $this->jsPath;
    }

    public function getJqueryPath(): string
    {
        return $this->jqueryPath;
    }

    public function getDefaultConfig(): ?string
    {
        return $this->defaultConfig;
    }

    public function getConfigs(): array
    {
        return $this->configs;
    }

    /**
     * @throws ConfigException
     */
    public function getConfig(string $name): array
    {
        if (!isset($this->configs[$name])) {
            throw ConfigException::configDoesNotExist($name);
        }

        return $this->configs[$name];
    }
}
