<?php

// Create Auth Classes
$settings = Settings::getInstance();

// Get Authorization Managers
$subdomainFactory = Container::getInstance()->get(\REW\Backend\CMS\Interfaces\SubdomainFactoryInterface::class);
$subdomain = $subdomainFactory->buildSubdomainFromRequest('canViewTools');
if (!$subdomain) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to view content tools.')
    );
}
$subdomain->validateSettings();
$subdomains = $subdomainFactory->getSubdomainList('canViewTools') ?: [];
$subdomainPostLink = $subdomain ? $subdomain->getPostLink() : '';

$show_conversion_tracking = false;
$show_communities = false;
$show_backup = false;
$show_rewrite = false;
$show_slideshow = false;
$show_testimonials = false;
$show_radio_landing_page = false;
$show_tracking_codes = false;
$show_developments = false;
if ($subdomain->getAuth()->canViewTools()) {
    $show_conversion_tracking = $subdomain->getAuth()->canManageConversionTracking();
    $show_communities         = $subdomain->getAuth()->canManageCommunities();
    $show_backup              = $subdomain->getAuth()->canManageBackup();
    $show_rewrite             = $subdomain->getAuth()->canManageRewrites();
    $show_slideshow           = $subdomain->getAuth()->canManageSlideshow();
    $show_testimonials        = $subdomain->getAuth()->canManageTestimonials();
    $show_radio_landing_page  = $subdomain->getAuth()->canManageRadioLandingPage();
    $show_tracking_codes      = $subdomain->getAuth()->canManageTracking();
    $show_developments        = Skin::hasFeature(Skin::REW_DEVELOPMENTS)
        && ($subdomain->getAuth()->canManageDevelopments() || $subdomain->getAuth()->canManageOwnDevelopments());
} else {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to view content tools.')
    );
}
