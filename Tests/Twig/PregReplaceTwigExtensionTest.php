<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Tests\Twig;

use \Twig_Environment,
    \Twig_Loader_String,
    Ivory\CKEditorBundle\Twig\PregReplaceTwigExtension;

/**
 * Preg replace twig extension test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PregReplaceTwigExtensionTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\CKEditorBundle\Twig\PregReplaceTwigExtension */
    protected $pregReplaceTwigExtension;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->pregReplaceTwigExtension = new PregReplaceTwigExtension();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->pregReplaceTwigExtension);
    }

    public function testPregReplace()
    {
        $this->assertSame('foo', $this->pregReplaceTwigExtension->pregReplace('bar', '/bar/', 'foo'));
    }

    public function testPregReplaceFilter()
    {
        $twig = new Twig_Environment(new Twig_Loader_String());
        $twig->addExtension($this->pregReplaceTwigExtension);

        $this->assertSame('foo', $twig->render('{{ \'bar\' | ivory_preg_replace(\'/bar/\', \'foo\') }}'));
    }
}
