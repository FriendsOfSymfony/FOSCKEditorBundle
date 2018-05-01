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

namespace FOS\CKEditorBundle\Tests\Model;

use FOS\CKEditorBundle\Model\StylesSetManager;
use FOS\CKEditorBundle\Tests\AbstractTestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class StylesSetManagerTest extends AbstractTestCase
{
    /**
     * @var StylesSetManager
     */
    private $stylesSetManager;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->stylesSetManager = new StylesSetManager();
    }

    public function testDefaultState()
    {
        $this->assertFalse($this->stylesSetManager->hasStylesSets());
        $this->assertSame([], $this->stylesSetManager->getStylesSets());
    }

    public function testInitialState()
    {
        $stylesSets = [
            'default' => [
                ['name' => 'Blue Title', 'element' => 'h2', 'styles' => ['color' => 'Blue']],
                ['name' => 'CSS Style', 'element' => 'span', 'attributes' => ['class' => 'my_style']],
            ],
        ];

        $this->stylesSetManager = new StylesSetManager($stylesSets);

        $this->assertTrue($this->stylesSetManager->hasStylesSets());
        $this->assertTrue($this->stylesSetManager->hasStylesSet('default'));
        $this->assertSame($stylesSets['default'], $this->stylesSetManager->getStylesSet('default'));
    }

    public function testTemplates()
    {
        $stylesSets = [
            'default' => [
                ['name' => 'Blue Title', 'element' => 'h2', 'styles' => ['color' => 'Blue']],
                ['name' => 'CSS Style', 'element' => 'span', 'attributes' => ['class' => 'my_style']],
            ],
        ];

        $this->stylesSetManager->setStylesSets($stylesSets);

        $this->assertTrue($this->stylesSetManager->hasStylesSets());
        $this->assertTrue($this->stylesSetManager->hasStylesSet('default'));
        $this->assertSame($stylesSets, $this->stylesSetManager->getStylesSets());
    }

    /**
     * @expectedException \FOS\CKEditorBundle\Exception\StylesSetManagerException
     * @expectedExceptionMessage The CKEditor styles set "foo" does not exist.
     */
    public function testGetStylesSetWithInvalidValue()
    {
        $this->stylesSetManager->getStylesSet('foo');
    }
}
