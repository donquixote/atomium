<?php

// @codingStandardsIgnoreStart
namespace Drupal\atomium\htmltag\Tag;

use Drupal\atomium\htmltag\Attributes\AttributesFactoryInterface;

/**
 * Interface TagFactoryInterface
 */
interface TagFactoryInterface
{

    /**
     * Create a new tag.
     *
     * @param string $name
     *   The tag name.
     * @param array $attributes
     *   The tag attributes.
     * @param mixed $content
     *   The tag content.
     * @param \Drupal\atomium\htmltag\Attributes\AttributesFactoryInterface $attributesFactory
     *   The attributes factory.
     *
     * @return \Drupal\atomium\htmltag\Tag\TagInterface
     *   The tag.
     */
    public static function build(
        $name,
        array $attributes = [],
        $content = false,
        AttributesFactoryInterface $attributesFactory = null
    );
}
