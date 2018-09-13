<?php

// @codingStandardsIgnoreStart
namespace Drupal\atomium\htmltag;

use Drupal\atomium\htmltag\Attribute\Attribute;
use Drupal\atomium\htmltag\Attribute\AttributeFactory;
use Drupal\atomium\htmltag\Attribute\AttributeFactoryInterface;
use Drupal\atomium\htmltag\Attributes\Attributes;
use Drupal\atomium\htmltag\Attributes\AttributesInterface;
use Drupal\atomium\htmltag\Attributes\AttributesFactory;
use Drupal\atomium\htmltag\Attributes\AttributesFactoryInterface;
use Drupal\atomium\Tag\Tag;

/**
 * Class HtmlTag
 */
final class HtmlTag implements HtmlTagInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tag(
        $name,
        array $attributes = [],
        $content = false,
        AttributesFactoryInterface $attributesFactory = null,
        AttributeFactoryInterface $attributeFactory = null
    ) {
        /** @var AttributeFactoryInterface $attributeFactory */
        $attributeFactory = null === $attributeFactory?
            (new \ReflectionClass(AttributeFactory::class))->newInstance():
            $attributeFactory;

        /** @var AttributesFactoryInterface $attributesFactory */
        $attributesFactory = null === $attributesFactory ?
            (new \ReflectionClass(AttributesFactory::class))->newInstance():
            $attributesFactory;

        /** @var AttributesInterface $attributes */
        $attributes = ($attributesFactory)::build($attributes, $attributeFactory);

        return new Tag($attributes, $name, $content);
    }

    /**
     * {@inheritdoc}
     */
    public function attributes(array $attributes = [], AttributeFactoryInterface $attributeFactory = null)
    {
        if (null === $attributeFactory) {
            /** @var \Drupal\atomium\htmltag\Attribute\AttributeFactoryInterface $attributeFactory */
            $attributeFactory = (new \ReflectionClass(AttributeFactory::class))
                ->newInstance();
        }

        return new Attributes(
            $attributeFactory,
            $attributes
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function attr($name, ...$value)
    {
        return new Attribute($name, $value);
    }
}
