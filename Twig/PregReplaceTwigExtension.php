<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Twig;

use \Twig_Extension;

/**
 * Preg replace twig extension.
 *
 * @link http://php.net/manual/en/function.preg-replace.php
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PregReplaceTwigExtension extends Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            'ivory_preg_replace' => new \Twig_Filter_Method($this, 'pregReplace', array('is_safe' => array('html'))),
        );
    }

    /**
     * Performs a regular expression search and replace.
     *
     * @param mixed $subject     The string or an array with strings to search and replace.
     * @param mixed $pattern     The pattern to search for. It can be either a string or an array with strings.
     * @param mixed $replacement The string or an array with strings to replace.
     *
     * @return mixed An array if the subject parameter is an array, or a string otherwise.
     */
    public function pregReplace($subject, $pattern, $replacement)
    {
        return preg_replace($pattern, $replacement, $subject);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'preg_replace';
    }
}
