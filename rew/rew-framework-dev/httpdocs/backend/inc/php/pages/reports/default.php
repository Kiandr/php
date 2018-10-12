<?php

// Full Page
$body_class = 'full';

// Get Authorization Managers
$reportsAuth = new REW\Backend\Auth\ReportsAuth(Settings::getInstance());

// Authorized to manage directories
if (!$reportsAuth->canViewReports($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to view reports')
    );
}

// Google Analytics Report
$analytics = $reportsAuth->canViewAnalyticsReport($authuser);

// Agent Response Report
$response = $reportsAuth->canViewResponseReport($authuser)
    || $reportsAuth->canViewOwnResponseReports($authuser);

// MLS Listing Report
$listing = $reportsAuth->canViewListingReport($authuser);

// REW Dialer (Espresso) Report
$dialer = $reportsAuth->canViewDialerReport($authuser)
    || $reportsAuth->canViewOwnDialerReport($authuser);

// Action Plans - Task Report
$tasks = $reportsAuth->canViewActionPlanReports($authuser);
