<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Tests\Template;

use Symfony\Component\Templating\Loader\FilesystemLoader,
    Symfony\Component\Templating\PhpEngine,
    Symfony\Component\Templating\TemplateNameParser;

/**
 * PHP template test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PhpTemplateTest extends AbstractTemplateTest
{
    /** @var \Symfony\Component\Templating\PhpEngine */
    protected $phpEngine;

    /** @var \Symfony\Bundle\FrameworkBundle\Templating\Helper\FormHelper */
    protected $formHelperMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->phpEngine = new PhpEngine(
            new TemplateNameParser(),
            new FilesystemLoader(__DIR__.'/../../Resources/views/Form/%name%')
        );

        $this->formHelperMock = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\Helper\FormHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $this->formHelperMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('form'));

        $this->phpEngine->addHelpers(array($this->formHelperMock));
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->formHelper);
        unset($this->phpEngine);
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate(array $context = array())
    {
        return $this->phpEngine->render('ckeditor_widget.html.php', $context);
    }
}
