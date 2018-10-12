<?php

// Create Auth Classes
$toolsAuth = new REW\Backend\Auth\ToolsAuth(Settings::getInstance());
if (!$toolsAuth->canManageCommunities($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage communities.')
    );
}

// Notices
$success = array();
$errors = array();

// DB connection
$db = DB::get();

// AJAX request: update community order
if (!empty($_POST['ajax']) && !empty($_POST['order'])) {
    $order = 0;
    $community_ids = $_GET['items'];
    if (!empty($community_ids) && is_array($community_ids)) {
        $save_order = $db->prepare("UPDATE `featured_communities` SET `order` = :order WHERE `id` = :community_id;");
        foreach ($community_ids as $community_id) {
            $save_order->execute(array(
                'community_id' => $community_id,
                'order' => ++$order
            ));
        }
    }
    // Return JSON response
    header('Content-type: application/json');
    die(json_encode(array()));
}

// Delete community
$delete = $_GET['delete'];
if (!empty($delete)) {
    try {
        if (!$toolsAuth->canDeleteCommunities($authuser)) {
            throw new \REW\Backend\Exceptions\UnauthorizedPageException(
                __('You do not have permission to delete communities.')
            );
        }

        // Find featured community in database
        $find_community = $db->prepare("SELECT * FROM `featured_communities` WHERE `id` = :community_id LIMIT 1;");
        $find_community->execute(array('community_id' => $delete));
        $community = $find_community->fetch();
        if (empty($community)) {
            throw new UnexpectedValueException(
                __('The selected featured community could not be found.')
            );
        }

        // Delete featured community images
        $uploads = new Helper_Uploads(DB::get(), Settings::getInstance());
        $uploads->remove($community['id'], 'community');

        // Delete featured community from database
        $delete_community = $db->prepare("DELETE FROM `featured_communities` WHERE `id` = :community_id;");
        $delete_community->execute(array('community_id' => $community['id']));
        $success[] = __('Featured community has successfully been deleted.');

        // Trigger hook after featured community is removed
        Hooks::hook(Hooks::HOOK_FEATURED_COMMUNITY_DELETE)->run($community);
    } catch (Exception_UploadDeleteError $e) {
        $errors[] = $e->getMessage();

    // Validation error
    } catch (UnexpectedValueException $e) {
        $errors[] = $e->getMessage();
    } catch (PDOException $e) {
        $errors[] = __('Error occurred while deleting featured community.');
        //$errors[] = $e->getMessage();
    }

    // Save notices and redirect to list
    $authuser->setNotices($success, $errors);
    header('Location: ?delete');
    exit;
}

try {
    // Load featured communities
    $manage_communities = array();
    $find_photo = $db->prepare("SELECT `file` FROM `cms_uploads` WHERE `type` = 'community' AND `row` = :community_id ORDER BY `order` ASC LIMIT 1;");
    $find_communities = $db->query("SELECT * FROM `featured_communities` ORDER BY `order` ASC;");
    foreach ($find_communities->fetchAll() as $community) {
        // Community photo
        $find_photo->execute(array('community_id' => $community['id']));
        $community['image'] = $find_photo->fetchColumn();

        // Strip HTML from description
        $community['description'] = Format::stripTags($community['description']);

        // Add community to list
        $manage_communities[] = $community;
    }

// Database error
} catch (PDOException $e) {
    $errors[] = __('Error occurred while loading featured communities.');
    //$errors[] = $e->getMessage();
}
