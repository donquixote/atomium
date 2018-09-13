<?php

// @codingStandardsIgnoreStart
namespace Drupal\atomium\htmltag\Attributes;

use Drupal\atomium\htmltag\AbstractBaseHtmlTagObject;
use Drupal\atomium\htmltag\Attribute\AttributeFactoryInterface;

/**
 * Class Attributes.
 */
class Attributes extends AbstractBaseHtmlTagObject implements AttributesInterface
{
    /**
     * Stores the attribute data.
     *
     * @var \Drupal\atomium\htmltag\Attribute\AttributeInterface[]
     */
    private $storage = [];

    /**
     * The attribute factory.
     *
     * @var \Drupal\atomium\htmltag\Attribute\AttributeFactoryInterface
     */
    private $attributeFactory;

    /**
     * Attributes constructor.
     *
     * @param \Drupal\atomium\htmltag\Attribute\AttributeFactoryInterface $attributeFactory
     *   The attribute factory.
     * @param mixed[] $attributes
     *   The input attributes.
     */
    public function __construct(AttributeFactoryInterface $attributeFactory, $attributes = [])
    {
        $this->attributeFactory = $attributeFactory;
        $this->import($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function import($attributes)
    {
        foreach ($attributes as $name => $value) {
            $this->set($name, $value);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, ...$value)
    {
        $this->storage[$name] = $this->attributeFactory->getInstance($name, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($name)
    {
        if (!isset($this->storage[$name])) {
            $this->set($name);
        }

        return $this->storage[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($name, $value = null)
    {
        $this->set($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($name)
    {
        unset($this->storage[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($name)
    {
        return isset($this->storage[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function append($key, ...$value)
    {
        $this->storage += array(
            $key => $this->attributeFactory->getInstance($key),
        );

        $this->storage[$key]->append($value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key, ...$value)
    {
        if (isset($this->storage[$key])) {
            $this->storage[$key]->remove($value);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(...$name)
    {
        foreach ($this->normalizeValue($name) as $attribute_name) {
            unset($this->storage[$attribute_name]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function without(...$key)
    {
        $attributes = clone $this;

        return $attributes->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function replace($key, $value, ...$replacement)
    {
        if (!isset($this->storage[$key])) {
            return $this;
        }

        if (!$this->contains($key, $value)) {
            return $this;
        }

        $this->storage[$key]->replace($value, $replacement);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->append($key, $value);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($key, $value = null)
    {
        if (!isset($this->storage[$key])) {
            return false;
        }

        if (null !== $value) {
            return $this->contains($key, $value);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function contains($key, ...$value)
    {
        if (!isset($this->storage[$key])) {
            return false;
        }

        return $this->storage[$key]->contains($value);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $attributes = implode(' ', $this->prepareValues());

        return $attributes ? ' ' . $attributes : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->toArray());
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->storage);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $result = [];

        foreach ($this->prepareValues() as $attribute) {
            $result[$attribute->getName()] = $attribute->getValueAsArray();
        }

        return $result;
    }

    /**
     * Returns all storage elements as an array.
     *
     * @return \Drupal\atomium\htmltag\Attribute\AttributeInterface[]
     *   An associative array of attributes.
     */
    private function prepareValues()
    {
        /** @var \Drupal\atomium\htmltag\Attribute\AttributeInterface[] $attributes */
        $attributes = $this->storage;

        // If empty, just return an empty array.
        if ([] === $attributes) {
            return [];
        }

        // Sort the attributes.
        ksort($attributes);

        return array_map(
            function ($attribute) {
                switch ($attribute->getName()) {
                    case 'class':
                        $classes = $attribute->getValueAsArray();
                        asort($classes);
                        $attribute->set($classes);
                        break;
                }

                return $attribute;
            },
            $attributes
        );
    }
}
