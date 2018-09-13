<?php

// @codingStandardsIgnoreStart
namespace Drupal\atomium\htmltag\Tag;

/**
 * Interface TagInterface.
 */
interface TagInterface
{
    /**
     * Get the attributes as string or a specific attribute if $name is provided.
     *
     * @param string $name
     *   The name of the attribute.
     *
     * @param mixed ...$value
     *   The value.
     *
     * @return string|\Drupal\atomium\htmltag\Attribute\AttributeInterface
     *   The attributes as string or a specific Attribute object.
     */
    public function attr($name = null, ...$value);

    /**
     * @param array|bool ...$content
     *   The content.
     *
     * @return string
     *   The content.
     */
    public function content(...$content);

    /**
     * Render the tag.
     *
     * @return string
     *   The tag in html.
     */
    public function render();
}
