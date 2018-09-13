<?php

// @codingStandardsIgnoreStart
namespace Drupal\atomium\htmltag\Tag;

use Drupal\atomium\htmltag\Attribute\AttributeFactory;
use Drupal\atomium\htmltag\Attributes\AttributesFactory;
use Drupal\atomium\htmltag\Attributes\AttributesInterface;
use Drupal\atomium\htmltag\Attributes\AttributesFactoryInterface;
use Drupal\atomium\htmltag\Attribute\AttributeFactoryInterface;

/**
 * Class TagFactory
 */
class TagFactory implements TagFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public static function build(
        $name,
        array $attributes = [],
        $content = false,
        AttributesFactoryInterface $attributesFactory = null,
        AttributeFactoryInterface $attributeFactory = null
    ) {
        /** @var AttributeFactoryInterface $attributeFactory */
        $attributeFactory = null === $attributeFactory ?
            (new \ReflectionClass(AttributeFactory::class))->newInstance():
            $attributeFactory;

        if (null === $attributesFactory) {
            $attributesFactory = new \ReflectionClass(AttributesFactory::class);

            /** @var AttributesFactoryInterface $attributesFactory */
            $attributesFactory = $attributesFactory->newInstance();
        }

        /** @var AttributesInterface $attributes */
        $attributes = ($attributesFactory)::build($attributes, $attributeFactory);

        return new Tag($attributes, $name, $content);
    }
}
