<?php

// @codingStandardsIgnoreStart
namespace Drupal\atomium\htmltag\Attributes;

use Drupal\atomium\htmltag\Attribute\AttributeFactoryInterface;

/**
 * Interface AttributesFactoryInterface
 */
interface AttributesFactoryInterface
{
    /**
     * Create a new attributes.
     *
     * @param array $attributes
     *   The attributes.
     * @param \Drupal\atomium\htmltag\Attribute\AttributeFactoryInterface|null $attributeFactory
     *   The attribute factory.
     *
     * @return \Drupal\atomium\htmltag\Attributes\AttributesInterface
     *   The attributes.
     */
    public static function build(array $attributes = [], AttributeFactoryInterface $attributeFactory = null);
}
