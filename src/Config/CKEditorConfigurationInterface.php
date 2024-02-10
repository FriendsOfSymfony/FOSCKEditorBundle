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
    public function isEnable(): bool;

    public function isAutoload(): bool;

    public function isPoweredBy(): bool;

    public function isResize(): bool;

    public function getBasePath(): string;

    public function getJsPath(): string;

    public function getDefaultConfig(): ?string;

    public function getConfigs(): array;

    public function getPlugins(): array;

    public function getStyles(): array;

    /**
     * @throws ConfigException
     */
    public function getConfig(string $name): array;
}
