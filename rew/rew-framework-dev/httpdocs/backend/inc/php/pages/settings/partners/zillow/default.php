<?php

// Full Page
$body_class = 'full';

// Get Authorization Managers
$partnersAuth = new REW\Backend\Auth\PartnersAuth(Settings::getInstance());

// Require Authorization
if (!$partnersAuth->canManageZillow($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage zillow integrations')
    );
}

// Get API Key
$db = DB::get();

// Partner instance
$api = new Partner_Zillow();

// API User Key
$site_key = $authuser->info('partners.zillow.key');
$site_secret = $authuser->info('partners.zillow.secret');
$user_id = $authuser->info('partners.zillow.id');
$global_assignment = $authuser->info('partners.zillow.global_assignment') === "true" ? true : false;

// API integration status
if (!empty($site_key) && !empty($site_secret) && !empty($user_id)) {
    // Set options
    $api->setOptions([
        'user_id' => $user_id,
        'api_key' => $site_key,
        'api_secret' => $site_secret
    ]);

    // Test validity
    $account = $api->getAccount();
    if ($err = $api->getLastError()) {
        unset($account);
    }
}

// Form action
if (isset($account)) {
    $form_action = '?update';
} else {
    $form_action = '?create';
}

// Success
$success = array();

// Errors
$errors = array();

// Delete submitted
if (isset($_GET['delete'])) {
    // Check for account before continuing
    if (empty($account)) {
        $errors []= __('The Zillow Integration to be disabled could not be found.');
    } else {
        // Check For Error
        $success = $api->deleteAccount();
        $error = $api->getLastError();
        if (!empty($error)) {
            $errors[] = __('Zillow Integration returned an error: %s', $api->getLastError());
        }
    }

    // If the account was succesfully deleted, redirect
    if ($success && empty($errors)) {
        // Redirect
        header('Location: ../?disconnect=zillow');
        exit;
    }
}

// Import Request submitted
if (isset($_GET['import'])) {
    // DB Users
    $db_users = DB::get('users');

    // Check for account before continuing
    if (empty($account)) {
        $errors []= __('The Zillow Integration to be imported could not be found.');
    } else {
        // Check For Error
        $leads = $api->getLeads();
        $error = $api->getLastError();
        if (!empty($error)) {
            $errors[] = __('Zillow Integration returned an error: %s', $api->getLastError());
        }
    }

    // If the account was succesfully deleted, redirect
    if (isset($leads) && empty($errors)) {
        // Import Leads
        $count = 0;

        // Build Query
        $leadQuery = $db->prepare("SELECT `id` FROM `users` WHERE `email` = :email");

        foreach ($leads as $lead) {
            $leadQuery->execute(['email' => $lead['email']]);
            $existing_lead = $leadQuery->fetchColumn();
            if (!$existing_lead) {
                try {
                    $data = [
                        'first_name'         => $lead['first_name'],
                        'last_name'          => $lead['last_name'],
                        'email'              => $lead['email'],
                        'remarks'            => $lead['comment'],
                        'phone'              => $lead['phone'],
                        'city'               => $lead['location']['city'],
                        'state'              => $lead['location']['state'],
                        'zip'                => $lead['location']['zip'],
                        'referer'            => 'Zillow'
                    ];
                    if(!$global_assignment) {
                        $data['agent'] = $authuser->info('id');

                        // Create Lead Object
                        $new_lead = new Backend_Lead($data, $db_users);
                        $new_lead->save();
                    } else {
                        // Create Lead Using Global Assignment Routine
                        collectContactData($data, 0);
                        // Fetch new Lead ID
                        $leadQuery->execute(['email' => $lead['email']]);
                        $new_id = $leadQuery->fetchColumn();

                        //Load New Lead
                        $new_lead = (new Backend_Lead())->load($new_id);
                    }

                    // Assign Group
                    $new_lead->assignGroup($api->getGroup());
                    $count++;
                } catch (\Exception $e) {
                    $errors[] = __('Lead could not be imported: %s', $e->getMessage());
                }
            }
        }

        // Success
        if (!empty($count)) {
            $success[] = __('Succesfully imported %s new leads from zillow.', $count);
        } else {
            $success[] = __('There were no new leads to import from zillow.');
        }
    }

    // Save Notices
    $authuser->setNotices($success, $errors);

    // Redirect
    header('Location: ?');
    exit;
}

// Form submitted
if (isset($_GET['create'])) {
    // Get API Endpoint
    $api_endpoint = URL . 'api/crm/v1/';

    // Get API Key
    $api_key_query = $db->prepare("SELECT `api_key` FROM `api_applications` WHERE `id` = 1;");
    $api_key_query->execute();
    $api_key = $api_key_query->fetchColumn();
    if (empty($api_key)) {
        $errors[] = __('A valid API key is required to set up a Zillow Integration.');
    }

    // Get zillow api account
    $account = $api->requestAccount($api_endpoint, $api_key, $authuser->info('id'), $authuser->info('first_name') . ' ' . $authuser->info('last_name'));

    // Check For Error
    $error = $api->getLastError();
    if (!empty($error)) {
        $errors[] = __('Zillow Integration returned an error: %s', $api->getLastError());
    }

    // Check for Requried Fields
    if (empty($account['id']) || empty($account['auth']['key']) || empty($account['auth']['secret'])) {
        $errors[] = __('Failed to obtain authentication details from Zillow Integration;. Please try again later.');
        return;
    }

    // Check Errors
    if (empty($errors)) {
        // Current partners
        $partners = $authuser->info('partners');

        // Merge changes
        $partners = array_merge($partners, array(
            'zillow' => array(
                'id' => $account['id'],
                'key' => $account['auth']['key'],
                'secret' => $account['auth']['secret']
            ),
        ));

        // Build query
        $partnerUpdate = $db->prepare("UPDATE `agents` SET `partners` = :partners WHERE `id` = :id;");
        if ($partnerUpdate->execute(['partners' => json_encode($partners), 'id' => $authuser->info('id')])) {
            // Success
            $success[] = __('Zillow Integration has been set up.');

            // Save Notices
            $authuser->setNotices($success, $errors);

            // Redirect
            header('Location: ?');
            exit;
        }
    }
}

if (isset($_GET['update'])) {
    // Current partners
    $partners = $authuser->info('partners');

    // Grab changes
    $partners["zillow"]["global_assignment"] = $_POST["global_assignment"];

    // Build query
    $partnerUpdate = $db->prepare("UPDATE `agents` SET `partners` = :partners WHERE `id` = :id;");
    if ($partnerUpdate->execute(['partners' => json_encode($partners), 'id' => $authuser->info('id')])) {
        // Success
        $success[] = __('Your changes have successfully been saved.');

        // Save Notices
        $authuser->setNotices($success, $errors);

        // Redirect
        header('Location: ?');
        exit;
    }
}
