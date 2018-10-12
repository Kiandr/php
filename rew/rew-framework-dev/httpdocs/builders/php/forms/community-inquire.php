<?php

namespace BDX;

// Error/Success collections
$success = array();
$errors = array();

// Page Title
$app->page_title = "Community Inquiry";

// Set input defaults
if (!empty($user) && $user->isValid()) {
    $_POST['onc5khko'] = $user->info('first_name');
    $_POST['sk5tyelo'] = $user->info('last_name');
    $_POST['mi0moecs'] = $user->info('email');
    $_POST['telephone'] = $user->info('phone');
}

// BDX Database
$db_settings = Settings::getInstance()->DATABASES['bdx'];
$db_bdx = new DB($db_settings['hostname'], $db_settings['username'], $db_settings['password'], $db_settings['database']);
    
// Show Form Flag
$show_form = true;

try {
    // Set Listing ID
    $community_id = (!empty($_POST['community_id']) ? $_POST['community_id'] : $_GET['community_id']);
        
    // Get Listing Data
    if (!empty($community_id)) {
        $query = $app->db_bdx->prepare("SELECT `SubdivisionID`, `Zip`, `BuilderID`, `SubdivisionName` FROM `" . Settings::getInstance()->TABLES['BDX_SUBDIVISIONS'] . "` WHERE `SubdivisionID` = :community_id");
        $query->execute(array(':community_id' => $community_id));
        $community = $query->fetch();
    }
    
    // Lender ID is set, find lender to assign new leads
    if (Settings::getInstance()->FRAMEWORK) {
        $lender = false;
        $lender_id = $_GET['lender'];
        if (isset($_POST['lender'])) {
            $lender_id = $_POST['lender'];
            unset($_POST['lender']);
        }
        if (!empty($lender_id) && (!$user->isValid() || $user->info('lender') < 1)) {
            $lender = \Backend_Lender::load($lender_id);
        }
    }
    
    if (!empty($_POST) && isset($_GET['submit'])) {
        // Form data
        $first_name = trim($_POST['onc5khko']);
        $last_name  = trim($_POST['sk5tyelo']);
        $email      = trim($_POST['mi0moecs']);
        $phone      = trim($_POST['telephone']);
        $comments   = trim($_POST['comments']);
        
        // Check & Save First Name
        $error = strlen($first_name) ? '' : 'Please supply your first name.';
        if (!empty($error)) {
            $errors['first_name'] = $error;
        }
        $user->saveInfo('first_name', $first_name);
    
        // Check & Save Last Name
        $error = strlen($last_name) ? '' : 'Please supply your last name.';
        if (!empty($error)) {
            $errors['last_name'] = $error;
        }
        $user->saveInfo('last_name', $last_name);
    
        // Check & Save Email (Seperate check if Framework is loaded)
        if (Settings::getInstance()->FRAMEWORK) {
            $error = \Validate::email($email) ? '' : 'Please supply a valid email address.';
        } else {
            $error = filter_var($email, FILTER_VALIDATE_EMAIL) ? '' : 'Please supply a valid email address.';
        }
        
        if (!empty($error)) {
            $errors['email'] = $error;
        }
        $user->saveInfo('email', $email);
        
        // Check if not empty or is required & Save Phone Number
        if (strlen($phone)) {
            $phone_test = preg_match_all('/(\d)/', $phone, $matches);
            $error = ($phone_test >= 7) ? '' : 'Please supply a valid phone number.';
            if (!empty($error)) {
                $errors['phone'] = $error;
            }
        }
        $user->saveInfo('phone', $phone);
    
        // Store Comments
        $user->saveInfo('comments', $comments);
        
        // An error has occurred
        if (!empty($errors)) {
            $errors[] = 'An error has occurred.</strong> Please review your information and try again.';
        } else {
            if (Settings::getInstance()->FRAMEWORK) {
                // Require Composer Vendor Auto loader
                require_once $_SERVER['DOCUMENT_ROOT'] . '/../boot/app.php';

                // CMS Database
                $db_settings = \Settings::getInstance()->DATABASES['default'];
                $db = new DB($db_settings['hostname'], $db_settings['username'], $db_settings['password'], $db_settings['database']);
                
                // Require 'contactForm' Function
                require_once(\Settings::getInstance()->DIRS['BACKEND'] . 'inc/php/functions/funcs.ContactSnippets.php');
                            
                // Override referer before passing to contact data
                if (!empty($_POST['source'])) {
                    $_SERVER['HTTP_REFERER'] = $_POST['source'];
                }
                
                // Collect Contact Data
                $userTrackID = collectContactData(array (
                    'first_name' => $user->info('first_name'),
                    'last_name'  => $user->info('last_name'),
                    'email'      => $user->info('email'),
                    'phone'      => $user->info('phone'),
                    'comments'   => $user->info('comments'),
                    'subject'    => 'BDX Community Inquiry - ' . $community['SubdivisionID'],
                    'forms'      => 'BDX Community Inquiry',
                    'listing'    => $community,
                    'message'    => getFormVars(),
                    'lender'    => (!empty($lender) ? $lender->getId()  : null),
                ), 6);
                
                if (!empty($userTrackID) && !empty($user) && $user->isValid()) {
                    // Get the assigned agent
                    $query = $db->query("SELECT `id`, `first_name`, `last_name`, `cell_phone`, `title`, `image`, `email` FROM `agents` WHERE `id` = " . $db->quote($user->info('agent')) . " LIMIT 1");
                    $agent = $query->fetch();
                    
                    // Create leadPosting Object
                    $leadPosting = new LeadPosting($user);
                    
                    // Post the Lead to SOAP server
                    if ($leadPosting->post('community', array(
                        'FirstName'         => $agent['first_name'],
                        'LastName'          => $agent['last_name'],
                        'EmailAddress'      => $agent['email'],
                        'PostalCode'        => $community['Zip'],
                        'BuilderId'         => $community['BuilderID'],
                        'CommunityId'       => $community['SubdivisionID'],
                    ))) {
                        // Unset post after successful inquiry
                        unset($_POST);
                        $success[] = "Your community inquiry has been successfully sent.";
                        $show_form = false;
                    } else {
                        $errors[] = 'An error occurred while processing your request.';
                    }
                    
                // Errors have occurred
                } else {
                    $errors[] = 'An error occurred while processing your request.';
                }
            } else {
                // @TODO Process form submission on stand alone
                $success[] = "Your community inquiry has been successfully sent.";
            }
        }
    }
// Error occurred
} catch (Exception $e) {
    //Log::error($e);
}

// Render Community Inqire Form
require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/forms/community-inquire.tpl');
