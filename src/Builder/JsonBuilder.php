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

namespace FOS\CKEditorBundle\Builder;

use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class JsonBuilder
{
    /**
     * @var PropertyAccessorInterface
     */
    private $accessor;

    /**
     * @var array
     */
    private $values;

    /**
     * @var array
     */
    private $escapes;

    /**
     * @var int
     */
    private $jsonEncodeOptions;

    /**
     * @param PropertyAccessorInterface|null $propertyAccessor
     */
    public function __construct(PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->accessor = $propertyAccessor ?: new PropertyAccessor();

        $this->reset();
    }

    /**
     * @return int
     */
    public function getJsonEncodeOptions()
    {
        return $this->jsonEncodeOptions;
    }

    /**
     * @param int $jsonEncodeOptions
     *
     * @return JsonBuilder
     */
    public function setJsonEncodeOptions($jsonEncodeOptions)
    {
        $this->jsonEncodeOptions = $jsonEncodeOptions;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasValues()
    {
        return !empty($this->values);
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param array  $values
     * @param string $pathPrefix
     *
     * @return JsonBuilder
     */
    public function setValues(array $values, $pathPrefix = null)
    {
        foreach ($values as $key => $value) {
            $path = sprintf('%s[%s]', $pathPrefix, $key);

            if (\is_array($value) && !empty($value)) {
                $this->setValues($value, $path);
            } else {
                $this->setValue($path, $value);
            }
        }

        return $this;
    }

    /**
     * @param string $path
     * @param mixed  $value
     * @param bool   $escapeValue
     *
     * @return JsonBuilder
     */
    public function setValue($path, $value, $escapeValue = true)
    {
        if (!$escapeValue) {
            $placeholder = uniqid('ivory', true);
            $this->escapes[sprintf('"%s"', $placeholder)] = $value;

            $value = $placeholder;
        }

        $this->values[$path] = $value;

        return $this;
    }

    /**
     * @param string $path
     *
     * @return JsonBuilder
     */
    public function removeValue($path)
    {
        unset($this->values[$path], $this->escapes[$path]);

        return $this;
    }

    /**
     * @return JsonBuilder
     */
    public function reset()
    {
        $this->values = [];
        $this->escapes = [];
        $this->jsonEncodeOptions = 0;

        return $this;
    }

    /**
     * @return string
     */
    public function build()
    {
        $json = [];

        foreach ($this->values as $path => $value) {
            $this->accessor->setValue($json, $path, $value);
        }

        return str_replace(
            array_keys($this->escapes),
            array_values($this->escapes),
            json_encode($json, $this->jsonEncodeOptions)
        );
    }
}
