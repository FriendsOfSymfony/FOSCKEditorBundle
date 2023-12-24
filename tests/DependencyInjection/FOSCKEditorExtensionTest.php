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

namespace FOS\CKEditorBundle\Tests\DependencyInjection;

use FOS\CKEditorBundle\DependencyInjection\FOSCKEditorExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class FOSCKEditorExtensionTest extends AbstractExtensionTestCase
{
    public function testHasServiceDefinitionForTwigExtension(): void
    {
        $this->container->setParameter('kernel.bundles', []);
        $this->load();

        $taggedServices = $this->container->findTaggedServiceIds('twig.extension');

        $this->assertArrayHasKey('fos_ck_editor.twig_extension', $taggedServices);
    }

    /**
     * @group legacy
     *
     * @expectedDeprecation IvoryCKEditorBundle isn't maintained anymore and should be replaced with FOSCKEditorBundle.
     */
    public function testIvoryDeprecation(): void
    {
        $this->container->setParameter('kernel.bundles', ['IvoryCKEditorBundle' => '']);
        $this->load();
    }

    protected function getContainerExtensions(): array
    {
        return [new FOSCKEditorExtension()];
    }
}
