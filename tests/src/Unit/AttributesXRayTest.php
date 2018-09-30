<?php

namespace Drupal\Tests\atomium\Unit;

use Drupal\atomium\Attributes;
use Drupal\Tests\atomium\XRay\Canvas\XRayCanvasInterface;
use Drupal\Tests\atomium\XRay\TestCase\XRayTestCaseBase;

/**
 * Tests the Attributes class using the XRay testing mechanism.
 */
class AttributesXRayTest extends XRayTestCaseBase {

  /**
   * Gets the xray export directory.
   *
   * @return string
   *   Path to the xray fixtures directory.
   */
  protected function getExpectedValuesDirectory() {
    return dirname(dirname(__DIR__)) . '/fixtures/xray/attributes';
  }

  /**
   * Generates values to be stored in or compared to the fixtures directory.
   *
   * @param \Drupal\Tests\atomium\XRay\Canvas\XRayCanvasInterface $canvas
   *   Canvas object to receive the values.
   */
  protected function generateValues(XRayCanvasInterface $canvas) {

    foreach ($this->buildOutputss() as $groupname => $outputs) {
      foreach ($outputs as $id => $output) {
        $canvas
          ->offset($groupname, $id)
          ->set($output);
      }
    }
  }

  /**
   * Builds output values.
   *
   * @return mixed[][]
   *   Format: $[$groupname][$id] = $output.
   */
  private function buildOutputss() {
    $outputss = [];

    $attributes = new Attributes(
      $values = [
        '_true_' => TRUE,
        '_false_' => FALSE,
        '_null_' => NULL,
        '_parts_' => ['aa', 'bb'],
        '_part_' => 'aa',
        '_empty_string_' => '',
        '_empty_array_' => [],
      ]);

    foreach ($values as $name => $value) {

      $name_export = var_export($name, TRUE);

      $output = $attributes->contains($name);
      $outputss['contains']["contains($name_export)"] = $output;

      $output = $attributes->exists($name);
      $outputss["exists"]["exists($name_export)"] = $output;

      $output = $attributes->offsetGet($name);
      $outputss["offsetGet"]["offsetGet($name_export)"] = $output;

      $output = $attributes->offsetExists($name);
      $outputss["offsetExists"]["offsetExists($name_export)"] = $output;

      $parts_to_test = [NULL, TRUE, FALSE, ''];
      $parts_to_test[] = $value;
      if (is_array($value)) {
        foreach ($value as $part) {
          $parts_to_test[] = $part;
        }
      }

      foreach ($parts_to_test as $v) {
        $v_export = var_export($v, TRUE);

        try {
          $output = $attributes->contains($name, $v);
        }
        catch (\Exception $e) {
          $output = get_class($e) . ' with message ' . $e->getMessage();
        }
        $outputss["contains"]["contains($name_export, $v_export)"] = $output;

        try {
          $output = $attributes->exists($name, $v);
        }
        catch (\Exception $e) {
          $output = get_class($e) . ' with message ' . $e->getMessage();
        }
        $outputss["exists"]["exists($name_export, $v_export)"] = $output;
      }
    }

    foreach ($this->buildValues() as $groupname => $valuess) {
      foreach ($valuess as $id => $values) {
        foreach ($this->buildAttributesFromValues($values) as $builderMethod => $attributes) {

          foreach ($this->buildOutputFromAttributes($attributes) as $outputMethod => $output) {
            $outputss["$groupname.$builderMethod.$outputMethod"][$id] = $output;
          }
        }
      }
    }

    return $outputss;
  }

  /**
   * Builds values that will be used to generate Attributes objects.
   *
   * @return array[][]
   *   Format: $[$groupname][$id][$attribute_name] = $attribute_value.
   */
  private function buildValues() {

    $valuess = [];

    $valuess['order-of-attributes'][''] = [
      'zzz' => TRUE,
      'x' => 'y',
      'aaa' => FALSE,
      'class' => ['sidebar'],
    ];

    $valuess['order-of-values'][''] = [
      'class' => ['z', 'b a'],
      'name' => ['z', 'b a'],
    ];

    foreach ([
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
    ] as $value) {
      $key = \json_encode($value);
      $valuess['unusual-values'][$key] = ['name' => $value];
      $valuess['unusual-values-enclosed'][$key] = ['name' => ['a', $value, 'z']];
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
      $valuess['broken-names'][$key] = [$name => 'value'];
      $valuess['broken-names-enclosed'][$key] = [
        'a' => TRUE,
        $name => 'value',
        'z' => TRUE,
      ];
    }

    return $valuess;
  }

  /**
   * Builds attributes from values in different ways.
   *
   * @param mixed[] $values
   *   Format: $[$attribute_name] = $attribute_value.
   *
   * @return \Drupal\atomium\Attributes[]
   *   Format: $[$builderMethod] = $attributes
   */
  private function buildAttributesFromValues(array $values) {
    $attributess = [];

    $attributes = new Attributes($values);
    $attributess['__construct'] = $attributes;

    $attributes = new Attributes();
    $attributes->setAttributes($values);
    $attributess['setAttributes'] = $attributes;

    $attributes = new Attributes();
    foreach ($values as $k => $v) {
      $attributes->setAttribute($k, $v);
    }
    $attributess['setAttribute-x'] = $attributes;

    $attributes = new Attributes();
    foreach ($values as $k => $v) {
      $attributes[$k] = $v;
    }
    $attributess['offsetSet'] = $attributes;

    $attributes = new Attributes();
    $attributes->merge($values);
    $attributess['merge'] = $attributes;

    $attributes = new Attributes();
    foreach ($values as $k => $v) {
      if (is_array($v)) {
        foreach ($v as $vv) {
          $attributes->merge([$k => $vv]);
        }
      }
    }
    $attributess['merge-x'] = $attributes;

    $attributes = new Attributes();
    foreach ($values as $k => $v) {
      $attributes->append($k, $v);
    }
    $attributess['append-x'] = $attributes;

    $attributes = new Attributes();
    foreach ($values as $k => $v) {
      if (is_array($v)) {
        foreach ($v as $vv) {
          $attributes->append($k, $vv);
        }
      }
    }
    $attributess['append-xx'] = $attributes;

    return $attributess;
  }

  /**
   * Builds output from an attributes object, in different ways.
   *
   * @param \Drupal\atomium\Attributes $attributes
   *   The attributes object.
   *
   * @return mixed[]
   *   Format: $[$outputMethod] = $output
   */
  private function buildOutputFromAttributes(Attributes $attributes) {
    $outputs = [];

    $instance = clone $attributes;
    $outputs['__toString'] = $instance->__toString();

    $instance = clone $attributes;
    $outputs['toArray'] = $instance->toArray();

    return $outputs;
  }

}
