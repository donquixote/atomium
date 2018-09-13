<?php

// @codingStandardsIgnoreStart
namespace Drupal\atomium\htmltag\Attributes;

use Drupal\atomium\htmltag\Attribute\AttributeFactory;
use Drupal\atomium\htmltag\Attribute\AttributeFactoryInterface;

/**
 * Class AttributesFactory
 */
class AttributesFactory implements AttributesFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public static function build(
        array $attributes = [],
        AttributeFactoryInterface $attributeFactory = null
    ) {
        if (null === $attributeFactory) {
            $reflection = new \ReflectionClass(AttributeFactory::class);
            /** @var \Drupal\atomium\htmltag\Attribute\AttributeFactoryInterface $attributeFactory */
            $attributeFactory = $reflection->newInstance();
        }

        return new Attributes(
            $attributeFactory,
            $attributes
        );
    }
}
