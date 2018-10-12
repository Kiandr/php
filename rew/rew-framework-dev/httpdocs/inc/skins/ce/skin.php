<?php

// Page Resource
$page = $this->getPage();

// Get Settings
$settings = Settings::getInstance();

// Get DB
$db = DB::get('cms');

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
        'thumbnails' => '350x350/r',
        'testimonials' => 1,
        'listings' => 6
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
} else {
    // Page Content
    $page->container('content')->addModule('rew_content');
}

// Not within ?popup window
if (!isset($_GET['popup'])) {
    // Force Map API to Load if Drive Time is enabled and idx-search module is loaded or using the homepage search
    if (!empty(Settings::getInstance()->MODULES['REW_IDX_DRIVE_TIME'])) {
        if (!empty($page->moduleLoaded('idx-search')) || $page->info('name') === 'homepage') {
            $this->loadMapApi();
        }
    }

    // Logo Markup Header
    $isSubdomain = $settings->SETTINGS['agent'] > 1 || $settings->SETTINGS['team'];

    // Find snippet from database
    $snippet = $db->prepare(
        "SELECT `id` FROM `snippets` WHERE (`agent` = :agent OR `team` = :team) AND `name` = :name;"
    );
    $snippet->execute(array(
        'agent' => ($settings->SETTINGS['agent']),
        'team' => ($settings->SETTINGS['team']),
        'name' => 'site-logo'
    ));
    $snippet = $snippet->fetch();
    $hasSnippet = !empty($snippet);

    $pageLogo = $page->info('template') === 'cover' ? 'logo_homepage' : 'logo_content_page';
    if (((!$isSubdomain && $hasSnippet) || !$hasSnippet) && !empty($logoPath = $page->getSkin()->getLogoPath($pageLogo))) {
        $logoRetinaPath = $page->getSkin()->getLogoPath('retina_' . $pageLogo);
        $logoRetinaPath = !empty($logoRetinaPath) ? ' srcset="' . $logoRetinaPath . ' 2x"' : '';
        $logoMarkup = '<a href="/">' . '<img src="' . $logoPath . '"' . $logoRetinaPath . ' title="Site Logo">' . '</a>';
    } else {
        $logoMarkup = '<a class="logo-link" href="/">' . rew_snippet('site-logo', false) . '</a>';
    }
    $page->info('logoMarkupHeader', $logoMarkup);

    // Logo Markup Footer
    if (((!$isSubdomain && $hasSnippet) || !$hasSnippet) && !empty($logoPath = $this->getLogoPath('logo_footer'))) {
        $logoRetinaPath = $page->getSkin()->getLogoPath('retina_logo_footer');
        $logoRetinaPath = !empty($logoRetinaPath) ? ' srcset="' . $logoRetinaPath . ' 2x"' : '';
        $logoMarkup = '<img src="' . $logoPath . '"' . $logoRetinaPath . ' title="Site Logo">';
    } else {
        $logoMarkup = rew_snippet('site-logo', false);
    }
    $page->info('logoMarkupFooter', $logoMarkup);

    // Application flag
    $appName = $page->info('app');
    $page->info($appName, true);

    // User Links
    $page->container('user-links')->addModule('user-links');

    // Subscribe CTA
    $page->container('subscribe-cta')->addModule('subscribe-cta');

    // Quick search container
    $templateName = $page->getTemplate()->getName();
    if (in_array($appName, ['cms', 'blog']) || $templateName === 'results') {
        if (!in_array($templateName, ['cover', 'details'])) {
            $page->container('quick-search')->addModule('idx-search', [
                'advanced' => in_array($templateName, ['results'])
            ]);
        }
    }
}
