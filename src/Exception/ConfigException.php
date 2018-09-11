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

namespace FOS\CKEditorBundle\Exception;

use RuntimeException;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
final class ConfigException extends RuntimeException implements FOSCKEditorException
{
    public static function configDoesNotExist(string $name): self
    {
        return new static(sprintf('The CKEditor config "%s" does not exist.', $name));
    }

    public static function invalidDefaultConfig(string $name): self
    {
        return new static(sprintf('The default config "%s" does not exist.', $name));
    }
}
