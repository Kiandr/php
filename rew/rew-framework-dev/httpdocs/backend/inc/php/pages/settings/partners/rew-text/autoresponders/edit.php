<?php

// Full Page
$body_class = 'full';

try {
    // DB connection
    $db = DB::get();

    // User feedback
    $errors = array();
    $success = array();

    // Selected agent to edit
    $selectedAgent = $_GET['id'];

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
            $agent_id = $authuser->info('id');
            $query = $db->prepare("SELECT * FROM `twilio_autoresponder` WHERE `agent_id` = :agent_id LIMIT 1;");
            $query->execute(array('agent_id' => $agent_id));
            $autoresponder = $query->fetch();

            // Create temp record
            if (empty($autoresponder)) {
                $autoresponder = array('agent_id' => $agent_id);
            }
        }
    } else {
        $query = $db->prepare("SELECT * FROM `twilio_autoresponder` WHERE `id` = :id LIMIT 1;");
        $query->execute(array('id' => $selectedAgent));
        $autoresponder = $query->fetch();
    }

    /* Throw Missing Autoresponder Exception */
    if (empty($autoresponder)) {
        throw new \REW\Backend\Exceptions\MissingId\MissingAutoresponderException();
    }

    // Maximum # of characters
    $maxlength = 160;

    // Can text any lead
    if ($textAuth->canTextLeads($authuser)) {
        // Available agents
        $query = $db->prepare("SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `agents` WHERE `id` = :agent_id;");
        $query->execute(array('agent_id' => $autoresponder['agent_id']));
        $agent = $query->fetch();
    }

    // Process PSOT request
    if (isset($_GET['submit']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            // Auto-responder data
            $active = !empty($_POST['active']) ? 1 : 0;
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

            // Insert new auto-responder
            if (empty($autoresponder['id'])) {
                // Update auto-responder record
                $db->prepare("INSERT INTO `twilio_autoresponder` SET "
                    . "`agent_id`	= :agent_id,"
                    . "`body`		= :body,"
                    . "`media`		= :media,"
                    . "`active`		= :active,"
                    . "`created_ts`	= NOW()"
                . ";")->execute(array(
                    'agent_id'  => $authuser->info('id'),
                    'body'      => $body,
                    'media'     => $media,
                    'active'    => $active
                ));
            } else {
                // Update auto-responder record
                $db->prepare("UPDATE `twilio_autoresponder` SET "
                    . "`body`		= :body,"
                    . "`media`		= :media,"
                    . "`active`		= :active,"
                    . "`updated_ts`	= NOW()"
                    ." WHERE `id` = :id"
                . ";")->execute(array(
                    'id'        => $autoresponder['id'],
                    'body'      => $body,
                    'media'     => $media,
                    'active'    => $active
                ));
            }

            // Success
            $success[] = __('Auto-responder has successfully been saved.');

            // Save notices and redirect page
            $authuser->setNotices($success, $errors);
            header('Location: ?id=' . $autoresponder['id'] . '&success');
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
    $errors[] = $e->getMessage();
}
