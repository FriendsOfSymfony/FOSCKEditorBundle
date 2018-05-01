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

namespace FOS\CKEditorBundle;

use FOS\CKEditorBundle\DependencyInjection\Compiler\ResourceCompilerPass;
use FOS\CKEditorBundle\DependencyInjection\Compiler\TemplatingCompilerPass;
use FOS\CKEditorBundle\DependencyInjection\FOSCKEditorExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FOSCKEditorBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new ResourceCompilerPass())
            ->addCompilerPass(new TemplatingCompilerPass());
    }

    public function getContainerExtension()
    {
        return new FOSCKEditorExtension();
    }
}
