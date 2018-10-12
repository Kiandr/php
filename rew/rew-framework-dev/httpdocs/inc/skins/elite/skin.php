<?php

// Page Resource
$page = $this->getPage();

// Agent Details
if ($page->config('building') || ($page->info('name') == 'agents' && isset($_GET['aname']))) {
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
        'thumbnails' => '350x350/f'
    ));

// Office Details
} else if ($page->config('building') || ($page->info('name') == 'offices' && isset($_GET['oid']))) {
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
    // Content
    $page->container('content')->addModule('rew_content');
}

// Not Popup Window
if (!isset($_GET['popup'])) {
    // Logo Markup Header
    $pageLogo = $page->info('template') === 'cover' ? 'logo_homepage' : 'logo_content_page';
    $logoPath = $page->getSkin()->getLogoPath($pageLogo);
    if (!empty($logoPath)) {
        $logoRetinaPath = $page->getSkin()->getLogoPath('retina_' . $pageLogo);
        $logoRetinaPath = !empty($logoRetinaPath) ? ' srcset="' . $logoRetinaPath . ' 2x"' : '';
    } else {
        $logoPath = $this->getSchemeUrl() . '/img/logo.svg';
        $logoRetinaPath = '';
    }
    $logoMarkup = '<img class="" src="' . $logoPath . '"' . $logoRetinaPath . ' alt="">';
    $page->info('logoMarkupHeader', $logoMarkup);
    // Logo Markup Footer
    $logoPath = $page->getSkin()->getLogoPath('logo_footer');
    if (!empty($logoPath)) {
        $logoRetinaPath = $page->getSkin()->getLogoPath('retina_logo_footer');
        $logoRetinaPath = !empty($logoRetinaPath) ? ' srcset="' . $logoRetinaPath . ' 2x"' : '';
    } else {
        $logoPath = $this->getSchemeUrl() . '/img/footer-logo.svg';
    }
    $logoMarkup = '<img src="' . $logoPath . '"' . $logoRetinaPath . ' alt="">';
    $page->info('logoMarkupFooter', $logoMarkup);

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

    // Social media slideout
    $page->container('sm-slideout')->addModule('sm-slideout');

    // Subdomain Site Header
    if ($page->info('name') == 'homepage') {
        // Agent Site Header
        if (isset(Settings::getInstance()->SETTINGS['agent']) && Settings::getInstance()->SETTINGS['agent'] !== 1) {
            $page->container('sub-feature')->addModule('agent-subdomain', array(
                'agent' => Settings::getInstance()->SETTINGS['agent'],
                'homepage' => Http_Uri::getUri() === '/',
                'feature_image' => $page->info('feature_image')
            ));
        // Team Site Header
        } else if (isset(Settings::getInstance()->SETTINGS['team']) && Settings::getInstance()->SETTINGS['team'] !== 1) {
            $page->container('sub-feature')->addModule('team-subdomain', array(
                'team' => Settings::getInstance()->SETTINGS['team'],
                'homepage' => Http_Uri::getUri() === '/',
                'feature_image' => $page->info('feature_image')
            ));
        }
    }
}

// user-session module is used to inject user details into an otherwise static page.
$page->container('footer')->addModule('user-session');
