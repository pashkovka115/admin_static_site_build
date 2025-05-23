<?php

declare(strict_types=1);

namespace Lib\DiDom;

use Lib\DiDom\Element;
use InvalidArgumentException;

class StyleAttribute
{
    /**
     * The DOM element instance.
     *
     * @var Element
     */
    protected $element;

    /**
     * @var string
     */
    protected $styleString = '';

    /**
     * @var array
     */
    protected $properties = [];

    /**
     * @param Element $element
     *
     * @throws InvalidArgumentException if parameter 1 is not an element node
     */
    public function __construct(Element $element)
    {
        if ( ! $element->isElementNode()) {
            throw new InvalidArgumentException(sprintf('The element must contain DOMElement node.'));
        }

        $this->element = $element;

        $this->parseStyleAttribute();
    }

    /**
     * Parses style attribute of the element.
     */
    protected function parseStyleAttribute()
    {
        if ( ! $this->element->hasAttribute('style')) {
            // possible if style attribute has been removed
            if ($this->styleString !== '') {
                $this->styleString = '';
                $this->properties = [];
            }

            return;
        }

        // if style attribute is not changed
        if ($this->element->getAttribute('style') === $this->styleString) {
            return;
        }

        // save style attribute as is (without trimming)
        $this->styleString = $this->element->getAttribute('style');

        $styleString = trim($this->styleString, ' ;');

        if ($styleString === '') {
            $this->properties = [];

            return;
        }

        $properties = explode(';', $styleString);

        foreach ($properties as $property) {
            list($name, $value) = explode(':', $property, 2);

            $name = trim($name);
            $value = trim($value);

            $this->properties[$name] = $value;
        }
    }

    /**
     * Updates style attribute of the element.
     */
    protected function updateStyleAttribute(): void
    {
        $this->styleString = $this->buildStyleString();

        $this->element->setAttribute('style', $this->styleString);
    }

    /**
     * @return string
     */
    protected function buildStyleString(): string
    {
        $properties = [];

        foreach ($this->properties as $propertyName => $value) {
            $properties[] = $propertyName . ': ' . $value;
        }

        return implode('; ', $properties);
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return \Lib\DiDom\StyleAttribute
     *
     * @throws InvalidArgumentException if property name is not a string
     * @throws InvalidArgumentException if property value is not a string
     */
    public function setProperty(string $name, string $value): self
    {
        $this->parseStyleAttribute();

        $this->properties[$name] = $value;

        $this->updateStyleAttribute();

        return $this;
    }

    /**
     * @param array $properties
     *
     * @return \Lib\DiDom\StyleAttribute
     *
     * @throws InvalidArgumentException if property name is not a string
     * @throws InvalidArgumentException if property value is not a string
     */
    public function setMultipleProperties(array $properties): self
    {
        $this->parseStyleAttribute();

        foreach ($properties as $propertyName => $value) {
            if ( ! is_string($propertyName)) {
                throw new InvalidArgumentException(sprintf('Property name must be a string, %s given.', (is_object($propertyName) ? get_class($propertyName) : gettype($propertyName))));
            }

            if ( ! is_string($value)) {
                throw new InvalidArgumentException(sprintf('Property value must be a string, %s given.', (is_object($value) ? get_class($value) : gettype($value))));
            }

            $this->properties[$propertyName] = $value;
        }

        $this->updateStyleAttribute();

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function getProperty(string $name, $default = null)
    {
        $this->parseStyleAttribute();

        if ( ! array_key_exists($name, $this->properties)) {
            return $default;
        }

        return $this->properties[$name];
    }

    /**
     * @param string[] $propertyNames
     *
     * @return array
     *
     * @throws InvalidArgumentException if property name is not a string
     */
    public function getMultipleProperties(array $propertyNames): array
    {
        $this->parseStyleAttribute();

        $result = [];

        foreach ($propertyNames as $propertyName) {
            if ( ! is_string($propertyName)) {
                throw new InvalidArgumentException(sprintf('Property name must be a string, %s given.', (is_object($propertyName) ? get_class($propertyName) : gettype($propertyName))));
            }

            if (array_key_exists($propertyName, $this->properties)) {
                $result[$propertyName] = $this->properties[$propertyName];
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getAllProperties(): array
    {
        $this->parseStyleAttribute();

        return $this->properties;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasProperty(string $name): bool
    {
        $this->parseStyleAttribute();

        return array_key_exists($name, $this->properties);
    }

    /**
     * @param string $name
     *
     * @return \Lib\DiDom\StyleAttribute
     *
     * @throws InvalidArgumentException if property name is not a string
     */
    public function removeProperty(string $name): self
    {
        $this->parseStyleAttribute();

        unset($this->properties[$name]);

        $this->updateStyleAttribute();

        return $this;
    }

    /**
     * @param array $propertyNames
     *
     * @return \Lib\DiDom\StyleAttribute
     *
     * @throws InvalidArgumentException if property name is not a string
     */
    public function removeMultipleProperties(array $propertyNames): self
    {
        $this->parseStyleAttribute();

        foreach ($propertyNames as $propertyName) {
            if ( ! is_string($propertyName)) {
                throw new InvalidArgumentException(sprintf('Property name must be a string, %s given.', (is_object($propertyName) ? get_class($propertyName) : gettype($propertyName))));
            }

            unset($this->properties[$propertyName]);
        }

        $this->updateStyleAttribute();

        return $this;
    }

    /**
     * @param string[] $preserved
     *
     * @return \Lib\DiDom\StyleAttribute
     */
    public function removeAllProperties(array $preserved = []): self
    {
        $this->parseStyleAttribute();

        $preservedProperties = [];

        foreach ($preserved as $propertyName) {
            if ( ! is_string($propertyName)) {
                throw new InvalidArgumentException(sprintf('Property name must be a string, %s given.', (is_object($propertyName) ? get_class($propertyName) : gettype($propertyName))));
            }

            if ( ! array_key_exists($propertyName, $this->properties)) {
                continue;
            }

            $preservedProperties[$propertyName] = $this->properties[$propertyName];
        }

        $this->properties = $preservedProperties;

        $this->updateStyleAttribute();

        return $this;
    }

    /**
     * @return Element
     */
    public function getElement(): Element
    {
        return $this->element;
    }
}
