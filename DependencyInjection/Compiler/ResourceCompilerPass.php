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

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Resource compiler pass.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResourceCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $templatingEngines = $container->getParameter('templating.engines');

        if (in_array('php', $templatingEngines)) {
            $container->setParameter(
                'templating.helper.form.resources',
                array_merge(
                    array('IvoryCKEditorBundle:Form'),
                    $container->getParameter('templating.helper.form.resources')
                )
            );
        }

        if (in_array('twig', $templatingEngines)) {
            $container->setParameter(
                'twig.form.resources',
                array_merge(
                    array('IvoryCKEditorBundle:Form:ckeditor_widget.html.twig'),
                    $container->getParameter('twig.form.resources')
                )
            );
        }
    }
}
