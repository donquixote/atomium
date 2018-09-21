<?php

use Drupal\Tests\atomium\Unit\AttributesValueGenerator;
use Symfony\Component\Yaml\Yaml;

require_once __DIR__ . '/bootstrap.php';

$generator = new AttributesValueGenerator();

$values = $generator->generate();

$yml = Yaml::dump($values, 9);

file_put_contents(__DIR__ . '/fixtures/attributes/values.yml', $yml);
