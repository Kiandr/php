<?php

// Full Page
$body_class = 'full';

// Get Authorization Managers
$partnersAuth = new REW\Backend\Auth\PartnersAuth(Settings::getInstance());

// Authorized to manage directories
if (!$partnersAuth->canViewPartners($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to view partners')
    );
}

// Check Permissions
$authorized = [];

// Happy  Grasshopper
if ($partnersAuth->canManageGrasshopper($authuser)) {
    $authorized[] = 'grasshopper';
}

// BombBomb
if ($partnersAuth->canManageBombomb($authuser)) {
    $authorized[] = 'bombbomb';
}

// Follow Up Boss
/*
 * @todo Follow Up Boss removed for V1 of REW CRM.
if ($partnersAuth->canManageFollowupboss($authuser)) {
	$authorized[] = 'followupboss';
}
*/

// REW Dialer (Espresso)
if ($partnersAuth->canManageEspresso($authuser)) {
    $authorized[] = 'espresso';
}

// WiseAgent
if ($partnersAuth->canManageWiseagent($authuser)) {
    $authorized[] = 'wiseagent';
}

// Zillow
if ($partnersAuth->canManageZillow($authuser)) {
    $authorized[] = 'zillow';
}

// FirstCallAgent
if ($partnersAuth->canManageFirstcallagent($authuser)) {
    $authorized[] = 'firstcallagent';
}

// DotLoop
if ($partnersAuth->canManageDotloop($authuser)) {
    $authorized[] = 'dotloop';
}

// Disconnect
if (!empty($_GET['disconnect'])) {
    // Current partners
    $partners = $authuser->info('partners');

    // Happy Grasshopper
    if ($_GET['disconnect'] === 'grasshopper' && in_array('grasshopper', $authorized) && !empty($partners['grasshopper'])) {
        unset($partners['grasshopper']);
        $success[] = __('Happy Grasshopper&reg; Integration has been successfully disabled.');
    }

    // BombBomb
    if ($_GET['disconnect'] === 'bombbomb' && in_array('bombbomb', $authorized) && !empty($partners['bombbomb'])) {
        unset($partners['bombbomb']);
        $success[] = __('BombBomb Integration has been successfully disabled.');
    }

    // Follow Up Boss
    /*
	 * @todo Follow Up Boss removed for V1 of REW CRM.
	if ($_GET['disconnect'] === 'followupboss' && in_array('followupboss', $authorized) && !empty($partners['followupboss'])) {
		unset($partners['followupboss']);
		$success[] = 'Follow Up Boss Integration has been successfully disabled.';
	}
	*/

    // WiseAgent
    if ($_GET['disconnect'] === 'wiseagent' && in_array('wiseagent', $authorized) && !empty($partners['wiseagent'])) {
        unset($partners['wiseagent']);
        $success[] = __('Wise Agent Integration has been successfully disabled.');
    }

    // Zillow
    if ($_GET['disconnect'] === 'zillow' && in_array('zillow', $authorized) && !empty($partners['zillow'])) {
        unset($partners['zillow']);
        $success[] = __('Zillow Integration has been successfully disabled.');
    }

    // DotLoop
    if ($_GET['disconnect'] === 'dotloop' && in_array('dotloop', $authorized) && !empty($partners['dotloop'])) {
        unset($partners['dotloop']);
        $success[] = __('DotLoop Integration has been successfully disabled.');
    }

    // Remove partner settings
    $query = DB::get()->prepare("UPDATE `agents` SET `partners` = :partners WHERE `id` = :id;");
    $query->execute(['partners' => json_encode($partners), 'id' => $authuser->info('id')]);

    // Redirect
    $authuser->setNotices($success, $errors);
    header('Location: ?success');
    exit;
}
