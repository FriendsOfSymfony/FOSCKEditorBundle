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
 * @author Marko Kunic <kunicmarko20@gmail.com>
 */
final class BadProxyUrlException extends RuntimeException implements FOSCKEditorException
{
    public static function fromEnvUrl(string $url): self
    {
        return new static(sprintf('Unable to parse provided proxy url "%s".', $url));
    }
}
