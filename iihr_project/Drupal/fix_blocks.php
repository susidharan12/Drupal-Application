<?php
$block = \Drupal\block\Entity\Block::load('iihr_theme_newsannoncement');
if ($block) {
  $block->delete();
  echo "DELETED: iihr_theme_newsannoncement\n";
} else {
  echo "Not found by primary ID, searching all...\n";
}
$blocks = \Drupal::entityTypeManager()->getStorage('block')
  ->loadByProperties(['theme' => 'iihr_theme']);
foreach ($blocks as $b) {
  $region = $b->getRegion();
  echo $b->id() . ' | region=' . $region . ' | plugin=' . $b->getPluginId() . "\n";
  if ($region === 'content' && $b->getPluginId() !== 'system_main_block') {
    $b->delete();
    echo "  -> DELETED (non-main block in content region)\n";
  }
}