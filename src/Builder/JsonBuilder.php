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

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
final class JsonBuilder
{
    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var array
     */
    private $values = [];

    /**
     * @var array
     */
    private $escapes = [];

    /**
     * @var int
     */
    private $jsonEncodeOptions = 0;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;

        $this->reset();
    }

    public function getJsonEncodeOptions(): int
    {
        return $this->jsonEncodeOptions;
    }

    public function setJsonEncodeOptions(int $jsonEncodeOptions): self
    {
        $this->jsonEncodeOptions = $jsonEncodeOptions;

        return $this;
    }

    public function hasValues(): bool
    {
        return !empty($this->values);
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function setValues(array $values, string $pathPrefix = null): self
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
     * @param mixed $value
     */
    public function setValue(string $path, $value, bool $escapeValue = true): self
    {
        if (!$escapeValue) {
            $placeholder = uniqid('friendsofsymfony', true);
            $this->escapes[sprintf('"%s"', $placeholder)] = $value;

            $value = $placeholder;
        }

        $this->values[$path] = $value;

        return $this;
    }

    public function removeValue(string $path): self
    {
        unset($this->values[$path], $this->escapes[$path]);

        return $this;
    }

    public function reset(): self
    {
        $this->values = [];
        $this->escapes = [];
        $this->jsonEncodeOptions = 0;

        return $this;
    }

    public function build(): string
    {
        $values = [];

        foreach ($this->values as $path => $value) {
            $this->propertyAccessor->setValue($values, $path, $value);
        }

        $json = json_encode($values, $this->jsonEncodeOptions);

        \assert(\is_string($json));

        return str_replace(
            array_keys($this->escapes),
            array_values($this->escapes),
            $json
        );
    }
}
