<?php

namespace Drupal\atomium;

/**
 * Class Attributes.
 *
 * @package Drupal\atomium
 */
class Attributes implements \ArrayAccess, \IteratorAggregate {

  /**
   * Stores the attribute data.
   *
   * @var mixed[]|bool[]|string[][]
   *   Format:
   *   $[$attribute_name_safe] = true|false|array(..)
   *   $[$attribute_name_safe][$value_part] = $value_part :string
   */
  private $storage = array();

  /**
   * @param mixed[] $attributes
   *   Format:
   *   $[$attribute_name_unsafe] = true|false|string|array(..)
   *   $[$attribute_name_unsafe] = $value :string
   *   $[$attribute_name_unsafe][] = $value_part :string
   *
   */
  public function __construct(array $attributes = array()) {

    if ([] === $attributes) {
      return;
    }

    foreach ($attributes as $key => $value) {
      if (is_bool($value)) {
        $this->storage[$key] = $value;
      }
      elseif (is_string($value)) {
        foreach (explode(' ', $value) as $part) {
          $this->storage[$key][$part] = $part;
        }
      }
      elseif (is_array($value)) {
        $this->keyCollectNestedFragmentsInArray($key, $value);
      }
    }

    $this->checkIntegrity();
  }

  /**
   * Set attributes.
   *
   * @param array|Attributes $attributes
   *   The attributes.
   * @param bool $explode
   *   Should we explode attributes value ?
   *
   * @return $this
   */
  public function setAttributes($attributes = array(), $explode = TRUE) {

    if ($attributes instanceof self) {
      // Attributes from another instance are already in the correct format.
      foreach ($attributes->storage as $name => $value) {
        $this->storage[$name] = $value;
      }
      $this->checkIntegrity();
      return $this;
    }

    foreach ($attributes as $name => $value) {
      if (is_numeric($name)) {
        $this->setAttribute($value, TRUE, $explode);
        $this->checkIntegrity();
      }
      else {
        $this->setAttribute($name, $value, $explode);
        $this->checkIntegrity();
      }
    }

    $this->checkIntegrity();

    return $this;
  }

  /**
   * @param mixed $name
   *
   * @return mixed|null
   */
  public function offsetGet($name) {
    if (!isset($this->storage[$name])) {
      return NULL;
    }
    $value = $this->storage[$name];
    if (is_bool($value)) {
      return $value;
    }
    unset($value['']);
    # asort($value);
    $return = array_values($value);
    return $return;
  }

  /**
   * @param string|int $name
   * @param bool|string|string[] $value
   *
   * @return $this|void
   */
  public function offsetSet($name, $value = FALSE) {

    if (is_bool($value)) {
      $this->storage[$name] = $value;
    }
    elseif (is_string($value)) {
      $this->storage[$name] = [];
      foreach (explode(' ', $value) as $part) {
        $this->storage[$name][$part] = $part;
      }
    }
    elseif (is_array($value)) {
      $this->storage[$name] = [];
      $this->keyCollectNestedFragmentsInArray($name, $value);
    }

    $this->checkIntegrity();
  }

  /**
   * {@inheritdoc}
   */
  public function offsetUnset($name) {
    unset($this->storage[$name]);

    $this->checkIntegrity();
  }

  /**
   * {@inheritdoc}
   */
  public function offsetExists($name) {
    return isset($this->storage[$name])
      && [] !== $this->storage[$name]
      && ['' => ''] !== $this->storage[$name];
  }

  /**
   * Sets values for an attribute key.
   *
   * @param string $attribute
   *   Name of the attribute.
   * @param string|array|bool $value
   *   Value(s) to set for the given attribute key.
   * @param bool $explode
   *   Should we explode attributes value ?
   *
   * @return $this
   */
  public function setAttribute($attribute, $value = FALSE, $explode = TRUE) {

    if (is_bool($value)) {
      $this->storage[$attribute] = $value;
    }
    elseif (is_string($value)) {
      if (!$explode) {
        $this->storage[$attribute] = [$value => $value];
      }
      else {
        $parts = explode(' ', $value);
        $this->storage[$attribute] = array_combine($parts, $parts);
      }
    }
    elseif (is_array($value)) {
      $this->storage[$attribute] = [];
      if (!$explode) {
        $this->keyCollectNestedStringsInArray($attribute, $value);
      }
      else {
        $this->keyCollectNestedFragmentsInArray($attribute, $value);
      }
    }

    $this->checkIntegrity();

    return $this;
  }

  /**
   * Append a value into an attribute.
   *
   * @param string $key
   *   The attribute's name.
   * @param string|array|bool $value
   *   The attribute's value.
   *
   * @return $this
   */
  public function append($key, $value = FALSE) {

    if (is_bool($value)) {
      $this->storage[$key] = $value;
    }
    elseif (is_string($value)) {
      # $this->storage[$key] = [];
      foreach (explode(' ', $value) as $part) {
        $this->storage[$key][$part] = $part;
      }
    }
    elseif (is_array($value)) {
      # $this->storage[$key] = [];
      $this->keyCollectNestedFragmentsInArray($key, $value);
    }

    $this->checkIntegrity();

    return $this;
  }

  /**
   * Remove a value from a specific attribute.
   *
   * @param string $key
   *   The attribute's name.
   * @param string|array|bool $value
   *   The attribute's value.
   *
   * @return $this
   */
  public function remove($key, $value = FALSE) {

    if (!isset($this->storage[$key])) {
      return $this;
    }

    if (is_bool($value)) {
      unset($this->storage[$key]);
    }
    elseif (is_bool($this->storage[$key])) {
      // Do nothing.
    }
    elseif (is_string($value)) {
      foreach (explode(' ', $value) as $part) {
        unset($this->storage[$key][$part]);
      }
    }
    elseif (is_array($value)) {
      foreach ($value as $part) {
        unset($this->storage[$key][$part]);
      }
    }

    $this->checkIntegrity();

    return $this;
  }

  /**
   * Delete an attribute.
   *
   * @param string|array $name
   *   The name of the attribute key to delete.
   *
   * @deprecated
   *
   * @return $this
   */
  public function removeAttribute($name = array()) {
    return $this->delete($name);
  }

  /**
   * Delete an attribute.
   *
   * @param string|array $name
   *   The name of the attribute key to delete.
   *
   * @return $this
   */
  public function delete($name = array()) {
    if (is_string($name)) {
      unset($this->storage[$name]);
    }
    elseif (is_array($name)) {
      // Support nested lists of keys to unset, for BC reasons.
      foreach ($name as $nested_arg) {
        $this->delete($nested_arg);
      }
    }

    $this->checkIntegrity();

    return $this;
  }

  /**
   * Return the attributes.
   *
   * @param string $key
   *   The attributes's name.
   * @param array|string $value
   *   The attribute's value.
   *
   * @return static
   */
  public function without($key, $value) {
    $attributes = clone $this;

    return $attributes->remove($key, $value);
  }

  /**
   * Replace a value with another.
   *
   * @param string $key
   *   The attributes's name.
   * @param string $value
   *   The attribute's value.
   * @param string $replacement
   *   The replacement value.
   *
   * @return $this
   */
  public function replace($key, $value, $replacement) {
    if (isset($this->storage[$key][$value])) {
      $this->storage[$key][$value] = $replacement;
      $this->storage[$key] = array_combine($this->storage[$key], $this->storage[$key]);
    }

    $this->checkIntegrity();

    return $this;
  }

  /**
   * Merge attributes.
   *
   * @param array $data
   *   The data to merge.
   *
   * @return $this
   */
  public function merge(array $data = array()) {

    if ($data instanceof self) {
      // Values from an instance of this class already have the correct format.
      foreach ($data->storage as $key => $value) {
        if (!isset($this->storage[$key])) {
          $this->storage[$key] = $value;
        }
        $existing_value = $this->storage[$key];
        if (is_bool($existing_value) || is_bool($value)) {
          // Simply overwrite.
          $this->storage[$key] = $value;
        }
        $this->storage[$key] += $value;
      }
      $this->checkIntegrity();
    }
    elseif (is_array($data)) {
      foreach ($data as $key => $unchecked_value) {
        if (is_bool($unchecked_value)) {
          $this->storage[$key] = $unchecked_value;
        }
        else {
          if (isset($this->storage[$key]) && is_bool($this->storage[$key])) {
            $this->storage[$key] = [];
          }

          $this->keyCollectNestedFragmentsInStringOrArray($key, $unchecked_value);
          $this->checkIntegrity();
        }
      }
      $this->checkIntegrity();
    }

    $this->checkIntegrity();

    return $this;
  }

  /**
   * Check if attribute exists.
   *
   * @param string $key
   *   Attribute name.
   * @param string|bool $value
   *   Attribute value.
   *
   * @return bool
   *   Whereas an attribute exists.
   */
  public function exists($key, $value = FALSE) {

    if (!isset($this->storage[$key])) {
      return FALSE;
    }

    if (NULL === $value) {
      // This case makes no sense, but the unit test wants this exact result.
      // @todo Either document that NULL is supported, or remove this case.
      return FALSE;
    }

    if (is_bool($actual_value = $this->storage[$key])) {
      return $actual_value === $value;
    }

    if (is_string($value)) {
      return isset($actual_value[$value]);
    }

    throw new \InvalidArgumentException("Second parameter is expected to be bool|string.");
  }

  /**
   * Check if attribute contains a value.
   *
   * @param string $key
   *   Attribute name.
   * @param string|bool $value
   *   Attribute value.
   *
   * @return bool
   *   Whereas an attribute contains a value.
   */
  public function contains($key, $value = FALSE) {

    if (!isset($this->storage[$key])) {
      return FALSE;
    }

    if (is_bool($actual_value = $this->storage[$key])) {
      return $actual_value === $value;
    }

    if (!is_string($value)) {
      throw new \InvalidArgumentException("Second parameter is expected to be bool|string.");
    }

    if (isset($actual_value[$value])) {
      return TRUE;
    }

    foreach ($actual_value as $part) {
      if (false !== strpos($part, $value)) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function __toString() {

    // If empty, just return an empty string.
    if ([] === $this->storage) {
      return '';
    }

    $values = $this->storage;

    // The 'placeholder' attribute is special, for some reason..
    if (isset($values['placeholder']) && is_array($values['placeholder'])) {
      $placeholder_value = [];
      foreach ($values['placeholder'] as $part) {
        $part = strip_tags($part);
        $placeholder_value[$part] = $part;
      }
      $values['placeholder'] = $placeholder_value;
    }

    // By default, sort the value of the class attribute.
    if (isset($values['class']) && is_array($values['class'])) {
      asort($values['class']);
    }

    asort($values);

    $pieces = [];
    foreach ($values as $key_unsafe => $value) {
      $key_safe = check_plain(trim($key_unsafe));
      if (is_bool($value)) {
        $pieces[] = ' ' . $key_safe;
        continue;
      }
      // If the value is not boolean, it must be an array.
      unset($value['']);
      $pieces[] = ' ' . $key_safe . '="' . check_plain(implode(' ', $value)) . '"';
    }

    // Sort the attributes.
    sort($pieces);

    return implode('', $pieces);
  }

  /**
   * Returns all storage elements as an array.
   *
   * @return array
   *   An associative array of attributes.
   */
  public function toArray() {
    $values = [];
    foreach ($this->storage as $key => $value) {
      if (is_bool($value)) {
        $values[$key] = [];
      }
      else {
        unset($value['']);
        # ksort($value);
        $values[$key] = array_keys($value);
      }
    }
    return $values;
  }

  /**
   * Returns the whole array.
   *
   * @return array
   *   The storage.
   *
   * @todo Perhaps this can be removed.
   */
  public function getStorage() {
    return $this->storage;
  }

  /**
   * Set the storage array.
   *
   * @param array $storage
   *   The storage.
   *
   * @return $this
   */
  public function setStorage(array $storage = array()) {

    foreach ($storage as $key => $value) {
      if (is_bool($value)) {
        continue;
      }
      if (!is_array($value)) {
        throw new \InvalidArgumentException("Values in storage must be bool or array.");
      }
      if ($value !== array_map($value, $value)) {
        throw new \InvalidArgumentException("Array values in storage must have keys identical to their values.");
      }
    }

    $this->storage = $storage;

    $this->checkIntegrity();

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getIterator() {
    return new \ArrayIterator($this->toArray());
  }

  private function checkIntegrity() {
    foreach ($this->storage as $key => $value) {
      if (is_bool($value)) {
        continue;
      }
      if (!is_array($value)) {
        $export = var_export($value, TRUE);
        throw new \RuntimeException("Values must be array|bool, $export found instead.");
      }
      if ($value !== array_combine($value, $value)) {
        throw new \RuntimeException("Array values must have keys identical to values.");
      }
    }
  }

  /**
   * Collects string values in a nested array, without exploding.
   *
   * @param string $key
   * @param array $tree
   */
  private function keyCollectNestedStringsInArray($key, array $tree) {
    foreach ($tree as $subtree) {
      if (is_string($subtree)) {
        $this->storage[$key][$subtree] = $subtree;
      }
      elseif (is_array($subtree)) {
        $this->keyCollectNestedStringsInArray($key, $subtree);
      }
    }
  }

  /**
   * Collects exploded fragments from string values in a nested array.
   *
   * @param string $key
   * @param array|string $value
   */
  private function keyCollectNestedFragmentsInStringOrArray($key, $value) {
    if (is_string($value)) {
      foreach (explode(' ', $value) as $part) {
        $this->storage[$key][$part] = $part;
      }
    }
    elseif (is_array($value)) {
      $this->keyCollectNestedFragmentsInArray($key, $value);
    }
  }

  /**
   * @param string $key
   * @param array $tree
   */
  private function keyCollectNestedFragmentsInArray($key, array $tree) {
    foreach ($tree as $subtree) {
      if (is_string($subtree)) {
        foreach (explode(' ', $subtree) as $part) {
          $this->storage[$key][$part] = $part;
        }
      }
      elseif (is_array($subtree)) {
        $this->keyCollectNestedFragmentsInArray($key, $subtree);
      }
    }
  }

}
