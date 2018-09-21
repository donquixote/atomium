<?php

namespace Drupal\Tests\atomium\Unit;

use Drupal\atomium\Attributes;

class AttributesValueGenerator {

  public function generate() {

    $spec = [];

    $spec['order of attributes'] = $this->generateForArray(
      [
        'zzz' => TRUE,
        'x' => 'y',
        'aaa' => FALSE,
        'class' => ['sidebar'],
      ]);

    $spec['order of values'] = $this->generateForArray(
      [
        'class' => ['z', 'b a'],
        'name' => ['z', 'b a'],
      ]);

    $values = [
      'value',
      'value ',
      ' value',
      '  value  ',
      'va l  ue',
      'mm mm',
      'val$u"e',
      '',
      ' ',
      '  ',
      TRUE,
      FALSE,
      [TRUE],
      [TRUE, TRUE],
      [FALSE, FALSE],
      ['', ''],
      ['x', 5, 5.1, TRUE, FALSE, ''],
      0,
      3,
      [1, 2, 3],
      [1, [2, [3]]],
      1.7,
      [1.7],
      [1.7, [1.9]],
      ['a', 'b', 'c'],
      ['a ', ' b ', ' c ', ' d ', ' e'],
      ['a', ['b', ['c']]],
    ];

    foreach ($values as $value) {
      $key = var_export($value, TRUE);
      $spec['unusual values'][$key] = $this->generateForArray(
        ['value' => $value]);
      $spec['unusual values enclosed'][$key] = $this->generateForArray(
        ['value' => ['a', $value, 'z']]);
    }

    foreach ([
      'name ',
      'name  ',
      ' name',
      '  name',
      'na m  e',
      'nam$e',
      'nam"e',
      '',
      ' ',
      '  ',
    ] as $name) {
      $key = var_export($name, TRUE);
      $spec['broken names'][$key] = $this->generateForArray([$name => 'value']);
      $spec['broken names enclosed'][$key] = $this->generateForArray(
        [
          'a' => TRUE,
          $name => 'value',
          'z' => TRUE,
        ]);
    }

    return $spec;
  }

  private function generateForArray(array $values) {
    $spec = [];

    $attributes = new Attributes($values);
    $spec['(new *)'] = $this->generateForAttributes($attributes);

    $attributes = new Attributes();
    $attributes->setAttributes($values);
    $spec['->setAttributes(*)'] = $this->generateForAttributes($attributes);

    $attributes = new Attributes();
    foreach ($values as $k => $v) {
      $attributes->setAttribute($k, $v);
    }
    $spec['->setAttribute(*, *) x'] = $this->generateForAttributes($attributes);

    $attributes = new Attributes();
    foreach ($values as $k => $v) {
      $attributes[$k] = $v;
    }
    $spec['[*] = *'] = $this->generateForAttributes($attributes);

    $attributes = new Attributes();
    foreach ($values as $k => $v) {
      $attributes->append($k, $v);
    }
    $spec['->append(*, *) x'] = $this->generateForAttributes($attributes);

    $attributes = new Attributes();
    foreach ($values as $k => $v) {
      if (is_array($v)) {
        foreach ($v as $vv) {
          $attributes->append($k, $vv);
        }
      }
    }
    $spec['->append(*, *) xx'] = $this->generateForAttributes($attributes);

    return $spec;
  }

  /**
   * @param \Drupal\atomium\Attributes $attributes
   *
   * @return array
   */
  private function generateForAttributes(Attributes $attributes) {
    $spec = [];

    $instance = clone $attributes;
    $spec['->__toString()'][':'] = $instance->__toString();

    $instance = clone $attributes;
    $spec['->toArray()'][':'] = $instance->toArray();

    return $spec;
  }

}
