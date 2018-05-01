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
    public function testHasServiceDefinitionForTemplatingAlias()
    {
        $this->load();

        $taggedServices = $this->container->findTaggedServiceIds('templating.helper');

        $this->assertArrayHasKey('fos_ck_editor.templating.helper', $taggedServices);
        $this->assertNotEmpty($taggedServices['fos_ck_editor.templating.helper']);
        $this->assertContains('fos_ckeditor', $taggedServices['fos_ck_editor.templating.helper'][0]['alias']);
    }

    protected function getContainerExtensions()
    {
        return [new FOSCKEditorExtension()];
    }
}
