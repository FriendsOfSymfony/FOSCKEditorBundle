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

@trigger_error(
    'The '.__NAMESPACE__.'TemplateManagerException is deprecated since 1.x '.
    'and will be removed with the 2.0 release.',
    E_USER_DEPRECATED
);

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TemplateManagerException extends Exception
{
    /**
     * @param string $name
     *
     * @return TemplateManagerException
     */
    public static function templateDoesNotExist($name)
    {
        return new static(sprintf('The CKEditor template "%s" does not exist.', $name));
    }
}
