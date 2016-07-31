<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Tests\Templating;

use Ivory\CKEditorBundle\Templating\CKEditorHelper;
use Ivory\CKEditorBundle\Tests\AbstractTestCase;

/**
 * CKEditor helper test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CKEditorHelperTest extends AbstractTestCase
{
    public function testLegacyContainerConstructor()
    {
        $container = $this->createMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('ivory_ck_editor.renderer'))
            ->will($this->returnValue($this->createMock('Ivory\CKEditorBundle\Renderer\CKEditorRendererInterface')));

        new CKEditorHelper($container);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The CKEditor renderer must be an instance of "Ivory\CKEditorBundle\Renderer\CKEditorRendererInterface".
     */
    public function testInvalidRendererConstructor()
    {
        new CKEditorHelper(new \stdClass());
    }
}
