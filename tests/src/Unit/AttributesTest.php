<?php

namespace Drupal\Tests\atomium\Unit;

use Drupal\atomium\Attributes;
use Symfony\Component\Yaml\Yaml;

/**
 * Class AttributesTest.
 *
 * @package Drupal\Tests\atomium
 */
class AttributesTest extends AbstractUnitTest {

  public function _testTrouble() {
    $attributes = new Attributes(['class' => ['one', 'two']]);

    self::assertStorage(
      ['class' => ['one' => 'one', 'two' => 'two']],
      $attributes);

    $attributes->append('class', TRUE);

    self::assertStorage(
      ['class' => true],
      $attributes);

    $attributes->append('class', ['one', 'two three']);

    self::assertStorage(
      ['class' => [true, 'one', 'two', 'three']],
        $attributes);
  }

  public function testAppend() {

    $attributes = new Attributes(['class' => ['one', 'two']]);

    self::assertStorage(
      ['class' => ['one' => 'one', 'two' => 'two']],
      $attributes);

    $attributes->append('class', ['one', 'two']);

    self::assertStorage(
      ['class' => ['one' => 'one', 'two' => 'two']],
      $attributes);

    $attributes->append('class', ['one', 'two']);

    $attr_clone = clone $attributes;

    self::assertStorage(
      ['class' => ['one' => 'one', 'two' => 'two']],
      $attributes);

    self::assertSame(
      ['class' => ['one', 'two']],
      $attributes->toArray());

    self::assertSame(
      ' class="one two"',
      $attributes->__toString());

    self::assertSame(
      ' class="one two"',
      $attr_clone->__toString());
  }

  /**
   * @param array $expected_storage
   * @param \Drupal\atomium\Attributes $attributes
   * @param string $message
   */
  private static function assertStorage(array $expected_storage, Attributes $attributes, $message = '') {
    // Grab private property value.
    $array = (array)$attributes;
    $actual_storage = reset($array);
    self::assertSame($expected_storage, $actual_storage, $message);
  }

  /**
   * @param \Drupal\atomium\Attributes $attributes
   * @param string $method
   */
  private static function assertIntegrity(Attributes $attributes, $method) {
    $array = (array)$attributes;
    $actual_storage = reset($array);
    foreach ($actual_storage as $key => $value) {
      if (is_bool($value)) {
        continue;
      }
      if (is_array($value)) {
        foreach ($value as $k => $v) {
          self::assertSame((string)$k, $v, "Integrity fail in Attributes object at key \$['$key']['$k'] after ->$method().");
        }
        continue;
      }
      self::assertSame([], $value, "Integrity fail in Attributes object at key '$key': Value must be bool or array.");
    }
  }

  /**
   * Test class methods.
   *
   * @dataProvider methodsProvider
   */
  public function testMethods(array $given, array $runs, array $expects) {
    $attributes = new Attributes($given);

    foreach ($runs as $run) {
      foreach ($run as $method => $arguments) {
        call_user_func_array([$attributes, $method], $arguments);
        self::assertIntegrity($attributes, $method);
      }
    }

    foreach ($expects as $expect) {
      foreach ($expect as $method => $item) {
        $item += array('arguments' => array());

        $actual = call_user_func_array(
          array($attributes, $method),
          $item['arguments']
        );
        self::assertIntegrity($attributes, $method);

        if ($item['return'] !== $actual) {
          # self::assertStorage(['?'], $attributes, "\$attributes->$method() STORAGE");
        }

        self::assertSame(
          var_export($item['return'], TRUE),
          var_export($actual, TRUE),
          "\$attributes->$method()");

        expect($actual)->to->equal($item['return']);
      }
    }
  }

  /**
   * Test class methods.
   *
   * @covers Attributes::exists
   * @covers Attributes::contains
   */
  public function testVariousMethod() {
    $attributes = array(
      'class' => array(
        'foo',
        'bar',
      ),
      'id' => 'atomium',
      'data-closable' => FALSE,
    );

    $attributes = new Attributes($attributes);

    expect($attributes->exists('class', 'foo'))->to->equal(TRUE);
    expect($attributes->exists('class', 'fooled'))->to->equal(FALSE);
    expect($attributes->exists('foo', 'bar'))->to->equal(FALSE);
    expect($attributes->exists('class', NULL))->to->equal(FALSE);
    expect($attributes->exists('id', 'atomium'))->to->equal(TRUE);
    expect($attributes->exists('data-closable', FALSE))->to->equal(TRUE);
    expect($attributes->exists('data-closable'))->to->equal(TRUE);

    expect($attributes->contains('class', 'fo'))->to->equal(TRUE);
    expect($attributes->contains('role'))->to->equal(FALSE);
    expect($attributes->contains('id', 'tomi'))->to->equal(TRUE);
  }

  /**
   * Methods provider.
   *
   * @return array
   *   Test data.
   */
  public function methodsProvider() {
    return Yaml::parse(file_get_contents(__DIR__ . '/../../fixtures/attributes_class.yml'));
  }

}
