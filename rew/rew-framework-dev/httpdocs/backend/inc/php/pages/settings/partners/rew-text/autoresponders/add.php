<?php

// Full Page
$body_class = 'full';

// Get Authorization Managers
$textAuth = new REW\Backend\Auth\TextAuth(Settings::getInstance());

// Authorized to text any leads
if (!$textAuth->canTextLeads($authuser)) {
    // Require permission to text own leads
    if (!$textAuth->canTextOwnLeads($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to set up text autoresponders.')
        );
    } else {
        header('Location: edit/');
        exit;
    }
}

try {
    // DB connection
    $db = DB::get();

    // User feedback
    $errors = array();
    $success = array();

    // Maximum # of characters
    $maxlength = 160;

    // Selected agent to add
    $selectedAgent = $_GET['agent_id'];

    // Available agents
    $query = $db->query("SELECT `a`.`id`, CONCAT(`a`.`first_name`, ' ', `a`.`last_name`) AS `name` FROM `agents` `a` LEFT JOIN `twilio_autoresponder` `ta` ON `ta`.`agent_id` = `a`.`id` WHERE `ta`.`id` IS NULL ORDER BY `name` ASC;");
    $agents = $query->fetchAll();

    // Process PSOT request
    if (isset($_GET['submit']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            // Auto-responder data
            $active = !empty($_POST['active']) ? 1 : 0;
            $agent_id = $_POST['agent_id'] ?: null;
            $media = $_POST['media'] ?: null;
            $body = $_POST['body'] ?: null;
            $body = Format::trim($body);

            // Media attachment URL
            if (!empty($media)) {
                $is_media_valid = parse_url($media);
                if (filter_var($media, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) === false) {
                    unset($media);
                    throw new UnexpectedValueException(__('Invalid media attachment.'));
                }
            }

            // If no media, require a message body
            if (empty($media) && empty($body)) {
                throw new UnexpectedValueException(__('You must provide a message to send.'));

            // Check message length
            } else if (strlen($body) > $maxlength) {
                throw new UnexpectedValueException(__('You cannot send a message longer than %s characters.', $maxlength));
            }

            // Must choose agent to assign
            $agent_id = $_POST['agent_id'];
            if (empty($agent_id)) {
                throw new UnexpectedValueException(__('You must choose an agent for this auto-responder.'));
            }
            $query = $db->prepare("SELECT `agent_id` FROM `twilio_autoresponder` WHERE `agent_id` = :agent_id LIMIT 1;");
            $query->execute(array('agent_id' => $agent_id));
            if ($query->fetchColumn() > 0) {
                throw new UnexpectedValueException(__('The selected agent already has an auto-responder.'));
            }

            // Insert auto-responder to database
            $db->prepare("INSERT INTO `twilio_autoresponder` SET "
                . "`agent_id`	= :agent_id,"
                . "`body`		= :body,"
                . "`media`		= :media,"
                . "`active`		= :active,"
                . "`created_ts`	= NOW()"
            . ";")->execute(array(
                'agent_id'  => $agent_id,
                'body'      => $body,
                'media'     => $media,
                'active'    => $active
            ));

            // Success
            $success[] = __('Auto-responder has successfully been saved.');

            // Save notices and redirect page
            $authuser->setNotices($success, $errors);
            header('Location: ../?success');
            exit;

        // Validation error has occurred
        } catch (UnexpectedValueException $e) {
            $errors[] = $e->getMessage();

        // Database error
        } catch (PDOException $e) {
            $errors[] = __('An error occurred while working with the database.');
            //$errors[] = $e->getMessage();

        // Unexpected error
        } catch (Exception $e) {
            $errors[] = __('Something went wrong.');
            //$errors[] = $e->getMessage();
        }
    }

// Database error occurred
} catch (PDOException $e) {
    $errors[] = __('An error occurred while working with the database.');
// 	$errors[] = $e->getMessage();
}
