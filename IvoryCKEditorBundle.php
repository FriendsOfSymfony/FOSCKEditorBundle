<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle;

use Ivory\CKEditorBundle\DependencyInjection\Compiler\AssetsHelperCompilerPass;
use Ivory\CKEditorBundle\DependencyInjection\Compiler\ResourceCompilerPass;
use Ivory\CKEditorBundle\DependencyInjection\Compiler\TemplatingCompilerPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Ivory CKEditor bundle.
 *
 * @author GeLo <geloen.eric@gmail.com>
 * @author Adam Misiorny <adam.misiorny@gmail.com>
 */
class IvoryCKEditorBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new AssetsHelperCompilerPass())
            ->addCompilerPass(new ResourceCompilerPass())
            ->addCompilerPass(new TemplatingCompilerPass());
    }
}
