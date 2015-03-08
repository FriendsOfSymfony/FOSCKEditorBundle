<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * Create assets.packages fallback alias for symfony < 2.7
 *
 * @author Adam Misiorny <adam.misiorny@gmail.com>
 */
class AssetsHelperCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('assets.packages')) {
            return;
        }

        if (!$container->hasDefinition('templating.helper.assets')) {
            throw new ServiceNotFoundException('templating.helper.assets');
        }

        // create fallback alias for symfony < 2.7
        $container->setAlias('assets.packages', 'templating.helper.assets');
    }
}
