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

interface CKEditorConfigurationInterface
{
    public function getToolbar(string $name): array;

    public function getStyles(): array;

    public function getPlugins(): array;

    public function getTemplates(): array;

    public function isEnable(): bool;

    public function isAsync(): bool;

    public function isAutoload(): bool;

    public function isAutoInline(): bool;

    public function isInline(): bool;

    public function isJquery(): bool;

    public function isRequireJs(): bool;

    public function isInputSync(): bool;

    public function getFilebrowsers(): array;

    public function getBasePath(): string;

    public function getJsPath(): string;

    public function getJqueryPath(): string;

    public function getDefaultConfig(): ?string;

    public function getConfigs(): array;

    /**
     * @throws ConfigException
     */
    public function getConfig(string $name): array;
}
