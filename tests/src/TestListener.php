<?php

namespace Drupal\Tests\atomium;

use PHPUnit\Framework\BaseTestListener;
use PHPUnit_Framework_TestSuite;

class TestListener extends BaseTestListener {

  public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
  {
    $basedir = dirname(__DIR__);

    if (false !== strpos($suite->getName(), '::')) {
      return;
    }

    print "SUITE NAME: " . $suite->getName() . "\n";

    if (0 === strpos($suite->getName(), 'Drupal\\')) {
      # print "\n";
    }

    if (0 === strpos($suite->getName(), 'Drupal\Tests\atomium\Kernel')) {
      require_once $basedir . '/bootstrap-integration.php';
    }

    if (0 === strpos($suite->getName(), 'Drupal\Tests\atomium\Unit')) {
      require_once $basedir . '/bootstrap-unit.php';
    }
  }

  public function endTestSuite(PHPUnit_Framework_TestSuite $suite) {
    if (0 === strpos($suite->getName(), 'Drupal\\')) {
      print "\n";
    }
  }

}
