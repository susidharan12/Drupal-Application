<?php

namespace Drupal\iihr_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\Query\QueryException;
use Symfony\Component\HttpFoundation\Request;

/**
 * IIHR Controller — fetches all dynamic content from Drupal CMS.
 */
class IihrController extends ControllerBase {

  public function home(Request $request) {
    return [
      '#theme'           => 'iihr_home',
      '#site_config'     => $this->getSiteConfig(),
      '#hero_slides'     => $this->getHeroSlides(),
      '#ticker_items'    => $this->getTickerItems(),
      '#quick_links'     => $this->getQuickLinks(),
      '#news_items'      => $this->getNewsItems(),
      '#events'          => $this->getEvents(),
      '#director'        => $this->getDirector(),
      '#spotlight_items' => $this->getSpotlightItems(),
      '#research_items'  => $this->getResearchItems(),
      '#partner_logos'   => $this->getPartnerLogos(),
      '#cache'           => ['max-age' => 0],
    ];
  }

  public function about(Request $request) {
    return [
      '#theme'        => 'iihr_about',
      '#ticker_items' => $this->getTickerItems(),
      '#site_config'  => $this->getSiteConfig(),
      '#cache'        => ['max-age' => 0],
    ];
  }

  /* ─────────────────────────────────────────────────────────────────────────
     SITE CONFIG — header titles, address, phone, email, spotlight text
     ───────────────────────────────────────────────────────────────────────── */

  private function getSiteConfig(): array {
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $nids = $storage->getQuery()
      ->condition('type', 'iihr_site_config')
      ->condition('status', 1)
      ->range(0, 1)
      ->accessCheck(FALSE)
      ->execute();

    if (!empty($nids)) {
      $node = reset($storage->loadMultiple($nids));
      return [
        'kannada_title'   => $this->fieldVal($node, 'field_sc_kannada_title',
          'ಭಾ.ಕೃ.ಅ.ಪ - ಭಾರತೀಯ ತೋಟಗಾರಿಕೆ ಸಂಶೋಧನಾ ಸಂಸ್ಥೆ | आई.सी.ए.आर - भारतीय बागवानी अनुसंधान संस्थान'),
        'hindi_title'     => $this->fieldVal($node, 'field_sc_hindi_title', ''),
        'english_title'   => $node->getTitle() !== 'Site Configuration'
          ? $node->getTitle() : 'ICAR - Indian Institute of Horticulture Research',
        'address'         => $this->fieldVal($node, 'field_sc_address',
          'Hesaraghatta Lake Post, Bengaluru-560 089'),
        'phone'           => $this->fieldVal($node, 'field_sc_phone', '(080) 23086100'),
        'email'           => $this->fieldVal($node, 'field_sc_email', 'iihrdirector[at]gmail[dot]com'),
        'ministry_text'   => $this->fieldVal($node, 'field_sc_ministry_text',
          'Ministry of Agriculture and Farmers Welfare | Government of India'),
        'spotlight_title' => $this->fieldVal($node, 'field_sc_spotlight_title', 'IIHR Spotlight'),
        'spotlight_text'  => $this->fieldVal($node, 'field_sc_spotlight_text',
          'Our roots represent focused areas of expertise working towards a common vision.'),
        'copyright'       => $this->fieldVal($node, 'field_sc_copyright',
          'Copyright © 2026 ICAR-IIHR, Maintained by Agricultural Knowledge Management Unit'),
      ];
    }

    // Defaults (shown before admin creates the Site Configuration node)
    return [
      'kannada_title'   => 'ಭಾ.ಕೃ.ಅ.ಪ - ಭಾರತೀಯ ತೋಟಗಾರಿಕೆ ಸಂಶೋಧನಾ ಸಂಸ್ಥೆ | आई.सी.ए.आर - भारतीय बागवानी अनुसंधान संस्थान',
      'hindi_title'     => '',
      'english_title'   => 'ICAR - Indian Institute of Horticulture Research',
      'address'         => 'Hesaraghatta Lake Post, Bengaluru-560 089',
      'phone'           => '(080) 23086100',
      'email'           => 'iihrdirector[at]gmail[dot]com',
      'ministry_text'   => 'Ministry of Agriculture and Farmers Welfare | Government of India',
      'spotlight_title' => 'IIHR Spotlight',
      'spotlight_text'  => 'Our roots represent focused areas of expertise working towards a common vision.',
      'copyright'       => 'Copyright © 2026 ICAR-IIHR, Maintained by Agricultural Knowledge Management Unit',
    ];
  }

  /* ─────────────────────────────────────────────────────────────────────────
     QUICK LINKS — sidebar links
     ───────────────────────────────────────────────────────────────────────── */

  private function getQuickLinks(): array {
    $fallback = [
      ['title' => 'Divisions',    'icon_url' => 'https://img.icons8.com/ios-filled/50/ffffff/city-buildings.png',   'link_url' => '#'],
      ['title' => 'Farmers Zone', 'icon_url' => 'https://img.icons8.com/ios-filled/50/ffffff/wheat.png',            'link_url' => '#'],
      ['title' => 'Varieties',    'icon_url' => 'https://img.icons8.com/ios-filled/50/ffffff/plant-under-rain.png', 'link_url' => '#'],
      ['title' => 'Technologies', 'icon_url' => 'https://img.icons8.com/ios-filled/50/ffffff/idea.png',            'link_url' => '#'],
      ['title' => 'PG Education', 'icon_url' => 'https://img.icons8.com/ios-filled/50/ffffff/graduation-cap.png',  'link_url' => '#'],
    ];
    try {
      $storage = \Drupal::entityTypeManager()->getStorage('node');
      $nids = $storage->getQuery()
        ->condition('type', 'iihr_quick_link')
        ->condition('status', 1)
        ->sort('field_ql_weight', 'ASC')
        ->sort('created', 'ASC')
        ->range(0, 8)
        ->accessCheck(FALSE)
        ->execute();

      $items = [];
      foreach ($storage->loadMultiple($nids) as $node) {
        $items[] = [
          'title'    => $node->getTitle(),
          'icon_url' => $this->fieldVal($node, 'field_ql_icon_url',
            'https://img.icons8.com/ios-filled/50/ffffff/star.png'),
          'link_url' => $this->fieldVal($node, 'field_ql_link_url', '#'),
        ];
      }
      return empty($items) ? $fallback : $items;
    }
    catch (\Exception $e) {
      return $fallback;
    }
  }

  /* ─────────────────────────────────────────────────────────────────────────
     HERO SLIDES
     ───────────────────────────────────────────────────────────────────────── */

  private function getHeroSlides(): array {
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $nids = $storage->getQuery()
      ->condition('type', 'iihr_slide')
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->range(0, 5)
      ->accessCheck(FALSE)
      ->execute();

    $slides = [];
    foreach ($storage->loadMultiple($nids) as $node) {
      $slides[] = [
        'title'     => $node->getTitle(),
        'tag'       => $this->fieldVal($node, 'field_slide_tag', ''),
        'subtitle'  => $this->fieldVal($node, 'field_slide_subtitle', ''),
        'hash'      => $this->fieldVal($node, 'field_slide_hash', ''),
        'image_url' => $this->imageUrl($node, 'field_slide_image',
          '/themes/custom/iihr_theme/images/slide1 (1).jpeg'),
      ];
    }

    if (empty($slides)) {
      $slides[] = [
        'title'     => 'IIHR Celebrates 59th Foundation Day',
        'tag'       => 'Special Day in IIHR',
        'subtitle'  => 'Marking Decades of Excellence in Horticultural Research and Innovation.',
        'hash'      => '#59forIIHR',
        'image_url' => '/themes/custom/iihr_theme/images/slide1 (1).jpeg',
      ];
    }
    return $slides;
  }

  /* ─────────────────────────────────────────────────────────────────────────
     TICKER ITEMS
     ───────────────────────────────────────────────────────────────────────── */

  private function getTickerItems(): array {
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $nids = $storage->getQuery()
      ->condition('type', 'iihr_ticker')
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->range(0, 10)
      ->accessCheck(FALSE)
      ->execute();

    $items = [];
    foreach ($storage->loadMultiple($nids) as $node) {
      $items[] = $node->getTitle();
    }
    return $items;
  }

  /* ─────────────────────────────────────────────────────────────────────────
     NEWS & ANNOUNCEMENTS
     ───────────────────────────────────────────────────────────────────────── */

  private function getNewsItems(): array {
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $nids = $storage->getQuery()
      ->condition('type', 'iihr_news')
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->range(0, 6)
      ->accessCheck(FALSE)
      ->execute();

    $items = [];
    foreach ($storage->loadMultiple($nids) as $node) {
      $date_val   = $node->hasField('field_news_date') ? $node->get('field_news_date')->value : NULL;
      $timestamp  = $date_val ? strtotime($date_val) : $node->getCreatedTime();
      $items[] = [
        'title' => $node->getTitle(),
        'url'   => $node->toUrl()->toString(),
        'date'  => date('M j, Y', $timestamp),
      ];
    }
    return $items;
  }

  /* ─────────────────────────────────────────────────────────────────────────
     UPCOMING EVENTS
     ───────────────────────────────────────────────────────────────────────── */

  private function getEvents(): array {
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $nids = $storage->getQuery()
      ->condition('type', 'iihr_event')
      ->condition('status', 1)
      ->sort('field_event_date', 'ASC')
      ->range(0, 3)
      ->accessCheck(FALSE)
      ->execute();

    $items = [];
    foreach ($storage->loadMultiple($nids) as $node) {
      $date_val  = $node->hasField('field_event_date') ? $node->get('field_event_date')->value : NULL;
      $timestamp = $date_val ? strtotime($date_val) : time();
      $category  = $node->hasField('field_event_category') ? ($node->get('field_event_category')->value ?? '') : '';

      $items[] = [
        'title'          => $node->getTitle(),
        'url'            => $node->toUrl()->toString(),
        'month'          => strtoupper(date('M', $timestamp)),
        'day'            => date('d', $timestamp),
        'category'       => $category,
        'category_label' => $category ? ucfirst($category) : '',
      ];
    }
    return $items;
  }

  /* ─────────────────────────────────────────────────────────────────────────
     DIRECTOR
     ───────────────────────────────────────────────────────────────────────── */

  private function getDirector(): array {
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $nids = $storage->getQuery()
      ->condition('type', 'iihr_director')
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->range(0, 1)
      ->accessCheck(FALSE)
      ->execute();

    if (!empty($nids)) {
      $node = reset($storage->loadMultiple($nids));
      return [
        'name'      => $node->getTitle(),
        'image_url' => $this->imageUrl($node, 'field_director_photo',
          '/themes/custom/iihr_theme/images/dr.jpg'),
        'url'       => $node->toUrl()->toString(),
      ];
    }
    return [
      'name'      => 'Prof. Tushar Kanti Behera',
      'image_url' => '/themes/custom/iihr_theme/images/dr.jpg',
      'url'       => '#',
    ];
  }

  /* ─────────────────────────────────────────────────────────────────────────
     SPOTLIGHT GRID ITEMS
     ───────────────────────────────────────────────────────────────────────── */

  private function getSpotlightItems(): array {
    $fallback = [
      ['title' => 'Fruit Crops',               'image_url' => 'https://images.unsplash.com/photo-1619566636858-adf3ef46400b?auto=format&fit=crop&q=80&w=600', 'description' => 'We work on researching and developing new crop varieties to improve yield, quality & resilience.', 'link_url' => '#', 'featured' => TRUE],
      ['title' => 'Vegetable Crops',           'image_url' => 'https://images.unsplash.com/photo-1566385101042-1a0aa0c1268c?auto=format&fit=crop&q=80&w=600', 'description' => '', 'link_url' => '#', 'featured' => FALSE],
      ['title' => 'Flowers & Medicinal Crops', 'image_url' => 'https://images.unsplash.com/photo-1486944859394-ed1c44255713?w=500&auto=format&fit=crop&q=60',  'description' => '', 'link_url' => '#', 'featured' => FALSE],
      ['title' => 'Crop Protection',           'image_url' => 'https://images.unsplash.com/photo-1592982537447-7440770cbfc9?auto=format&fit=crop&q=80&w=600', 'description' => '', 'link_url' => '#', 'featured' => FALSE],
      ['title' => 'Natural Resources',         'image_url' => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&q=80&w=600', 'description' => '', 'link_url' => '#', 'featured' => FALSE],
      ['title' => 'Research',                  'image_url' => 'https://images.unsplash.com/photo-1518152006812-edab29b069ac?w=600&auto=format&fit=crop',       'description' => '', 'link_url' => '#', 'featured' => FALSE],
      ['title' => 'Farmers',                   'image_url' => 'https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?auto=format&fit=crop&q=80&w=600', 'description' => '', 'link_url' => '#', 'featured' => FALSE],
    ];
    try {
      $storage = \Drupal::entityTypeManager()->getStorage('node');
      $nids = $storage->getQuery()
        ->condition('type', 'iihr_spotlight')
        ->condition('status', 1)
        ->sort('field_sp_weight', 'ASC')
        ->sort('created', 'ASC')
        ->range(0, 7)
        ->accessCheck(FALSE)
        ->execute();

      $items = [];
      foreach ($storage->loadMultiple($nids) as $node) {
        $items[] = [
          'title'       => $node->getTitle(),
          'image_url'   => $this->imageUrl($node, 'field_sp_image', ''),
          'description' => $this->fieldVal($node, 'field_sp_description', ''),
          'link_url'    => $this->fieldVal($node, 'field_sp_link_url', '#'),
          'featured'    => (bool) ($node->hasField('field_sp_featured') ? $node->get('field_sp_featured')->value : FALSE),
        ];
      }
      return empty($items) ? $fallback : $items;
    }
    catch (\Exception $e) {
      return $fallback;
    }
  }

  /* ─────────────────────────────────────────────────────────────────────────
     RESEARCH PROGRAMMES
     ───────────────────────────────────────────────────────────────────────── */

  private function getResearchItems(): array {
    $fallback = [
      ['title' => 'Development and refinement of production technology of fruit crops', 'image_url' => 'https://images.unsplash.com/photo-1568702846914-96b305d2aaeb?w=400'],
      ['title' => 'Improvement of annona for yield and quality',                        'image_url' => 'https://images.unsplash.com/photo-1609780447631-05b93e5a88ea?w=400'],
      ['title' => 'Others',                                                             'image_url' => 'https://images.unsplash.com/photo-1464226184884-fa280b87c399?w=400'],
    ];
    try {
      $storage = \Drupal::entityTypeManager()->getStorage('node');
      $nids = $storage->getQuery()
        ->condition('type', 'iihr_research_prog')
        ->condition('status', 1)
        ->sort('field_rp_weight', 'ASC')
        ->sort('created', 'ASC')
        ->range(0, 6)
        ->accessCheck(FALSE)
        ->execute();

      $items = [];
      foreach ($storage->loadMultiple($nids) as $node) {
        $items[] = [
          'title'     => $node->getTitle(),
          'image_url' => $this->imageUrl($node, 'field_rp_image', ''),
        ];
      }
      return empty($items) ? $fallback : $items;
    }
    catch (\Exception $e) {
      return $fallback;
    }
  }

  /* ─────────────────────────────────────────────────────────────────────────
     PARTNER LOGOS
     ───────────────────────────────────────────────────────────────────────── */

  private function getPartnerLogos(): array {
    $fallback = [];
    for ($i = 1; $i <= 5; $i++) {
      $fallback[] = ['name' => "Partner {$i}", 'image_url' => "/themes/custom/iihr_theme/images/logo{$i}.png"];
    }
    try {
      $storage = \Drupal::entityTypeManager()->getStorage('node');
      $nids = $storage->getQuery()
        ->condition('type', 'iihr_partner')
        ->condition('status', 1)
        ->sort('field_partner_weight', 'ASC')
        ->sort('created', 'ASC')
        ->range(0, 10)
        ->accessCheck(FALSE)
        ->execute();

      $logos = [];
      foreach ($storage->loadMultiple($nids) as $node) {
        $logos[] = [
          'name'      => $node->getTitle(),
          'image_url' => $this->imageUrl($node, 'field_partner_logo', ''),
        ];
      }
      return empty($logos) ? $fallback : $logos;
    }
    catch (\Exception $e) {
      return $fallback;
    }
  }

  /* ─────────────────────────────────────────────────────────────────────────
     PRIVATE UTILITIES
     ───────────────────────────────────────────────────────────────────────── */

  /** Get a text field value with a fallback default. */
  private function fieldVal($node, string $field_name, string $default = ''): string {
    if ($node->hasField($field_name) && !$node->get($field_name)->isEmpty()) {
      return (string) $node->get($field_name)->value;
    }
    return $default;
  }

  /** Get an image field absolute URL with a fallback. */
  private function imageUrl($node, string $field_name, string $default = ''): string {
    if ($node->hasField($field_name) && !$node->get($field_name)->isEmpty()) {
      $file = $node->get($field_name)->entity;
      if ($file) {
        return \Drupal::service('file_url_generator')
          ->generateAbsoluteString($file->getFileUri());
      }
    }
    return $default;
  }

}
