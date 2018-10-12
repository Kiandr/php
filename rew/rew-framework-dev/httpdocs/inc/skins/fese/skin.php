<?php

// Page Resource
$page = $this->getPage();

// Get Settings
$settings = Settings::getInstance();

// Get DB
$db = DB::get('cms');

// IDX Page Templates
if ($page->info('app') === 'idx') {
    if (in_array($page->info('name'), ['search','search_map'])) {
        $page->setTemplate('idx/results');
    } else {
        $page->setTemplate('idx/details');
    }
}

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
        'thumbnails' => '350x350/f'
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
    // Logo Markup Header
    $isSubdomain = $settings->SETTINGS['agent'] > 1 || $settings->SETTINGS['team'];

    // Find snippet from database
    $snippet = $db->prepare(
        "SELECT `id` FROM `snippets` WHERE (`agent` = :agent OR `team` = :team) AND `name` = :name;"
    );
    $snippet->execute(array(
        'agent' => ($settings->SETTINGS['agent']),
        'team' => ($settings->SETTINGS['team']),
        'name' => 'site-logo-link'
    ));
    $snippet = $snippet->fetch();
    $hasSnippet = !empty($snippet);

    $pageLogo = $page->info('template') === 'cover' ? 'logo_homepage' : 'logo_content_page';
    if (((!$isSubdomain && $hasSnippet) || !$hasSnippet) && !empty($logoPath = $page->getSkin()->getLogoPath($pageLogo))) {
        $logoRetinaPath = $page->getSkin()->getLogoPath('retina_' . $pageLogo);
        $logoRetinaPath = !empty($logoRetinaPath) ? ' srcset="' . $logoRetinaPath . ' 2x"' : '';
        $logoMarkup = '<a href="/"><img src="' . $logoPath . '"' . $logoRetinaPath . ' title="Site Logo"></a>';
    } else {
        $logoMarkup = rew_snippet('site-logo-link', false);
    }
    $page->info('logoMarkupHeader', $logoMarkup);

    // User Links
    $page->container('user-links')->addModule('user-links');

    // Application flag
    $page->info($page->info('app'), true);
}
