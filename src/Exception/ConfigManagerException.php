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

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ConfigManagerException extends Exception
{
    /**
     * @param string $name
     *
     * @return ConfigManagerException
     */
    public static function configDoesNotExist($name)
    {
        return new static(sprintf('The CKEditor config "%s" does not exist.', $name));
    }
}
