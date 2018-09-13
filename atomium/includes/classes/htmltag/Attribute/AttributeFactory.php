<?php

// @codingStandardsIgnoreStart
namespace Drupal\atomium\htmltag\Attribute;

/**
 * Class AttributeFactory
 */
class AttributeFactory implements AttributeFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public static function build($name, ...$value)
    {
        return new Attribute($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getInstance($name, ...$value)
    {
      return new Attribute($name, $value);
    }
}
