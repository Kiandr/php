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
        'agent' => $_GET['aname'],
        'thumbnails' => '350x/r',
        'testimonials' => true
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
        'office' => $_GET['oid'],
        'agent_thumbnails' => '350x/r'
    ));

// CMS Content
} else {
    // Content
    $page->container('content')->addModule('rew_content');

    // Agent subdomain site
    if (Settings::getInstance()->SETTINGS['agent'] !== 1) {
        // Load homepage feature image
        if (Http_Uri::getUri() === '/') {
            $page->container('feature')->addModule('slideshow', array(
                'template' => 'subdomain.tpl.php'
            ));
        }
    }
}

// Not Popup Window
if (!isset($_GET['popup'])) {
    // Logo Markup Header
    $pageLogo = $page->info('template') === 'cover' ? 'logo_homepage' : 'logo_content_page';
    if (!empty($logoPath = $page->getSkin()->getLogoPath($pageLogo))) {
        $logoRetinaPath = $page->getSkin()->getLogoPath('retina_' . $pageLogo);
        $logoRetinaPath = !empty($logoRetinaPath) ? ' srcset="' . $logoRetinaPath . ' 2x"' : '';
        $logoMarkup = '<img src="' . $logoPath . '"' . $logoRetinaPath . ' alt="">';
    } else {
        $logoMarkup = '<img width="300" src="/thumbs/220x50/f' . $this->getSchemeUrl() . '/img/logo.png" srcset="' . $this->getSchemeUrl() . '/img/logo.png 2x" alt="">';
    }
    $page->info('logoMarkupHeader', $logoMarkup);

    // CMS Page
    if ($page->info('app') == 'cms') {
        $page->info('cms', true);

    // Blog Page
    } else if ($page->info('app') == 'blog') {
        $page->info('blog', true);

    // Directory Page
    } else if ($page->info('app') == 'directory') {
        $page->info('directory', true);
    }

    // Login/Register and User Profile Box
    $page->container('user-profile')->addModule('user-profile');

    // LEC Navigation
    $page->container('lec-navigation')->addModule('lec-navigation');

    // Include #social-share# snippet on main site
    if (Settings::getInstance()->SETTINGS['agent'] === 1) {
        $page->container('social-share')->addModule('social-share', array('style' => 'gradient'));
    }
}
