<?php

namespace Drupal\atomium;

/**
 * Class AttributesContainer.
 *
 * @package Drupal\atomium
 */
class AttributesContainer implements \ArrayAccess {

  /**
   * Stores the attribute data.
   *
   * @var \Drupal\atomium\Attributes[]
   *   Format: $[$channel] = $attributes
   */
  protected $storage = array();

  /**
   * AttributesContainer constructor.
   *
   * @param mixed[][] $attributess
   *   Format:
   *   - $[$channel][$attribute_name] = $attribute_value :string
   *   - $[$channel][$attribute_name][] = $attribute_value_part
   *   - $[$channel][$attribute_name] = FALSE|TRUE  (attributes without value)
   */
  public function __construct(array $attributess = array()) {
    foreach ($attributess as $channel => $attributes) {
      $this->storage[$channel] = new Attributes($attributes);
    }
  }

  /**
   * @param string|int $channel
   *
   * @return \Drupal\atomium\Attributes
   */
  public function &offsetGet($channel) {
    if (!isset($this->storage[$channel])) {
      $this->storage[$channel] = new Attributes();
    }
    return $this->storage[$channel];
  }

  /**
   * {@inheritdoc}
   */
  public function offsetSet($channel, $attributes) {
    if (!is_array($attributes)) {
      throw new \InvalidArgumentException('$attributes must be an array.');
    }
    if (isset($this->storage[$channel])) {
      $this->storage[$channel]->setAttributes($attributes);
    }
    else {
      $this->storage[$channel] = new Attributes($attributes);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function offsetUnset($channel) {
    unset($this->storage[$channel]);
  }

  /**
   * @param mixed $channel
   *
   * @return bool
   */
  public function offsetExists($channel) {
    // @todo Also check if empty?
    return isset($this->storage[$channel]);
  }

  /**
   * Returns the whole array.
   */
  public function getStorage() {
    return $this->storage;
  }

}
