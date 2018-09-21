<?php

namespace Drupal\Tests\atomium\Unit;

use Drupal\atomium\Attributes;
use Drupal\Tests\atomium\XRay\Canvas\XRayCanvasInterface;
use Drupal\Tests\atomium\XRay\TestCase\XRayTestCaseBase;

class AttributesXRayTest extends XRayTestCaseBase {

  /**
   * @return string
   */
  protected function getExpectedValuesDirectory() {
    return dirname(dirname(__DIR__)) . '/fixtures/xray/attributes';
  }

  /**
   * @param \Drupal\Tests\atomium\XRay\Canvas\XRayCanvasInterface $canvas
   */
  protected function generateValues(XRayCanvasInterface $canvas) {

    $this->generateForArray(
      $canvas->offset('order-of-attributes'),
      [
        'zzz' => TRUE,
        'x' => 'y',
        'aaa' => FALSE,
        'class' => ['sidebar'],
      ]);

    $this->generateForArray(
      $canvas->offset('order-of-values'),
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
      $key = \json_encode($value);

      $this->generateForArray(
        $canvas->offset('unusual-values', $key),
        ['name' => $value]);

      $this->generateForArray(
        $canvas->offset('unusual-values-enclosed', $key),
        ['name' => ['a', $value, 'z']]);
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
      $key = \json_encode($name);

      $this->generateForArray(
        $canvas->offset('broken-names', $key),
        [$name => 'value']);

      $this->generateForArray(
        $canvas->offset('broken-names-enclosed', $key),
        [
          'a' => TRUE,
          $name => 'value',
          'z' => TRUE,
        ]);
    }
  }

  /**
   * @param \Drupal\Tests\atomium\XRay\Canvas\XRayCanvasInterface $canvas
   * @param mixed[] $values
   *   Format: $[$attribute_name] = $attribute_value
   */
  private function generateForArray(XRayCanvasInterface $canvas, array $values) {

    $attributes = new Attributes($values);
    $this->generateForAttributes(
      $canvas->offset('__construct'),
      $attributes);

    $attributes = new Attributes();
    $attributes->setAttributes($values);
    $this->generateForAttributes(
      $canvas->offset('setAttributes'),
      $attributes);

    $attributes = new Attributes();
    foreach ($values as $k => $v) {
      $attributes->setAttribute($k, $v);
    }
    $this->generateForAttributes(
      $canvas->offset('setAttribute-x'),
      $attributes);

    $attributes = new Attributes();
    foreach ($values as $k => $v) {
      $attributes[$k] = $v;
    }
    $this->generateForAttributes(
      $canvas->offset('offsetSet'),
      $attributes);

    $attributes = new Attributes();
    $attributes->merge($values);
    $this->generateForAttributes(
      $canvas->offset('merge'),
      $attributes);

    $attributes = new Attributes();
    foreach ($values as $k => $v) {
      if (is_array($v)) {
        foreach ($v as $vv) {
          $attributes->merge([$k => $vv]);
        }
      }
    }
    $this->generateForAttributes(
      $canvas->offset('merge-x'),
      $attributes);

    $attributes = new Attributes();
    foreach ($values as $k => $v) {
      $attributes->append($k, $v);
    }
    $this->generateForAttributes(
      $canvas->offset('append-x'),
      $attributes);

    $attributes = new Attributes();
    foreach ($values as $k => $v) {
      if (is_array($v)) {
        foreach ($v as $vv) {
          $attributes->append($k, $vv);
        }
      }
    }

    $this->generateForAttributes(
      $canvas->offset('append-xx'),
      $attributes);
  }

  /**
   * @param \Drupal\Tests\atomium\XRay\Canvas\XRayCanvasInterface $canvas
   * @param \Drupal\atomium\Attributes $attributes
   */
  private function generateForAttributes(XRayCanvasInterface $canvas, Attributes $attributes) {

    $instance = clone $attributes;
    $canvas->offsetPath('__toString')
      ->set($instance->__toString());

    $instance = clone $attributes;
    $canvas->offsetPath('toArray')
      ->set($instance->toArray());
  }
}
