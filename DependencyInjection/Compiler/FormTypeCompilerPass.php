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
use Symfony\Component\HttpKernel\Kernel;

/**
 * Create form.type tag alias for Symfony 2.x
 */
class FormTypeCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (Kernel::VERSION_ID >= 30000) {
            return;
        }

        if (!$container->hasDefinition('ivory_ck_editor.form.type')) {
            throw new ServiceNotFoundException('ivory_ck_editor.form.type');
        }

        $definition = $container->getDefinition('ivory_ck_editor.form.type');
        $definition->clearTag('form.type');
        $definition->addTag('form.type', array('alias' => 'ckeditor'));
    }
}
