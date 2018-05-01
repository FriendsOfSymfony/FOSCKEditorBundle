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

namespace FOS\CKEditorBundle\Model;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface StylesSetManagerInterface
{
    /**
     * @return bool
     */
    public function hasStylesSets();

    /**
     * @return array
     */
    public function getStylesSets();

    /**
     * @param array $stylesSets
     */
    public function setStylesSets(array $stylesSets);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasStylesSet($name);

    /**
     * @param string $name
     *
     * @return array
     */
    public function getStylesSet($name);

    /**
     * @param string $name
     * @param array  $stylesSet
     */
    public function setStylesSet($name, array $stylesSet);
}
