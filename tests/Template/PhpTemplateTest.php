<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\CKEditorBundle\Tests\Template;

use FOS\CKEditorBundle\Templating\CKEditorHelper;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\FormHelper;
use Symfony\Component\Templating\Helper\SlotsHelper;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class PhpTemplateTest extends AbstractTemplateTest
{
    /**
     * @var PhpEngine
     */
    private $phpEngine;

    /**
     * @var FormHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $formHelper;

    /**
     * @var SlotsHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $slotsHelper;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->formHelper = $this->getMockBuilder(FormHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->slotsHelper = $this->getMockBuilder(SlotsHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->phpEngine = new PhpEngine(
            new TemplateNameParser(),
            new FilesystemLoader([__DIR__.'/../../src/Resources/views/Form/%name%']),
            [
                'form' => $this->formHelper,
                'slots' => $this->slotsHelper,
                new CKEditorHelper($this->renderer),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate(array $context = [])
    {
        return $this->phpEngine->render('ckeditor_widget.html.php', $context);
    }
}
