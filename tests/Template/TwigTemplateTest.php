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

use FOS\CKEditorBundle\Twig\CKEditorExtension;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TwigTemplateTest extends AbstractTemplateTest
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var Template
     */
    private $template;

    protected function setUp(): void
    {
        parent::setUp();

        $symfonyTheme = '{% block widget_attributes %}{% endblock %}';
        $ckeditorTheme = file_get_contents(__DIR__.'/../../src/Resources/views/Form/ckeditor_widget.html.twig');

        $this->twig = new Environment(new ArrayLoader([
            'ckeditor' => $symfonyTheme.$ckeditorTheme,
        ]));

        $this->twig->addExtension(new CKEditorExtension($this->renderer));
        $this->template = $this->twig->load('ckeditor');
    }

    protected function renderTemplate(array $context = []): string
    {
        return $this->template->renderBlock('ckeditor_widget', $context);
    }
}
