<?php

// @codingStandardsIgnoreStart
namespace Drupal\atomium\htmltag;

use Drupal\atomium\htmltag\Attribute\AttributeFactoryInterface;
use Drupal\atomium\htmltag\Attributes\AttributesFactoryInterface;

/**
 * Interface HtmlTagInterface
 */
interface HtmlTagInterface
{

    /**
     * Create a new tag.
     *
     * @param string $name
     *   The tag name.
     * @param array $attributes
     *   The attributes.
     * @param mixed $content
     *   The content.
     * @param \Drupal\atomium\htmltag\Attributes\AttributesFactoryInterface|null $attributesFactory
     *   The attributes factory.
     * @param \Drupal\atomium\htmltag\Attribute\AttributeFactoryInterface|null $attributeFactory
     *   The attributes factory.
     *
     * @return \Drupal\atomium\Tag\TagInterface
     *   The tag.
     */
    public static function tag(
        $name,
        array $attributes = [],
        $content = false,
        AttributesFactoryInterface $attributesFactory = null,
        AttributeFactoryInterface $attributeFactory = null
    );

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
    public function attributes(array $attributes = [], AttributeFactoryInterface $attributeFactory = null);

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
    public static function attr($name, ...$value);
}
