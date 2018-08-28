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
     * @group legacy
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
     * @group legacy
     */
    public function testRenderWithSimpleWidget()
    {
        parent::testRenderWithSimpleWidget();
    }

    /**
     * @group legacy
     */
    public function testRenderWithFullWidget()
    {
        parent::testRenderWithFullWidget();
    }

    /**
     * @group legacy
     */
    public function testRenderWithJQuery()
    {
        parent::testRenderWithJQuery();
    }

    /**
     * @group legacy
     */
    public function testRenderWithRequireJs()
    {
        parent::testRenderWithRequireJs();
    }

    /**
     * @group legacy
     */
    public function testRenderWithNotAutoloadedWidget()
    {
        parent::testRenderWithNotAutoloadedWidget();
    }

    /**
     * @group legacy
     */
    public function testRenderWithDisableWidget()
    {
        parent::testRenderWithDisableWidget();
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate(array $context = [])
    {
        return $this->phpEngine->render('ckeditor_widget.html.php', $context);
    }
}
