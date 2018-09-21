<?php

namespace Drupal\Tests\atomium\Unit;

use Symfony\Component\Yaml\Yaml;

class AttributesGeneratorTest extends UnitTestBase {

  public function testGeneratedValues() {
    $file = dirname(dirname(__DIR__)) . '/fixtures/attributes/values.yml';
    $expected_yml = file_get_contents($file);
    $expected_array = Yaml::parse($expected_yml);
    $generator = new AttributesValueGenerator();
    $actual_array = $generator->generate();

    self::assertSameAssoc($expected_array, $actual_array, '$value');
  }

  /**
   * @param array $expected
   * @param array $actual
   * @param string $trail
   */
  private static function assertSameAssoc(array $expected, array $actual, $trail) {

    self::assertSame(
      array_keys($expected),
      array_keys($actual),
      'array_keys(' . $trail . ')');

    foreach ($expected as $k => $v_expected) {
      $subtrail = $trail . '[' . var_export($k, TRUE) . ']';
      $v_actual = $actual[$k];

      if (':' === $k) {
        self::assertSame($v_expected, $v_actual, $subtrail);
        continue;
      }

      self::assertSame(
        gettype($v_expected),
        gettype($v_actual),
        'gettype(' . $subtrail . ')');

      switch (gettype($v_expected)) {

        case 'array':
          self::assertSameAssoc($v_expected, $v_actual, $subtrail);
          break;

        case 'object':
          self::fail('Object found at ' . $subtrail);
          break;

        default:
          self::assertSame($v_expected, $v_actual, $subtrail);
          break;
      }
    }
  }

}
