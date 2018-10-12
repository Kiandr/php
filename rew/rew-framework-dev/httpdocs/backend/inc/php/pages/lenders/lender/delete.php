<?php

// App DB
$db = DB::get();

// Full Page
$body_class = 'full';

// Get Authorization
$lenderAuth = new REW\Backend\Auth\LendersAuth(Settings::getInstance());

// Require permission to edit all associates
if (!$lenderAuth->canDeleteLenders($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to delete lenders.')
    );
}

$can_delete = true;

// Row ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// Success
$success = array();

// Errors
$errors = array();

// Load Backend_Lender
$lender = Backend_Lender::load($_GET['id']);

/* Throw Missing ID Exception */
if (empty($lender)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingLenderException();
}

// Count Leads
try {
    $leads = $db->fetch("SELECT COUNT(`id`) AS `total` FROM `users` WHERE `lender` = :lender;", ["lender" => $lender->getId()]);
    $leads = $leads['total'];
} catch (PDOException $e) {}

// Confirm Delete
if (!empty($_POST['delete'])) {
    // Require Leads
    if (!empty($leads)) {
        // Re-Assign to Lender (or Un-Assign)
        $reassign = !empty($_POST['lender']) ? Backend_Lender::load($_POST['lender']) : null;

        // Re-Assigned Leads
        $assigned = array();

        // Get Lender's Leads
        try {
            foreach($db->fetchAll("SELECT * FROM `users` WHERE `lender` = :lender;", ["lender" => $lender->getId()]) as $lead) {
                // Use Backend_Lead
                $lead = new Backend_Lead($lead);

                // Re-Assign Lead
                if (!empty($reassign)) {
                    $lead->assign($reassign, $authuser);
                    $assigned[] = $lead;
                // No Lender
                } elseif (is_null($reassign)) {
                    $lead->assignLender(null, $authuser);
                }
            }
        } catch (PDOException $e) {}

        // Send "New Leads" Notification
        if (!empty($reassign) && !empty($assigned)) {
            $reassign->notifyLender($assigned, $authuser);
        }
    }

    try {
        // Delete Lender Image
        $uploads = new Helper_Uploads(DB::get(), Settings::getInstance());
        $uploads->remove($lender->getId(), 'lender');

        // Delete Auth Record
        try {
            $db->prepare("DELETE FROM `" . Auth::$table . "` WHERE `id` = :id;")->execute(["id" => $lender['auth']]);
        } catch (PDOException $e) {
            throw new Exception(__('An error occurred while attempting to delete the selected account.'));
        }

        // Delete Lender
        try {
            $db->prepare("DELETE FROM `lenders` WHERE `id` = :id;")->execute(["id" => $lender->getId()]);
        } catch (PDOException $e){
            throw new Exception(__('An error occurred while trying to delete this Lender.'));
        }

        $success[] = __('%s has successfully been deleted.', Format::htmlspecialchars($lender->getName()));

        // Save Notices
        $authuser->setNotices($success, $errors);

        // Redirect to Manage Lenders
        header('Location: ' . URL_BACKEND . 'lenders/');
        exit;
    } catch (Exception_UploadDeleteError $e) {
        $errors[] = __("Unable to delete lender image.  Please try again.");
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }
}

// Available Lenders (To Re-Assign Leads To)
try {

    $lenders = $db->fetchAll(
        "SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `lenders` WHERE `id` != :id ORDER BY `first_name` ASC",
        ['id' => $lender->info('id')]
    );

} catch (PDOException $e) {}
