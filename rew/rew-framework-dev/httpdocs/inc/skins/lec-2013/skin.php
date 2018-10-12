<?php

// Page Resource
$page = $this->getPage();

// Agent Details
if ($page->info('name') == 'agents' && isset($_GET['aname'])) {
    // Remove Existing Module
    $snippet = $page->container('snippet');
    $exists = $snippet->contains('agents');
    if (!empty($exists)) {
        $snippet->removeModule($exists);
    }
    unset($snippet, $exists);

    // Agent Details
    $page->container('content')->addModule('agents', array(
        'mode' => 'details',
        'agent' => $_GET['aname']
    ));

// Office Details
} else if ($page->info('name') == 'offices' && isset($_GET['oid'])) {
    // Remove Existing Module
    $snippet = $page->container('snippet');
    $exists = $snippet->contains('offices');
    if (!empty($exists)) {
        $snippet->removeModule($exists);
    }
    unset($snippet, $exists);

    // Office Details
    $page->container('content')->addModule('offices', array(
        'mode' => 'details',
        'office' => $_GET['oid']
    ));

// CMS Content
} else {
    $page->container('content')->addModule('rew_content');
}

// Not Popup Window
if (!isset($_GET['popup'])) {
    // Logo Markup Header
    $path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $this->getPath());
    $pageLogo = $page->info('template') === 'cover' ? 'logo_homepage' : 'logo_content_page';
    $logoPath = $page->getSkin()->getLogoPath($pageLogo);
    if (!empty($logoPath)) {
        $logoRetinaPath = $page->getSkin()->getLogoPath('retina_' . $pageLogo);
        $logoRetinaPath = !empty($logoRetinaPath) ? ' srcset="' . $logoRetinaPath . ' 2x"' : '';
    } else {
        $logoPath = $path . '/img/site-logo.png';
        $logoRetinaPath = '';
    }
    $logoMarkup = '<img src="' . $logoPath . '"' . $logoRetinaPath . ' alt="' . rew_snippet('var-site-name', false, 1) . '">';
    $page->info('logoMarkupHeader', $logoMarkup);

    // Social Media Snippet (#lec-sidebar# contains HTML)
    $page->container('snippet')->addModule('social-share');

    // Broker Site
    if (Settings::getInstance()->SETTINGS['agent'] === 1) {
        // Footer: Testimonials Module
        if (!empty(Settings::getInstance()->MODULES['REW_TESTIMONIALS'])) {
            $page->container('testimonial')->addModule('testimonials', array(
                'title' => 'Our Clients are Saying&hellip;',
                'class' => 'testimonial',
                'limit' => 1,
                'html'  => false,
                'truncate' => 175
            ));
        }

    // Agent Site
    } else {
        // Agent Header
        $feature_image = $page->info('feature_image');
        if (!empty($feature_image) && file_exists(DIR_FEATURED_IMAGES . $feature_image)) {
            $page->container('content')->addModule('cms-snippet', array(
                'code' => '<div id="agent-feature"><img src="' . URL_FEATURED_IMAGES . $feature_image . '" alt=""></div>',
                'prepend' => true
            ));
        }
    }

    // CMS Page
    if ($page->info('app') == 'cms') {
        if (Settings::getInstance()->SETTINGS['agent'] !== 1) {
            $page->setTemplate('1col');
        }
        $page->info('cms', true);

    // Blog Page
    } else if ($page->info('app') == 'blog') {
        $page->info('blog', true);

    // Directory Page
    } else if ($page->info('app') == 'directory') {
        $page->info('directory', true);

    // IDX Map Search
    } else if ($page->info('app') == 'idx-map') {
        $page->info('idx-map', true);
    }

    // Page Vignette
    if ($page->variable('showVignette') === true) {
        $page->info('class', $page->info('class') . ' featVignette');
    }
}
