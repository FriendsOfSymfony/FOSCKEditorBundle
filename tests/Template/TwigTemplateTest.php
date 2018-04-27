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

use Ivory\CKEditorBundle\Twig\CKEditorExtension;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TwigTemplateTest extends AbstractTemplateTest
{
    /**
     * @var \Twig\Environment
     */
    private $twig;

    /**
     * @var \Twig\Template
     */
    private $template;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $symfonyTheme = '{% block widget_attributes %}{% endblock %}';
        $ckeditorTheme = file_get_contents(__DIR__.'/../../src/Resources/views/Form/ckeditor_widget.html.twig');

        $this->twig = new \Twig_Environment(new \Twig_Loader_Array([
            'ckeditor' => $symfonyTheme.$ckeditorTheme,
        ]));

        $this->twig->addExtension(new CKEditorExtension($this->renderer));
        $this->template = $this->twig->loadTemplate('ckeditor');
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate(array $context = [])
    {
        return $this->template->renderBlock('ckeditor_widget', $context);
    }
}
