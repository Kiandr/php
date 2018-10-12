<?php

/* @global Auth $authuser */
if (!Skin::hasFeature(Skin::REW_DEVELOPMENTS)) {
    throw new \REW\Backend\Exceptions\PageNotFoundException();
}

// Full width page
$body_class = 'full';

// Get Authorization Managers
$developmentsAuth = new REW\Backend\Auth\DevelopmentsAuth(Settings::getInstance());

// Authorized to Edit all Leads
if (!$developmentsAuth->canManageDevelopments($authuser)) {
    // Require permission to edit self
    if (!$developmentsAuth->canManageOwnDevelopments($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            'You do not have permission to edit developments.'
        );
    } else {
        // Restrict to owned
        $agent_id = $authuser->info('id');
    }
}

try {
    // Allowed to delete
    $can_delete = $developmentsAuth->canDeleteDevelopments($authuser);

    // Notices
    $success = array();
    $errors = array();

    // DB connection
    $db = DB::get();

    // Update order for developments
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order'])) {
        try {
            $json = [];
            if (!empty($_POST['order']) && is_array($_POST['order'])) {
                $count = 0;
                $order = $db->prepare("UPDATE `developments` SET `order` = ? WHERE `id` = ? LIMIT 1;");
                $json['order'] = [];
                foreach ($_POST['order'] as $id) {
                    if (is_numeric($id)) {
                        $json['order'][] = (int) $id;
                        $order->execute([++$count, $id]);
                    }
                }
            }
        // Database error occurred
        } catch (\PDOException $e) {
            $json['error'] = 'Error occurred while updating developments.';
            //$json['error'] = $e->getMessage();
        }
        // Return JSON response
        header('Content-type: application/json');
        echo json_encode($json);
        exit;
    }

    // Manage developments
    $developments = [];

    // Prepare query to find primary photo
    $find_photo = $db->prepare(sprintf(
        "SELECT `file` FROM `%s` WHERE `type` = 'development' AND `row` = ? ORDER BY `order` ASC LIMIT 1;",
        Settings::getInstance()->TABLES['UPLOADS']
    ));

    // Fetch available developments from database
    $query = $db->query(
        "SELECT `id`, `title`, `subtitle`, `description`, `is_enabled`, `is_featured` FROM `developments`"
        . ($agent_id ? sprintf(' WHERE `agent_id` = %d', $agent_id) : '')
        . " ORDER BY `order` ASC"
        . ";"
    );
    foreach ($query->fetchAll() as $development) {
        // Fetch primary photo
        $find_photo->execute([$development['id']]);
        $development['image'] = $find_photo->fetchColumn();

        // Add to list of developments
        $developments[] = $development;
    }

// Database error
} catch (\PDOException $e) {
    $errors[] = 'Error occurred while loading developments.';
    //$errors[] = $e->getMessage();
}
