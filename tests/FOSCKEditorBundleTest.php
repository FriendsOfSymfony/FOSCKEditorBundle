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

use FOS\CKEditorBundle\FOSCKEditorBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author GeLo <geloen.eric@gmail.com>
 * @author Adam Misiorny <adam.misiorny@gmail.com>
 */
class FOSCKEditorBundleTest extends AbstractTestCase
{
    /**
     * @var FOSCKEditorBundle
     */
    private $bundle;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->bundle = new FOSCKEditorBundle();
    }

    public function testBundle()
    {
        $this->assertInstanceOf(Bundle::class, $this->bundle);
    }
}
