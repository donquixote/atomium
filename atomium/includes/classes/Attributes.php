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
   * Constructor.
   *
   * @param mixed[] $attributes
   *   Format:
   *   $[$attribute_name_unsafe] = true|false|string|array(..)
   *   $[$attribute_name_unsafe] = $value :string
   *   $[$attribute_name_unsafe][] = $value_part :string
   */
  public function __construct(array $attributes = array()) {

    if ([] === $attributes) {
      return;
    }

    AttributesUtil::removeInvalidAttributeNames($attributes);

    foreach ($attributes as $name => $value) {
      if (\is_bool($value)) {
        $this->storage[$name] = TRUE;
      }
      elseif (\is_array($value)) {
        $this->keyCollectNestedStringsInArray($name, $value);
      }
      else {
        $value = (string) $value;
        $this->storage[$name][$value] = $value;
      }
    }
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
      return $this;
    }

    AttributesUtil::removeInvalidAttributeNames($attributes);

    foreach ($attributes as $name => $value) {
      if (is_numeric($name)) {
        $this->setAttribute($value, TRUE, $explode);
      }
      else {
        $this->setAttribute($name, $value, $explode);
      }
    }

    return $this;
  }

  /**
   * Gets the value of an attribute.
   *
   * @param mixed $name
   *   The attribute name.
   *
   * @return mixed|null
   *   The attribute value, or an empty array if the attribute does not exist.
   *
   * @see \ArrayAccess::offsetGet()
   */
  public function offsetGet($name) {
    if (!isset($this->storage[$name])) {
      return NULL;
    }
    $value = $this->storage[$name];
    if (\is_bool($value)) {
      return TRUE;
    }
    $return = array_values($value);
    return $return;
  }

  /**
   * Sets the value of an attribute.
   *
   * @param string|int $name
   *   The attribute name.
   * @param bool|string|string[] $value
   *   The value to set.
   *
   * @see \ArrayAccess::offsetSet()
   */
  public function offsetSet($name, $value = FALSE) {

    if (!AttributesUtil::attributeNameIsValidOrNotice($name)) {
      return;
    }

    if (\is_bool($value)) {
      $this->storage[$name] = TRUE;
    }
    elseif (\is_array($value)) {
      $this->storage[$name] = [];
      $this->keyCollectNestedStringsInArray($name, $value);
    }
    else {
      $value = (string) $value;
      $this->storage[$name] = [$value => $value];
    }
  }

  /**
   * Removes an attribute completely.
   *
   * @param string $name
   *   The attribute name.
   *
   * @see \ArrayAccess::offsetUnset()
   */
  public function offsetUnset($name) {
    unset($this->storage[$name]);
  }

  /**
   * Checks whether an attribute with the given name exists.
   *
   * @param string $name
   *   The attribute name.
   *
   * @return bool
   *   TRUE, if the attribute exists.
   *
   * @see \ArrayAccess::offsetExists()
   */
  public function offsetExists($name) {
    return isset($this->storage[$name])
      && [] !== $this->storage[$name];
  }

  /**
   * Sets values for an attribute key.
   *
   * @param string $name
   *   Name of the attribute.
   * @param string|array|bool $value
   *   Value(s) to set for the given attribute key.
   * @param bool $explode
   *   Should we explode attributes value ?
   *
   * @return $this
   */
  public function setAttribute($name, $value = FALSE, $explode = TRUE) {

    if (!AttributesUtil::attributeNameIsValidOrNotice($name)) {
      return $this;
    }

    if (\is_bool($value)) {
      $this->storage[$name] = TRUE;
    }
    elseif (\is_string($value)) {
      if (!$explode) {
        $this->storage[$name] = [$value => $value];
      }
      else {
        $parts = explode(' ', $value);
        $this->storage[$name] = array_combine($parts, $parts);
        unset($this->storage['']);
      }
    }
    elseif (\is_array($value)) {
      $this->storage[$name] = [];
      if (!$explode) {
        $this->keyCollectNestedStringsInArray($name, $value);
      }
      else {
        $this->keyCollectNestedFragmentsInArray($name, $value);
        unset($this->storage[$name]['']);
      }
    }
    else {
      $value = (string) $value;
      $this->storage[$name] = [$value => $value];
    }

    return $this;
  }

  /**
   * Append a value into an attribute.
   *
   * @param string $name
   *   The attribute's name.
   * @param string|array|bool $value
   *   The attribute's value.
   *
   * @return $this
   */
  public function append($name, $value = FALSE) {

    if (!AttributesUtil::attributeNameIsValidOrNotice($name)) {
      return $this;
    }

    if (\is_bool($value)) {
      $this->storage[$name] = TRUE;
    }
    elseif (\is_array($value)) {
      if (isset($this->storage[$name]) && \is_bool($this->storage[$name])) {
        unset($this->storage[$name]);
      }
      $this->keyCollectNestedStringsInArray($name, $value);
    }
    else {
      if (isset($this->storage[$name]) && \is_bool($this->storage[$name])) {
        unset($this->storage[$name]);
      }
      $value = (string) $value;
      $this->storage[$name][$value] = $value;
    }

    return $this;
  }

  /**
   * Remove a value from a specific attribute.
   *
   * @param string $name
   *   The attribute's name.
   * @param string|array|bool $value
   *   The attribute's value.
   *
   * @return $this
   */
  public function remove($name, $value = FALSE) {

    if (!isset($this->storage[$name])) {
      return $this;
    }

    if (\is_bool($value)) {
      unset($this->storage[$name]);
    }
    elseif (\is_bool($this->storage[$name])) {
      // Do nothing.
    }
    elseif (\is_array($value)) {
      foreach ($value as $part) {
        unset($this->storage[$name][$part]);
      }
    }
    else {
      unset($this->storage[$name][(string) $value]);
    }

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
    if (\is_string($name)) {
      unset($this->storage[$name]);
    }
    elseif (\is_array($name)) {
      // Support nested lists of keys to unset, for BC reasons.
      foreach ($name as $nested_arg) {
        $this->delete($nested_arg);
      }
    }

    return $this;
  }

  /**
   * Return the attributes.
   *
   * @param string $name
   *   The attributes's name.
   * @param array|string $value
   *   The attribute's value.
   *
   * @return static
   */
  public function without($name, $value) {
    $attributes = clone $this;

    return $attributes->remove($name, $value);
  }

  /**
   * Replace a value with another.
   *
   * @param string $name
   *   The attributes's name.
   * @param string $value
   *   The attribute's value.
   * @param string $replacement
   *   The replacement value.
   *
   * @return $this
   */
  public function replace($name, $value, $replacement) {
    if (isset($this->storage[$name][$value])) {
      $this->storage[$name][$value] = $replacement;
      $this->storage[$name] = array_combine($this->storage[$name], $this->storage[$name]);
    }

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
      foreach ($data->storage as $name => $value) {
        if (!isset($this->storage[$name])) {
          $this->storage[$name] = $value;
        }
        $existing_value = $this->storage[$name];
        if (\is_bool($existing_value) || \is_bool($value)) {
          // Simply overwrite.
          $this->storage[$name] = TRUE;
        }
        $this->storage[$name] += $value;
      }
    }
    elseif (\is_array($data)) {

      AttributesUtil::removeInvalidAttributeNames($data);

      foreach ($data as $name => $unchecked_value) {
        if (\is_bool($unchecked_value)) {
          $this->storage[$name] = $unchecked_value;
        }
        else {
          if (isset($this->storage[$name]) && \is_bool($this->storage[$name])) {
            $this->storage[$name] = [];
          }

          if (\is_array($unchecked_value)) {
            $this->keyCollectNestedStringsInArray($name, $unchecked_value);
          }
          else {
            $value = (string) $unchecked_value;
            $this->storage[$name][$value] = $value;
          }
        }
      }
    }

    return $this;
  }

  /**
   * Check if attribute exists.
   *
   * @param string $name
   *   Attribute name.
   * @param string|bool $value
   *   Attribute value.
   *
   * @return bool
   *   Whereas an attribute exists.
   */
  public function exists($name, $value = FALSE) {

    if (!isset($this->storage[$name])) {
      return FALSE;
    }

    if (NULL === $value) {
      // This case makes no sense, but the unit test wants this exact result.
      // @todo Either document that NULL is supported, or remove this case.
      return FALSE;
    }

    if (\is_bool($actual_value = $this->storage[$name])) {
      return $actual_value === $value;
    }

    if (\is_string($value)) {
      return isset($actual_value[$value]);
    }

    if (is_bool($value)) {
      return TRUE;
    }

    throw new \InvalidArgumentException("Second parameter is expected to be bool|string.");
  }

  /**
   * Check if attribute contains a value.
   *
   * @param string $name
   *   Attribute name.
   * @param string|bool $value
   *   Attribute value.
   *
   * @return bool
   *   Whereas an attribute contains a value.
   */
  public function contains($name, $value = FALSE) {

    if (!isset($this->storage[$name])) {
      return FALSE;
    }

    if (\is_bool($actual_value = $this->storage[$name])) {
      return $actual_value === $value;
    }

    if (\is_bool($value)) {
      return TRUE;
    }

    if (!\is_string($value)) {
      throw new \InvalidArgumentException("Second parameter is expected to be bool|string.");
    }

    if (isset($actual_value[$value])) {
      return TRUE;
    }

    foreach ($actual_value as $part) {
      if (FALSE !== strpos($part, $value)) {
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
    if (isset($values['placeholder']) && \is_array($values['placeholder'])) {
      $placeholder_value = [];
      foreach ($values['placeholder'] as $part) {
        $part = strip_tags($part);
        $placeholder_value[$part] = $part;
      }
      $values['placeholder'] = $placeholder_value;
    }

    // By default, sort the value of the class attribute.
    if (isset($values['class']) && \is_array($values['class'])) {
      asort($values['class']);
    }

    asort($values);

    $pieces = [];
    foreach ($values as $name => $value) {
      if (\is_bool($value)) {
        $pieces[] = ' ' . $name;
        continue;
      }
      // If the value is not boolean, it must be an array.
      $pieces[] = ' ' . $name . '="' . check_plain(implode(' ', $value)) . '"';
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
    foreach ($this->storage as $name => $value) {
      if (\is_bool($value)) {
        $values[$name] = TRUE;
      }
      else {
        $values[$name] = array_values($value);
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

    AttributesUtil::removeInvalidAttributeNames($storage);

    foreach ($storage as $name => $value) {
      if (\is_bool($value)) {
        continue;
      }
      if (!\is_array($value)) {
        throw new \InvalidArgumentException("Values in storage must be bool or array.");
      }
      if ($value !== array_map($value, $value)) {
        throw new \InvalidArgumentException("Array values in storage must have keys identical to their values.");
      }
    }

    $this->storage = $storage;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getIterator() {
    return new \ArrayIterator($this->toArray());
  }

  /**
   * Collects string values in a nested array, without exploding.
   *
   * @param string $name
   * @param array $tree
   */
  private function keyCollectNestedStringsInArray($name, array $tree) {
    foreach ($tree as $subtree) {
      if (\is_array($subtree)) {
        $this->keyCollectNestedStringsInArray($name, $subtree);
      }
      else {
        $value = (string) $subtree;
        $this->storage[$name][$value] = $value;
      }
    }
  }

  /**
   * @param string $name
   *   Attribute name where the nested fragments should be inserted.
   * @param array $tree
   *   Value array containing nested fragments.
   */
  private function keyCollectNestedFragmentsInArray($name, array $tree) {
    foreach ($tree as $subtree) {
      if (\is_string($subtree)) {
        foreach (explode(' ', $subtree) as $part) {
          $this->storage[$name][$part] = $part;
        }
      }
      elseif (\is_array($subtree)) {
        $this->keyCollectNestedFragmentsInArray($name, $subtree);
      }
      else {
        $value = (string) $subtree;
        $this->storage[$name][$value] = $value;
      }
    }
  }

}
