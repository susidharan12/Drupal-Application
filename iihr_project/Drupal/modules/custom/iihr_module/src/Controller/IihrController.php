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
      '#theme'          => 'iihr_about',
      '#ticker_items'   => $this->getTickerItems(),
      '#site_config'    => $this->getSiteConfig(),
      '#about_content'  => $this->getAboutContent(),
      '#mandate_items'  => $this->getMandateItems(),
      '#partner_logos'  => $this->getPartnerLogos(),
      '#cache'          => ['max-age' => 0],
    ];
  }

  public function divisionFruitCrops(Request $request) {
    return [
      '#theme'            => 'iihr_division',
      '#ticker_items'     => $this->getTickerItems(),
      '#site_config'      => $this->getSiteConfig(),
      '#division_content' => $this->getDivisionContent('fruit_crops'),
      '#division_mandates'=> $this->getDivisionMandates('fruit_crops'),
      '#division_genbank' => $this->getDivisionGeneBank('fruit_crops'),
      '#division_head'    => $this->getDivisionHead('fruit_crops'),
      '#partner_logos'    => $this->getPartnerLogos(),
      '#cache'            => ['max-age' => 0],
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
      'kannada_title'   => 'ಭಾ.ಕೃ.ಅ.ಪ - ಭಾರತೀಯ ತೋಟಗಾರಿಕೆ ಸಂಶೋಧನಾ ಸಂಸ್ಥೆ',
      'hindi_title'     => 'आई.सी.ए.आर - भारतीय बागवानी अनुसंधान संस्थान',
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
     ABOUT PAGE CONTENT — intro, history, core, award note
     ───────────────────────────────────────────────────────────────────────── */

  private function getAboutContent(): array {
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $nids = $storage->getQuery()
      ->condition('type', 'iihr_about_content')
      ->condition('status', 1)
      ->range(0, 1)
      ->accessCheck(FALSE)
      ->execute();

    if (!empty($nids)) {
      $node = reset($storage->loadMultiple($nids));
      return [
        'intro_heading'   => $this->fieldVal($node, 'field_ac_intro_heading', 'Introduction to IIHR'),
        'intro_text'      => $this->fieldVal($node, 'field_ac_intro_text',
          '<p>The Institute spread its sphere of Research activities to the length and breadth of the Nation by establishing its experimental stations at Lucknow, Nagpur, Ranchi, Godhra, Chettalli and Gonikopal. Over the years these experiment stations have grown in size and today they stand as independent institutes, however, retaining the Chettalli and Gonicoppal under its fold.</p><p>As of now, the IIHR has its main research station at Hessaraghatta, Bengaluru with 263 ha of land and Regional experiment station at Bhuvaneshwar in Orissa with two Krishi Vigyan Kendras both located in Karnataka state at Gonikopal in Kodagu and Hirehalli in Tumkur districts.</p>'),
        'intro_image'     => $this->imageUrl($node, 'field_ac_intro_image',
          '/themes/custom/iihr_theme/images/iihr.jpg'),
        'history_text'    => $this->fieldVal($node, 'field_ac_history_text',
          '<p>The physical growth of the Institute could be viewed in two phases. The first phase is from 1970 to 1990, wherein emphases were laid on land development and buildings. During this phase, the area for carrying out research and the area for laboratory buildings, supporting buildings and other essential office buildings was earmarked. Accordingly, the entire arable land was divided into well-defined nine blocks for carrying out research and independent buildings for various divisions and departments with laboratories were built.</p><p>The second phase of the physical growth was after 1990 during which emphases was laid on creating ultra-modern world class infrastructure facilities in terms of equipment and structures. Currently the institute has well defined 8 divisions namely, The Division of Fruit Crops, Division of Vegetable Crops, Division of Flower and Medicinal Crops, Division of Post Harvest Technology and Agricultural Engineering, Division of Crop Protection, Division of Natural Resources, Division of Basic Sciences and Division of Social Sciences and Training with more than 65 purpose oriented laboratories having state of art equipment like electron microscope, ultra-centrifuge, LC-MS, GC-MS, GC-EAD, AAS, ICP-OES, SEM, TEM, HPLC, GLC, FT-NIR, Gama Chamber etc.</p>'),
        'core_text'       => $this->fieldVal($node, 'field_ac_core_text',
          '<p>The Institute has also an Agriculture Technology Information Centre (ATIC), which is a single window agency for dissemination of information and technologies developed by the Institute. All the technological products and popular publications developed by the Institute are sold to the farmers and interested public through the agricultural technology information centre.</p><p>The main strength of the institute is excellent well-trained human resources. Presently the Institute has a total cadre strength of 714 staff with 150 Scientists, 226 technical staff, 83 Administrative staff and 145 supporting staff.</p>'),
        'mandate_heading' => $this->fieldVal($node, 'field_ac_mandate_heading', 'The Mandate'),
        'award_text'      => $this->fieldVal($node, 'field_ac_award_text',
          'Twice, i.e. during the year 1999 and 2011, the Indian Council of Agricultural Research, New Delhi awarded the Best Institute Award to IIHR, Bangalore in recognition of institute\'s progress, achievements, and its contribution to the field of horticulture.'),
      ];
    }

    // Defaults (shown before admin creates the About Page Content node)
    return [
      'intro_heading'   => 'Introduction to IIHR',
      'intro_text'      => '<p>The Institute spread its sphere of Research activities to the length and breadth of the Nation by establishing its experimental stations at Lucknow, Nagpur, Ranchi, Godhra, Chettalli and Gonikopal. Over the years these experiment stations have grown in size and today they stand as independent institutes, however, retaining the Chettalli and Gonicoppal under its fold.</p><p>As of now, the IIHR has its main research station at Hessaraghatta, Bengaluru with 263 ha of land and Regional experiment station at Bhuvaneshwar in Orissa with two Krishi Vigyan Kendras both located in Karnataka state at Gonikopal in Kodagu and Hirehalli in Tumkur districts.</p>',
      'intro_image'     => '/themes/custom/iihr_theme/images/iihr.jpg',
      'history_text'    => '<p>The physical growth of the Institute could be viewed in two phases. The first phase is from 1970 to 1990, wherein emphases were laid on land development and buildings. During this phase, the area for carrying out research and the area for laboratory buildings, supporting buildings and other essential office buildings was earmarked. Accordingly, the entire arable land was divided into well-defined nine blocks for carrying out research and independent buildings for various divisions and departments with laboratories were built.</p><p>The second phase of the physical growth was after 1990 during which emphases was laid on creating ultra-modern world class infrastructure facilities in terms of equipment and structures. Currently the institute has well defined 8 divisions namely, The Division of Fruit Crops, Division of Vegetable Crops, Division of Flower and Medicinal Crops, Division of Post Harvest Technology and Agricultural Engineering, Division of Crop Protection, Division of Natural Resources, Division of Basic Sciences and Division of Social Sciences and Training with more than 65 purpose oriented laboratories having state of art equipment like electron microscope, ultra-centrifuge, LC-MS, GC-MS, GC-EAD, AAS, ICP-OES, SEM, TEM, HPLC, GLC, FT-NIR, Gama Chamber etc.</p>',
      'core_text'       => '<p>The Institute has also an Agriculture Technology Information Centre (ATIC), which is a single window agency for dissemination of information and technologies developed by the Institute. All the technological products and popular publications developed by the Institute are sold to the farmers and interested public through the agricultural technology information centre.</p><p>The main strength of the institute is excellent well-trained human resources. Presently the Institute has a total cadre strength of 714 staff with 150 Scientists, 226 technical staff, 83 Administrative staff and 145 supporting staff.</p>',
      'mandate_heading' => 'The Mandate',
      'award_text'      => 'Twice, i.e. during the year 1999 and 2011, the Indian Council of Agricultural Research, New Delhi awarded the Best Institute Award to IIHR, Bangalore in recognition of institute\'s progress, achievements, and its contribution to the field of horticulture.',
    ];
  }

  /* ─────────────────────────────────────────────────────────────────────────
     MANDATE CARDS — individual cards in "The Mandate" grid
     ───────────────────────────────────────────────────────────────────────── */

  private function getMandateItems(): array {
    $fallback = [
      ['icon_url' => 'https://img.icons8.com/ios-filled/40/000000/microscope.png',    'text' => 'To Undertake Basic And Applied Research For Developing Strategies To Enhance Productivity And Utilization Of Tropical And Sub-Tropical Horticulture Crops Viz., Fruits, Vegetables, Ornamentals, Medicinal And Aromatic Plants And Mushrooms.'],
      ['icon_url' => 'https://img.icons8.com/ios-filled/40/000000/book.png',           'text' => 'To Serve As A Repository Of Scientific Information Relevant To Horticulture.'],
      ['icon_url' => 'https://img.icons8.com/ios-filled/40/000000/graduation-cap.png', 'text' => 'To Act As A Centre For Training For Up Gradation Of Scientific Manpower In Modern Technologies For Horticulture Production.'],
      ['icon_url' => 'https://img.icons8.com/ios-filled/40/000000/handshake.png',      'text' => 'To Collaborate With National And International Agencies In Achieving The Above Objectives.'],
    ];
    try {
      $storage = \Drupal::entityTypeManager()->getStorage('node');
      $nids = $storage->getQuery()
        ->condition('type', 'iihr_mandate')
        ->condition('status', 1)
        ->sort('field_mandate_weight', 'ASC')
        ->sort('created', 'ASC')
        ->range(0, 8)
        ->accessCheck(FALSE)
        ->execute();

      $items = [];
      foreach ($storage->loadMultiple($nids) as $node) {
        $items[] = [
          'icon_url' => $this->fieldVal($node, 'field_mandate_icon_url',
            'https://img.icons8.com/ios-filled/40/000000/star.png'),
          'text'     => $this->fieldVal($node, 'field_mandate_text', ''),
        ];
      }
      return empty($items) ? $fallback : $items;
    }
    catch (\Exception $e) {
      return $fallback;
    }
  }

  /* ─────────────────────────────────────────────────────────────────────────
     DIVISION PAGE — Fruit Crops (and future divisions)
     ───────────────────────────────────────────────────────────────────────── */

  /**
   * Division main content block.
   * Tries content type iihr_division (field_div_machine_name = $key),
   * falls back to hardcoded defaults matching the PDF.
   */
  private function getDivisionContent(string $key): array {
    $defaults = [
      'fruit_crops' => [
        'page_title'      => 'Fruit Crops',
        'about_text'      => '<p>The Division of fruit crops was started in 1968 to cater the research and development needs in tropical and subtropical fruits at national level. This division is mainly working on the genetic improvement, development and refinement of fruit crops. This division is also offering courses on breeding of fruit crops and national problems in fruit crops to the Post Graduate students of Fruit Science.</p>',
        'mandate_heading' => 'The Mandate',
        'thrust_areas'    => [
          'Breeding for yield, quality, and biotic and abiotic stresses.',
          'High density planting (HDP), Canopy management, Crop regulation and Stock-Scion interaction in tropical fruits.',
          'Fruit crop based farming systems',
          'Intercropping in pre-bearing fruit crop based orchard systems.',
        ],
        'genbank_heading'    => "Field Gene Bank' Collections at Present",
        'mother_bank_text'   => 'The Division maintains mother banks of all released varieties in an area about 20 acres for multiplication purpose.',
      ],
    ];

    try {
      $storage = \Drupal::entityTypeManager()->getStorage('node');
      $nids = $storage->getQuery()
        ->condition('type', 'iihr_division')
        ->condition('status', 1)
        ->condition('field_div_machine_name', $key)
        ->range(0, 1)
        ->accessCheck(FALSE)
        ->execute();

      if (!empty($nids)) {
        $node = reset($storage->loadMultiple($nids));
        $thrust_raw = $this->fieldVal($node, 'field_div_thrust_areas', '');
        $thrust_arr = array_filter(array_map('trim', explode("\n", $thrust_raw)));
        return [
          'page_title'      => $node->getTitle(),
          'about_text'      => $this->fieldVal($node, 'field_div_about_text', $defaults[$key]['about_text']),
          'mandate_heading' => $this->fieldVal($node, 'field_div_mandate_heading', 'The Mandate'),
          'thrust_areas'    => !empty($thrust_arr) ? $thrust_arr : $defaults[$key]['thrust_areas'],
          'genbank_heading' => $this->fieldVal($node, 'field_div_genbank_heading', $defaults[$key]['genbank_heading']),
          'mother_bank_text'=> $this->fieldVal($node, 'field_div_mother_bank', $defaults[$key]['mother_bank_text']),
        ];
      }
    }
    catch (\Exception $e) {
      // fall through to defaults
    }

    return $defaults[$key] ?? $defaults['fruit_crops'];
  }

  /**
   * Division mandate cards (3 icons + text).
   * Tries iihr_division_mandate nodes filtered by field_div_key,
   * falls back to PDF defaults.
   */
  private function getDivisionMandates(string $key): array {
    $defaults = [
      'fruit_crops' => [
        [
          'icon_url' => 'https://img.icons8.com/ios-filled/50/ffffff/microscope.png',
          'text'     => 'To carry out the basic, strategic and applied research for higher productivity, quality and utility of fruit crops in tropical agro-climatic zones of India.',
        ],
        [
          'icon_url' => 'https://img.icons8.com/ios-filled/50/ffffff/dna-helix.png',
          'text'     => 'To be the National Active Germplasm Sites (NAGS) for major fruit crops and their effective management.',
        ],
        [
          'icon_url' => 'https://img.icons8.com/ios-filled/50/ffffff/graduation-cap.png',
          'text'     => 'To conduct teaching and training programmes for development of human resources.',
        ],
      ],
    ];

    try {
      $storage = \Drupal::entityTypeManager()->getStorage('node');
      $nids = $storage->getQuery()
        ->condition('type', 'iihr_division_mandate')
        ->condition('status', 1)
        ->condition('field_div_key', $key)
        ->sort('field_mandate_weight', 'ASC')
        ->range(0, 6)
        ->accessCheck(FALSE)
        ->execute();

      $items = [];
      foreach ($storage->loadMultiple($nids) as $node) {
        $items[] = [
          'icon_url' => $this->fieldVal($node, 'field_mandate_icon_url',
            'https://img.icons8.com/ios-filled/50/ffffff/star.png'),
          'text'     => $this->fieldVal($node, 'field_mandate_text', ''),
        ];
      }
      return !empty($items) ? $items : ($defaults[$key] ?? $defaults['fruit_crops']);
    }
    catch (\Exception $e) {
      return $defaults[$key] ?? $defaults['fruit_crops'];
    }
  }

  /**
   * Field Gene Bank collection stats.
   * Tries iihr_division_genbank nodes, falls back to PDF numbers.
   */
  private function getDivisionGeneBank(string $key): array {
    $defaults = [
      'fruit_crops' => [
        ['key' => 'mango',       'count' => '767', 'label' => 'Mango'],
        ['key' => 'pomegranate', 'count' => '265', 'label' => 'Pomegranate'],
        ['key' => 'grapes',      'count' => '20',  'label' => 'Grapes'],
        ['key' => 'jackfruit',   'count' => '171', 'label' => 'Jackfruit'],
        ['key' => 'dragon',      'count' => '6',   'label' => 'Dragon Fruit'],
      ],
    ];

    try {
      $storage = \Drupal::entityTypeManager()->getStorage('node');
      $nids = $storage->getQuery()
        ->condition('type', 'iihr_genbank')
        ->condition('status', 1)
        ->condition('field_div_key', $key)
        ->sort('field_gb_weight', 'ASC')
        ->range(0, 10)
        ->accessCheck(FALSE)
        ->execute();

      $items = [];
      foreach ($storage->loadMultiple($nids) as $node) {
        $items[] = [
          'key'   => $this->fieldVal($node, 'field_gb_css_key', 'mango'),
          'count' => $this->fieldVal($node, 'field_gb_count', '0'),
          'label' => $node->getTitle(),
        ];
      }
      return !empty($items) ? $items : ($defaults[$key] ?? $defaults['fruit_crops']);
    }
    catch (\Exception $e) {
      return $defaults[$key] ?? $defaults['fruit_crops'];
    }
  }

  /**
   * Division head (scientist in charge).
   * Tries iihr_division_head node, falls back to PDF data.
   */
  private function getDivisionHead(string $key): array {
    $defaults = [
      'fruit_crops' => [
        'name'        => 'Dr. M. Sankaran',
        'designation' => 'Principal Scientist & Head',
        'email'       => 'fruits[dot]iihr[at]icar[dot]gov[dot]in',
        'phone'       => '080-23086100 (Extn:297)',
        'image_url'   => '/themes/custom/iihr_theme/images/dr.jpg',
      ],
    ];

    try {
      $storage = \Drupal::entityTypeManager()->getStorage('node');
      $nids = $storage->getQuery()
        ->condition('type', 'iihr_division_head')
        ->condition('status', 1)
        ->condition('field_div_key', $key)
        ->range(0, 1)
        ->accessCheck(FALSE)
        ->execute();

      if (!empty($nids)) {
        $node = reset($storage->loadMultiple($nids));
        return [
          'name'        => $node->getTitle(),
          'designation' => $this->fieldVal($node, 'field_dh_designation', 'Principal Scientist & Head'),
          'email'       => $this->fieldVal($node, 'field_dh_email', ''),
          'phone'       => $this->fieldVal($node, 'field_dh_phone', ''),
          'image_url'   => $this->imageUrl($node, 'field_dh_photo',
            '/themes/custom/iihr_theme/images/dr.jpg'),
        ];
      }
    }
    catch (\Exception $e) {
      // fall through
    }

    return $defaults[$key] ?? $defaults['fruit_crops'];
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
