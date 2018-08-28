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

namespace FOS\CKEditorBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

@trigger_error(
    'The '.__NAMESPACE__.'TemplatingCompilerPass is deprecated since 1.x '.
    'and will be removed with the 2.0 release.',
    E_USER_DEPRECATED
);

/**
 * @deprecated since 1.x and will be removed with the 2.0 release.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class TemplatingCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('templating.engine.php')) {
            $container->removeDefinition('fos_ck_editor.templating.helper');
        }

        if (!$container->hasDefinition('twig')) {
            $container->removeDefinition('fos_ck_editor.twig_extension');
        }
    }
}
