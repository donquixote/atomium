<?php

namespace Drupal\Tests\atomium\Unit;

use Drupal\atomium\Attributes;

/**
 * Tests the Attributes class.
 */
class AttributesExplicitTest extends UnitTestBase {

  /**
   * Tests whether attributes are ordered by name.
   */
  public function testAttributesOrder() {

    self::assertToString(
      ' aaa class="sidebar" x="y" zzz',
      new Attributes(
        [
          'zzz' => TRUE,
          'x' => 'y',
          'aaa' => FALSE,
          'class' => ['sidebar'],
        ]));
  }

  /**
   * Tests whether attributes value fragments are sorted.
   */
  public function testValueOrder() {
    self::assertToString(
      ' class="a b z" name="z b a"',
      new Attributes(
        [
          'class' => ['z', 'b a'],
          'name' => ['z', 'b a'],
        ]));
  }

  /**
   * Tests unusual attribute values.
   */
  public function testUnusualAttributeValueStrings() {

    foreach ([
      'value' => 'value',
      'value ' => 'value',
      ' value' => 'value',
      '  value  ' => 'value',
      'va l  ue' => 'va l ue',
      'mm mm' => 'mm',
      'val$u"e' => 'val$u&quot;e',
      '' => '',
      ' ' => '',
      '  ' => '',
    ] as $value => $expected_value) {

      $expected = ' name="' . $expected_value . '"';

      self::assertToString(
        $expected,
        new Attributes(['name' => $value]),
        var_export($value, TRUE));

      $attributes = new Attributes();
      $attributes->append('name', $value);

      self::assertToString(
        $expected,
        $attributes,
        var_export($value, TRUE) . ' (append)');
    }
  }

  /**
   * Tests unusual attribute values.
   */
  public function testUnusualAttributeValueStringsEnclosed() {

    foreach ([
      'value' => 'a value z',
      'value ' => 'a value z',
      ' value' => 'a value z',
      '  value  ' => 'a value z',
      'va l  ue' => 'a va l ue z',
      'mm mm' => 'a mm z',
      'val$u"e' => 'a val$u&quot;e z',
      '' => 'a z',
      ' ' => 'a z',
      '  ' => 'a z',
    ] as $value => $expected_value) {

      $expected = ' name="' . $expected_value . '"';

      self::assertToString(
        $expected,
        new Attributes(['name' => ['a', $value, 'z']]),
        var_export($value, TRUE));

      $attributes = new Attributes();
      $attributes->append('name', 'a');
      $attributes->append('name', $value);
      $attributes->append('name', 'z');

      self::assertToString(
        $expected,
        $attributes,
        var_export($value, TRUE) . ' (append)');
    }
  }

  /**
   * Tests behavior for broken attribute names.
   *
   * Note that this is an incorrect use of this library.
   */
  public function testBrokenAttributeNames() {

    foreach ([
      'name ' => ' name ="value"',
      'name  ' => ' name  ="value"',
      ' name' => '  name="value"',
      '  name' => '   name="value"',
      'na m  e' => ' na m  e="value"',
      'nam$e' => ' nam$e="value"',
      'nam"e' => ' nam"e="value"',
      '' => ' ="value"',
      ' ' => '  ="value"',
      '  ' => '   ="value"',
    ] as $name => $expected) {

      self::assertToString(
        $expected,
        new Attributes([$name => 'value']),
        var_export($name, TRUE));
    }
  }

  /**
   * Tests behavior for broken attribute names.
   *
   * Note that this is an incorrect use of this library.
   */
  public function testBrokenAttributeNamesEnclosed() {

    foreach ([
      'name ' => ' a name ="value" z',
      'name  ' => ' a name  ="value" z',
      ' name' => '  name="value" a z',
      '  name' => '   name="value" a z',
      'na m  e' => ' a na m  e="value" z',
      'nam$e' => ' a nam$e="value" z',
      '' => ' ="value" a z',
      ' ' => '  ="value" a z',
      '  ' => '   ="value" a z',
    ] as $name => $expected) {

      self::assertToString(
        $expected,
        new Attributes(
          [
            'a' => TRUE,
            $name => 'value',
            'z' => TRUE,
          ]),
        var_export($name, TRUE));
    }
  }

  /**
   * Tests behavior for broken attribute names.
   *
   * Note that this is an incorrect use of this library.
   */
  public function testBrokenAttributeNamesBoolean() {

    foreach ([
      'name ' => ' name',
      'name  ' => ' name',
      ' name' => ' name',
      '  name' => ' name',
      'na m  e' => ' na m  e',
      'nam$e' => ' nam$e',
      '' => ' ',
      ' ' => ' ',
      '  ' => ' ',
    ] as $name => $expected) {

      self::assertToString(
        $expected,
        new Attributes([$name => TRUE]),
        var_export($name, TRUE));
    }
  }

  /**
   * Tests behavior for broken attribute names.
   *
   * Note that this is an incorrect use of this library.
   */
  public function testBrokenAttributeNamesBooleanEnclosed() {

    foreach ([
      'name ' => ' a name z',
      'name  ' => ' a name z',
      ' name' => ' a name z',
      '  name' => ' a name z',
      'na m  e' => ' a na m  e z',
      'nam$e' => ' a nam$e z',
      '' => '  a z',
      ' ' => '  a z',
      '  ' => '  a z',
    ] as $name => $expected) {

      self::assertToString(
        $expected,
        new Attributes(['a' => TRUE, $name => TRUE, 'z' => TRUE]),
        var_export($name, TRUE));
    }
  }

  /**
   * Tests example attributes from a profiling case.
   */
  public function testExample() {

    // Line breaks make this string easier to read, and nicer in git diff.
    // If done like below, GrumPHP won't complain.
    $expected = str_replace("\n", '', '
 bool-false
 bool-false-array=""
 bool-true
 bool-true-array="1"
 float-array="0.31830988618379 0.63661977236758 3.1415926535898 1.5707963267949 0.78539816339745"
 float-nested-array="0.31830988618379 0.63661977236758 3.1415926535898 1.5707963267949 0.78539816339745"
 float="3.1415926535898"
 integer-array="0 1 2 3 4 5"
 integer-nested-array="0 1 2 3 4 5"
 integer="0"
 string-array-spaces="a b c d e"
 string-array="a b c d e f"
 string-nested-array="a b c d e f"
 string="a b c d e f"');

    $attributes = new Attributes(
      [
        'bool-true' => TRUE,
        'bool-false' => FALSE,
        'bool-true-array' => array_fill(0, 3, TRUE),
        'bool-false-array' => array_fill(0, 3, FALSE),
        'integer' => 0,
        'integer-array' => range(0, 5),
        'integer-nested-array' => [0, [1, [2, [3, [4, [5]]]]]],
        'float' => M_PI,
        'float-array' => [M_1_PI, M_2_PI, M_PI, M_PI_2, M_PI_4],
        'float-nested-array' => [M_1_PI, [M_2_PI, [M_PI, [M_PI_2, [M_PI_4]]]]],
        'string' => ' a b c d e f ',
        'string-array' => range('a', 'f'),
        'string-array-spaces' => ['a ', ' b ', ' c ', ' d ', ' e'],
        'string-nested-array' => ['a', ['b', ['c', ['d', ['e', ['f']]]]]],
      ]);

    self::assertToString(
      $expected,
      $attributes);
  }

  /**
   * Asserts that Attributes->__toString() has the expected value.
   *
   * @param string $expected
   *   Expected string value.
   * @param \Drupal\atomium\Attributes $attributes
   *   The attributes object.
   * @param string $message
   *   Message to send to self::assertSame().
   */
  private static function assertToString($expected, Attributes $attributes, $message = '') {
    self::assertSame(
      var_export($expected, TRUE),
      var_export($attributes->__toString(), TRUE),
      $message);
  }

}
