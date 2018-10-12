<?php

// App DB
$db = DB::get();

// App Settings
$settings = Settings::getInstance();

// Success
$success = array();

// Error
$errors = array();

// Lead ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// Query Lead
$lead = $db->fetch("SELECT * FROM `" . LM_TABLE_LEADS . "` WHERE `id` = :id;", ['id' => $_GET['id']]);

/* Throw Missing $lead Exception */
if (empty($lead)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingLeadException();
}

// Create lead instance
$lead = new Backend_Lead($lead);

// Get Lead Authorization
$leadAuth = new REW\Backend\Auth\Leads\LeadAuth($settings, $authuser, $lead);

// Not authorized to view all lead transactions
if (!$leadAuth->canViewTransactions()) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        'You do not have permission to view lead transactions'
    );
}

// Can Create, Edit & Delete Transactions
$can_create_transactions = $leadAuth->canAddTransaction();
$can_edit_transactions = $can_delete_transactions = true;

// Add Transaction
$_GET['add'] = isset($_POST['add']) ? $_POST['add'] : $_GET['add'];

// Edit Transaction
$_GET['edit'] = isset($_POST['edit']) ? $_POST['edit'] : $_GET['edit'];

// Show or Hide Form
$show_form = isset($_GET['add']) && !empty($can_create_transactions) ? true : false;

/**
 * Delete Transaction
 */
if (!empty($_GET['delete'])) {
    if (!$can_delete_transactions) {
        $errors[] = 'You do not have permission to delete this leads transactions.';
    } else {
        // Require Transaction
        $query = "SELECT * FROM `" . $settings->TABLES['LM_TRANSACTIONS'] . "` WHERE `id` = '" . mysql_real_escape_string($_GET['delete']) . "' AND `user_id` = '" . $lead['id'] . "';";
        if ($result = mysql_query($query)) {
            $delete = mysql_fetch_assoc($result);
            if (!empty($delete)) {
                // Delete from Database
                $query = "DELETE FROM `" . $settings->TABLES['LM_TRANSACTIONS'] . "` WHERE `id` = '" . mysql_real_escape_string($delete['id']) . "';";
                if (mysql_query($query)) {
                    try {
                        // Log Event: New Lead Transaction
                        $event = new History_Event_Delete_LeadTransaction(array(
                            'type' => $delete['type'],
                            'list_price' => $delete['list_price'],
                            'sold_price' => $delete['sold_price'],
                            'mls_number' => $delete['mls_number'],
                            'details' => $delete['details']
                        ), array(
                            new History_User_Lead($lead['id']),
                            $authuser->getHistoryUser()
                        ));

                        // Save to DB
                        $event->save();
                    } catch (Exception $e) {
                        $errors[] = 'Transaction deletion failed to update lead history.';
                    }

                    // Success
                    $success[] = 'The selected transaction has been deleted.';

                // Query Error
                } else {
                    $errors[] = 'An error occurred while trying to delete the selected transaction.';
                }
            }
        }
    }
}
/**
         * Edit Transaction
         */
if (!empty($_GET['edit'])) {
    if (!$can_edit_transactions) {
        $errors[] = 'You do not have permission to edit this leads transactions.';
    } else {
            // Require Transaction
            $query = "SELECT * FROM `" . $settings->TABLES['LM_TRANSACTIONS'] . "` WHERE `id` = '" . mysql_real_escape_string($_GET['edit']) . "' AND `user_id` = '" . $lead['id'] . "';";
        if ($result = mysql_query($query)) {
            $edit = mysql_fetch_assoc($result);
            if (!empty($edit)) {
                // Show Edit Form
                $show_form = true;

                /**
                     * Process Submit
                     */
                if (isset($_GET['submit'])) {
                    // Required Fields
                    $required   = array();
                    $required[] = array('value' => 'type',       'title' => 'Type');
                    $required[] = array('value' => 'list_price', 'title' => 'List Price');
                    $required[] = array('value' => 'sold_price', 'title' => 'Sold Price');
                    $required[] = array('value' => 'details',    'title' => 'Details');

                    // Require Numeric Value
                    $_POST['list_price'] = str_replace(array('$', ','), '', $_POST['list_price']);
                    $_POST['sold_price'] = str_replace(array('$', ','), '', $_POST['sold_price']);

                    // Process Required Fields
                    foreach ($required as $require) {
                        if (empty($_POST[$require['value']])) {
                            $errors[] = $require['title'] . ' is a required field.';
                        }
                    }

                    // Check Errors
                    if (empty($errors)) {
                        // Build UPDATE Query
                        $query = "UPDATE `" . $settings->TABLES['LM_TRANSACTIONS'] . "` SET "
                               . "`user_id`    = '" . $lead['id'] . "', "
                               . "`type`       = '" . mysql_real_escape_string($_POST['type']) . "', "
                               . "`list_price` = '" . mysql_real_escape_string($_POST['list_price']) . "', "
                               . "`sold_price` = '" . mysql_real_escape_string($_POST['sold_price']) . "', "
                               . "`mls_number` = '" . mysql_real_escape_string($_POST['mls_number']) . "', "
                               . "`details`    = '" . mysql_real_escape_string($_POST['details']) . "'"
                               . " WHERE `id` = '" . $edit['id'] . "';";

                        // Execute Query
                        if (mysql_query($query)) {
                            // Success
                            $success[] = 'Transaction has successfully been saved.';

                            // Unset $_POST Data
                            unset($show_form, $_POST['type'], $_POST['list_price'], $_POST['sold_price'], $_POST['mls_number'], $_POST['details']);

                            // Query Error
                        } else {
                            $errors[] = 'An error occurred while attempting to save transaction.';
                        }
                    }
                } else {
                    // Set $_POST Defaults
                    foreach ($edit as $k => $v) {
                        $_POST[$k] = isset($_POST[$k]) ? $_POST[$k] : $v;
                    }
                }

                // Transaction not Found
            } else {
                $errors[] = 'The selected transaction could not be found.';
            }

            // Query Error
        } else {
            $errors[] = 'An error occurred while trying to delete the locate the selected transaction.';
        }
    }
} else {

    /**
     * New Transaction
     */
    if (isset($_GET['submit'])) {
        if (!$can_create_transactions) {
            $errors[] = 'You do not have permission to add transactions to this lead.';
        } else {
                // Required Fields
                $required   = array();
                $required[] = array('value' => 'type',       'title' => 'Type');
                $required[] = array('value' => 'list_price', 'title' => 'List Price');
                $required[] = array('value' => 'sold_price', 'title' => 'Sold Price');
                $required[] = array('value' => 'details',    'title' => 'Details');

                // Require Numeric Value
                $_POST['list_price'] = str_replace(array('$', ','), '', $_POST['list_price']);
                $_POST['sold_price'] = str_replace(array('$', ','), '', $_POST['sold_price']);

                // Process Required Fields
            foreach ($required as $require) {
                if (empty($_POST[$require['value']])) {
                    $errors[] = $require['title'] . ' is a required field.';
                }
            }

                // Check Errors
            if (empty($errors)) {
                // Locate IDX Listing
                $listing = '';
                if (!empty($_POST['mls_number'])) {
                    // Load IDX resources
                    $idx = Util_IDX::getIDX();
                    $db_idx = Util_IDX::getDatabase();

                    $search_where = "`" . $idx->field('ListingMLS') . "` = '" . $db_idx->cleanInput($_POST['mls_number']) . "'";

                    // Any global criteria
                    $idx->executeSearchWhereCallback($search_where);

                    $listing = $db_idx->fetchQuery("SELECT " . $idx->selectColumns() . " FROM `" . $idx->getTable() . "` WHERE " . $search_where . ";");
                    if (!empty($listing)) {
                        // Parse Listing
                        $listing = Util_IDX::parseListing($idx, $db_idx, $listing);

                        // Serialize Listing Row
                        $listing = serialize($listing);
                    }
                }

                // Build INSERT Query
                $query = "INSERT INTO `" . $settings->TABLES['LM_TRANSACTIONS'] . "` SET "
                       . "`user_id`    = '" . $lead['id'] . "', "
                       . "`agent_id`   = '" . $authuser->info('id') . "', "
                       . "`type`       = '" . mysql_real_escape_string($_POST['type']) . "', "
                       . "`list_price` = '" . mysql_real_escape_string($_POST['list_price']) . "', "
                       . "`sold_price` = '" . mysql_real_escape_string($_POST['sold_price']) . "', "
                       . "`mls_number` = '" . mysql_real_escape_string($_POST['mls_number']) . "', "
                       . "`details`    = '" . mysql_real_escape_string($_POST['details']) . "', "
                       . "`listing`    = '" . mysql_real_escape_string($listing) . "', "
                       . "`timestamp`  = NOW();";

                // Execute Query
                if (mysql_query($query)) {
                    // Success
                    $success[] = 'Transaction has successfully been saved.';

                    try {
                        // Log Event: New Lead Transaction
                        $event = new History_Event_Create_LeadTransaction(array(
                            'type' => $_POST['type'],
                            'list_price' => $_POST['list_price'],
                            'sold_price' => $_POST['sold_price'],
                            'mls_number' => $_POST['mls_number'],
                            'details' => $_POST['details']
                        ), array(
                            new History_User_Lead($lead['id']),
                            $authuser->getHistoryUser()
                        ));

                        // Save to DB
                        $event->save();
                    } catch (Exception $e) {
                        $errors[] = 'Transaction failed to update lead history.';
                    }
                    // Unset $_POST Data
                    unset($show_form, $_POST['type'], $_POST['list_price'], $_POST['sold_price'], $_POST['mls_number'], $_POST['details']);

                    // Query Error
                } else {
                    $errors[] = 'An error occurred while attempting to add transaction.';
                }
            }
        }
    }
}

// Manage Transactions
$transactions = array();

// Build Collection
$query = "SELECT *, UNIX_TIMESTAMP(`timestamp`) AS `timestamp` FROM `" . $settings->TABLES['LM_TRANSACTIONS'] . "` WHERE `user_id` = '" . $lead['id'] . "' ORDER BY `timestamp` DESC;";
if ($result = mysql_query($query)) {
    while ($row = mysql_fetch_assoc($result)) {
        // Listing Row
        $row['listing'] = unserialize($row['listing']);

        // Add to Collection
        $transactions[] = $row;
    }

// Query Error
} else {
    $errors[] = 'Error Occurred while loading Lead Transactions';
}
