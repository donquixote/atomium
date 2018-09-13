<?php

// @codingStandardsIgnoreStart
namespace Drupal\atomium\htmltag\Attribute;

/**
 * Interface AttributeFactoryInterface
 */
interface AttributeFactoryInterface
{
    /**
     * Create a new attribute.
     *
     * @param string $name
     *   The attribute name.
     * @param string|string[]|mixed ...$value
     *   The attribute value.
     *
     * @return \Drupal\atomium\htmltag\Attribute\AttributeInterface
     *   The attribute.
     */
    public static function build($name, ...$value);

    /**
     * Create a new attribute.
     *
     * @param string $name
     *   The attribute name.
     * @param string|string[]|mixed ...$value
     *   The attribute value.
     *
     * @return \Drupal\atomium\htmltag\Attribute\AttributeInterface
     *   The attribute.
     */
    public function getInstance($name, ...$value);
}
