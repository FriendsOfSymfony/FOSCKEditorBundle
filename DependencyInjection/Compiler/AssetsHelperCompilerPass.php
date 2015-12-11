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
use Symfony\Component\HttpKernel\Kernel;

/**
 * Create assets.packages fallback alias for Symfony < 2.7
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
        if (Kernel::VERSION_ID < 27000 && $container->has('templating.helper.assets')) {
            $container->setAlias('assets.packages', 'templating.helper.assets');
        }
    }
}
