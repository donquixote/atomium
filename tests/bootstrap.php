<?php

/**
 * @file
 * PHPUnit bootstrap file.
 */

require_once './vendor/autoload.php';

define('DRUPAL_ROOT', dirname(__DIR__) . '/build');

// Directory change necessary since Drupal often uses relative paths.
chdir(DRUPAL_ROOT);
require_once 'includes/bootstrap.inc';
