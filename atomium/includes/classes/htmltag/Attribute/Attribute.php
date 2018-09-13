<?php

// @codingStandardsIgnoreStart
namespace Drupal\atomium\htmltag\Attribute;

use Drupal\atomium\htmltag\AbstractBaseHtmlTagObject;

/**
 * Class Attribute.
 */
class Attribute extends AbstractBaseHtmlTagObject implements AttributeInterface
{
    /**
     * Store the attribute name.
     *
     * @var string|null
     */
    private $name;

    /**
     * Store the attribute value.
     *
     * @var array|null
     */
    private $values;

    /**
     * Attribute constructor.
     *
     * @param string $name
     *   The attribute name.
     * @param string|string[]|mixed[] ...$value
     *   The attribute value.
     */
    public function __construct($name, ...$value)
    {
        $this->name = trim($name);
        $this->set($value);
    }

    /**
     * {@inheritdoc}
     */
    public function set(...$value)
    {
        $this->values = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getValueAsString()
    {
        return implode(
            ' ',
            $this->getValueAsArray()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getValueAsArray()
    {
        return array_unique(
            $this->normalizeValue($this->values)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $output = (string) $this->name;

        if (!$this->isBoolean()) {
            $output .= '="' . trim($this->getValueAsString()) . '"';
        }

        return $output;
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
    public function isBoolean()
    {
        if ([] === $this->getValueAsArray()) {
            $this->values = null;
        }

        return null === $this->values;
    }

    /**
     * {@inheritdoc}
     */
    public function append(...$value)
    {
        $this->values[] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(...$value)
    {
        $this->values = array_diff(
            $this->normalizeValue($this->values),
            $this->normalizeValue($value)
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function replace($original, ...$replacement)
    {
        $count_start = count($this->normalizeValue($this->values));
        $this->remove($original);
        $count_end = count($this->normalizeValue($this->values));

        if ($count_end !== $count_start) {
            $this->append($replacement);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function contains(...$substring)
    {
        $values = $this->normalizeValue($this->values);

        $array = array_map(
            function ($substring_item) use ($values) {
                return in_array($substring_item, $values, true);
            },
            $this->normalizeValue($substring)
        );

        return $array &&
            array_reduce(
                $array,
                function ($left, $right) {
                    return $left && $right;
                },
                true
            );
    }

    /**
     * {@inheritdoc}
     */
    public function setBoolean($boolean = true)
    {
        if (true === $boolean) {
            $this->values = null;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function delete()
    {
        $this->name = null;
        $this->values = null;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function alter(callable $callable)
    {
      $this->values = array_map($callable, $this->normalizeValue($this->values));

      return $this;
    }
}
