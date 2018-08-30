<?php

/**
 * Return template file and variables array for testing purposes.
 *
 * @param string $template_file
 *   Template file.
 * @param mixed $variables
 *   Variables array.
 *
 * @return array
 *   Template file and variables array for testing purposes.
 */
function atomium_test_render_template($template_file, $variables) {
  return [
    'template' => $template_file,
    'variables' => $variables,
  ];
}

drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
