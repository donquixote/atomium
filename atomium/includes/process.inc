<?php

/**
 * @file
 * process.inc
 */

/**
 * Implements hook_process().
 *
 * @param array $variables
 * @param string $hook
 */
function atomium_process(array &$variables, $hook) {

  if (!isset($variables['theme_hook_original'])) {
    return;
  }

  // Look for renderable elements in $variables.
  // @todo Look into theme registry to determine which keys to check?
  foreach ($variables as $key => $value) {

    if (!is_array($value)) {
      continue;
    }

    if (isset($value['#theme'])) {
      _atomium_extend_theme_property(
        $variables[$key],
        array($variables['theme_hook_original'])
      );
    }

    if (isset($value['#theme_wrappers'])) {
      _atomium_extend_theme_wrappers_property(
        $variables[$key],
        array($variables['theme_hook_original'])
      );
    }
  }
}
