<?php

// Create Auth Classes
$settings = Settings::getInstance();

// Get Authorization Managers
$subdomainFactory = Container::getInstance()->get(\REW\Backend\CMS\Interfaces\SubdomainFactoryInterface::class);
$subdomain = $subdomainFactory->buildSubdomainFromRequest('canManageTracking');
if (!$subdomain) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to edit tracking codes.')
    );
}
$subdomain->validateSettings();
$subdomains = $subdomainFactory->getSubdomainList('canManageTracking');

// DB Connection
$db = DB::get();

// Success
$success = array();

// Errors
$errors = array();

// Process Submit on POST
if (isset($_GET['submit']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    // Build UPDATE Query
    $query = "UPDATE `" . TABLE_SETTINGS . "` SET "
           . "`verifyv1`	= '" . mysql_real_escape_string(htmlspecialchars($_POST['metatag'])) . "', "
           . "`uacct`		= '" . mysql_real_escape_string(htmlspecialchars($_POST['analytics'])) . "', "
           . "`msvalidate`	= '" . mysql_real_escape_string(htmlspecialchars($_POST['msvalidate'])) . "', "
           . "`hittail`		= '" . mysql_real_escape_string(htmlspecialchars($_POST['hittail'])) . "', "
           . "`google_apikey`     = '" . mysql_real_escape_string($_POST['google_apikey']) . "', "
           . "`google_secret`     = '" . mysql_real_escape_string($_POST['google_secret']) . "' "
           . "WHERE " . $subdomain->getOwnerSql() . ";"
    ;

    // Execute Query
    if (mysql_query($query)) {
        // Success
        $success[] = __('CMS Tracking Codes have successfully been updated.');

    // Query Error
    } else {
        $errors[] = __('CMS Tracking Codes could not be updated.');
    }
}


// Select Tracking Codes
$result = mysql_query("SELECT `google_apikey`, `google_secret`, `verifyv1`, `uacct`, `msvalidate`, `hittail` FROM `" . TABLE_SETTINGS . "` WHERE " . $subdomain->getOwnerSql() . ";");
list ($google_apikey, $google_secret, $metav1, $uacct, $msvalidate, $hittail) = mysql_fetch_row($result);
