<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Tests\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class TestContainerPass implements CompilerPassInterface
{
    /**
     * An array of service id's which should be public in a test scenario.
     *
     * @var array
     */
    private $services = [];

    public function __construct(array $services = [])
    {
        $this->services = $services;
    }

    public function process(ContainerBuilder $container)
    {
        foreach ($container->getDefinitions() as $id => $definition) {
            if (in_array($id, $this->services, true)) {
                $definition->setPublic(true);
            }
        }
    }
}
