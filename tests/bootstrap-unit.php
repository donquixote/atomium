<?php

foreach (glob(dirname(__DIR__) . '/atomium/includes/classes/*.php') as $file_to_include) {
  include_once $file_to_include;
}
