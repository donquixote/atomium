<?php

namespace Drupal\Tests\atomium\Unit;

use Drupal\atomium\Attributes;

/**
 * Tests known issues in the Attributes class.
 */
class AttributesKnownIssuesTest extends UnitTestBase {

  /**
   * Tests existing behavior with Attributes->offsetGet().
   */
  public function testOffsetGetByReferenceIsPointless() {
    $attributes = new Attributes(['class' => ['sidebar']]);
    $attributes['class'][] = 'other-class';
    $attributes['id'][] = 'example-id';
    // The additional values are not added, and no empty attribute is created.
    self::assertSame(
      ' class="sidebar"',
      $attributes->__toString());
  }

}
