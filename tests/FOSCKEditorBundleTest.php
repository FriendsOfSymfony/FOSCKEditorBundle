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

namespace FOS\CKEditorBundle\Tests;

use FOS\CKEditorBundle\DependencyInjection\FOSCKEditorExtension;
use FOS\CKEditorBundle\FOSCKEditorBundle;
use PHPUnit\Framework\TestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 * @author Adam Misiorny <adam.misiorny@gmail.com>
 */
class FOSCKEditorBundleTest extends TestCase
{
    /**
     * @var FOSCKEditorBundle
     */
    private $bundle;

    protected function setUp(): void
    {
        $this->bundle = new FOSCKEditorBundle();
    }

    public function testExtension(): void
    {
        $this->assertInstanceOf(FOSCKEditorExtension::class, $this->bundle->getContainerExtension());
    }
}
