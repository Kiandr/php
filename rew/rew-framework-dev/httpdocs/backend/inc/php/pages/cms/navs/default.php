<?php

use REW\Backend\Auth\DomainAuth;
use REW\Backend\Auth\BlogsAuth;
use REW\Backend\CMS\Interfaces\SubdomainFactoryInterface;

$container = Container::getInstance();

// Create Auth Classes
$blogsAuth = $container->get(BlogsAuth::class);
$domainAuth = $container->get(DomainAuth::class);

// Get Subdomain being Edited
$subdomainFactory = $container->get(SubdomainFactoryInterface::class);
$subdomain = $subdomainFactory->buildSubdomainFromRequest('canManageNav');
$subdomainAuth = $subdomain ? $subdomain->getAuth() : $domainAuth;

// Check general authorization permissions
if (!($subdomain || $blogsAuth->canManageLinks($this->auth))) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to edit CMS navigation.')
    );
}

// If in agent or team subdomain mode
if(!empty($subdomain)) {
    $subdomain->validateSettings();
}

// Navigation Content
$navs = [];

// Check Navigation Authorization
if ($subdomainAuth->canManageNav($authuser)) {
    $navs[]= [
        'name' => 'CMS Navigation',
        'link' => '/backend/cms/?filter=nav' . $subdomain->getPostLink(true),
        'description' => __('Description')
    ];
}

// Check Blogs Authorization
if ($blogsAuth->canManageLinks($authuser)) {
    $navs[]= [
        'name' => __('Blog Links'),
        'link' => '/backend/cms/navs/blog-links/',
        'description' => __('Description')
    ];
}

// Unauthorized to view
if (empty($navs)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage site navigation.')
    );
}
