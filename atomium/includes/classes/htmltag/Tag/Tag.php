<?php

// @codingStandardsIgnoreStart
namespace Drupal\atomium\htmltag\Tag;

use Drupal\atomium\AbstractBaseHtmlTagObject;
use Drupal\atomium\htmltag\Attributes\AttributesInterface;

/**
 * Class Tag.
 */
class Tag extends AbstractBaseHtmlTagObject implements TagInterface
{
    /**
     * The tag name.
     *
     * @var string
     */
    private $tag;

    /**
     * The tag attributes.
     *
     * @var \Drupal\atomium\htmltag\Attributes\AttributesInterface
     */
    private $attributes;

    /**
     * The tag content.
     *
     * @var mixed[]|null
     */
    private $content;

    /**
     * Tag constructor.
     *
     * @param \Drupal\atomium\htmltag\Attributes\AttributesInterface $attributes
     * @param string $name
     *   The tag name.
     * @param mixed $content
     *   The content.
     */
    public function __construct(AttributesInterface $attributes, $name, $content = false)
    {
        $this->tag = $name;
        $this->attributes = $attributes;
        $this->content($content);
    }

    /**
     * @param string $name
     * @param array $arguments
     *
     * @return \Drupal\atomium\Tag\TagInterface
     */
    public static function __callStatic($name, array $arguments = [])
    {
        return new static($arguments[0], $name);
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
    public function attr($name = null, ...$value)
    {
        if (null === $name) {
            return $this->attributes->render();
        }

        if ([] === $value) {
            return $this->attributes[$name];
        }

        return $this->attributes[$name]->set($value);
    }

    /**
     * {@inheritdoc}
     */
    public function content(...$content)
    {
        if ([] !== $content) {
            if (reset($content) === false) {
                $content = null;
            }

            $this->content = $content;
        }

        return $this->renderContent();
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $output = sprintf('<%s%s', $this->tag, $this->attributes);

        $output .= null === $this->content ?
            '/>':
            sprintf('>%s</%s>', $this->renderContent(), $this->tag);

        return $output;
    }

    /**
     * Render the tag content.
     *
     * @return string
     */
    private function renderContent()
    {
        return implode(
            '',
            array_filter(
                array_map(
                    function ($content_item) {
                        $output = '';

                        // Make sure we can 'stringify' the item.
                        if (!is_array($content_item) &&
                            ((!is_object($content_item) && settype($content_item, 'string') !== false) ||
                                (is_object($content_item) && method_exists($content_item, '__toString')))) {
                            $output = (string) $content_item;
                        }

                        return $output;
                    },
                    $this->ensureFlatArray($this->ensureArray($this->content))
                ),
                'strlen'
            )
        );
    }
}
