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
   * Tests unusual non-string attribute values.
   */
  public function testUnusualAttributeValues() {

    foreach ([
      [TRUE, TRUE],
      [FALSE, TRUE],
      [[TRUE], '1'],
      [[TRUE, TRUE], '1'],
      [[FALSE, FALSE], ''],
      [0, '0'],
      [3, '3'],
      [[1, 2, 3], '1 2 3'],
      [[1, [2, [3]]], '1 2 3'],
      [M_PI, (string) M_PI],
      [[M_PI], (string) M_PI],
      [[M_PI, [M_PI + 1]], M_PI . ' ' . (M_PI + 1)],
      [['a', 'b', 'c'], 'a b c'],
      [['a ', ' b ', ' c ', ' d ', ' e'], 'a b c d e'],
      [['a', ['b', ['c']]], 'a b c'],
    ] as $value_and_expected) {

      list($value, $expected_value) = $value_and_expected;

      if (TRUE === $expected_value) {
        $expected = ' name';
      }
      elseif (NULL === $expected_value) {
        $expected = '';
      }
      else {
        $expected = ' name="' . $expected_value . '"';
      }

      self::assertToString(
        $expected,
        new Attributes(['name' => $value]),
        var_export($value, TRUE));
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
        'bool-true-array' => [TRUE, TRUE, TRUE],
        'bool-false-array' => [FALSE, FALSE, FALSE],
        'integer' => 0,
        'integer-array' => [0, 1, 2, 3, 4, 5],
        'integer-nested-array' => [0, [1, [2, [3, [4, [5]]]]]],
        'float' => M_PI,
        'float-array' => [1.1, 1.2, 1.3, 1.4, 1.5],
        'float-nested-array' => [1.1, [1.2, [1.3, [1.4, [1.5]]]]],
        'string' => ' a b c d e f ',
        'string-array' => range('a', 'f'),
        'string-array-spaces' => ['a ', ' b ', ' c ', ' d ', ' e'],
        'string-nested-array' => ['a', ['b', ['c', ['d', ['e', ['f']]]]]],
      ]);

    self::assertToStringAdvanced(
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

  /**
   * Asserts that Attributes->__toString() has the expected value.
   *
   * Strings are broken to multiple lines for readability.
   *
   * @param string $expected
   *   Expected string value.
   * @param \Drupal\atomium\Attributes $attributes
   *   The attributes object.
   * @param string $message
   *   Message to send to self::assertSame().
   */
  private static function assertToStringAdvanced($expected, Attributes $attributes, $message = '') {
    self::assertSame(
      self::formatAtributesString($expected),
      self::formatAtributesString($attributes->__toString()),
      $message);
  }

  /**
   * Formats an attributes string as multi-line PHP expression.
   *
   * @param string $string
   *   The original attributes string.
   *
   * @return string
   *   The PHP expression.
   */
  private static function formatAtributesString($string) {
    $parts = explode(' ', $string);
    for ($i = count($parts) - 2; $i > 0; --$i) {
      # $parts[$i] .= '"';
    }
    $out = "''";
    foreach ($parts as $part) {
      $out .= "\n . " . var_export($part, TRUE);
    }
    return $out;
  }

}
