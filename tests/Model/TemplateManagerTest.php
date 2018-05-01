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

use FOS\CKEditorBundle\Model\TemplateManager;
use FOS\CKEditorBundle\Tests\AbstractTestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TemplateManagerTest extends AbstractTestCase
{
    /**
     * @var TemplateManager
     */
    private $templateManager;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->templateManager = new TemplateManager();
    }

    public function testDefaultState()
    {
        $this->assertFalse($this->templateManager->hasTemplates());
        $this->assertSame([], $this->templateManager->getTemplates());
    }

    public function testInitialState()
    {
        $templates = [
            'default' => [
                'imagesPath' => '/my/path',
                'templates' => [
                    [
                        'title' => 'My Template',
                        'html' => '<h1>Template</h1><p>Type your text here.</p>',
                    ],
                ],
            ],
        ];

        $this->templateManager = new TemplateManager($templates);

        $this->assertTrue($this->templateManager->hasTemplates());
        $this->assertTrue($this->templateManager->hasTemplate('default'));
        $this->assertSame($templates['default'], $this->templateManager->getTemplate('default'));
    }

    public function testTemplates()
    {
        $templates = [
            'default' => [
                'imagesPath' => '/my/path',
                'templates' => [
                    [
                        'title' => 'My Template',
                        'html' => '<h1>Template</h1><p>Type your text here.</p>',
                    ],
                ],
            ],
        ];

        $this->templateManager->setTemplates($templates);

        $this->assertTrue($this->templateManager->hasTemplates());
        $this->assertTrue($this->templateManager->hasTemplate('default'));
        $this->assertSame($templates, $this->templateManager->getTemplates());
    }

    /**
     * @expectedException \FOS\CKEditorBundle\Exception\TemplateManagerException
     * @expectedExceptionMessage The CKEditor template "foo" does not exist.
     */
    public function testGetTemplateWithInvalidValue()
    {
        $this->templateManager->getTemplate('foo');
    }
}
