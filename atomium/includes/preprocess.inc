<?php

/**
 * @file
 * preprocess.inc
 */

use Drupal\atomium\AttributesContainer;

/**
 * Implements hook_preprocess().
 *
 * @param array $variables
 * @param string $hook
 */
function atomium_preprocess(array &$variables, $hook) {
  // Make sure the ['atomium'] and ['atomium']['attributes'] keys are created.
  if (!isset($variables['atomium']['attributes'])) {
    $variables['atomium']['attributes'] = new AttributesContainer();
  }
  elseif (!($variables['atomium']['attributes'] instanceof AttributesContainer)) {
    // @todo Does this even work?
    $variables['atomium']['attributes'] = new AttributesContainer($variables['atomium']['attributes']);
  }

  if (FALSE !== $pos = strpos($hook, '__')) {
    $variables['base_hook'] = substr($hook, 0, $pos);
  }
  else {
    $variables['base_hook'] = $hook;
  }
}
