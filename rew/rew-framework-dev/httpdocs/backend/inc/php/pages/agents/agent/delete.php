<?php

// Get Database
$db = DB::get();

// Full Page
$body_class = 'full';

// App Settings
$settings = Settings::getInstance();

// Success
$success = array();

// Error
$errors = array();

// Lead ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// Query Lead
$agent = Backend_Agent::load($_GET['id']);

// Throw Missing Agent Exception
if (empty($agent)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingAgentException();
}

// Get Agent Authorization
$agentAuth = new REW\Backend\Auth\Agents\AgentAuth($settings, $authuser, $agent);

// Not authorized to view all leads
if (!$agentAuth->canDeleteAgent()) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to delete this agent')
    );
}

// Count Leads
try {
    $leads = $db->fetch("SELECT COUNT(`id`) AS `total` FROM `users` WHERE `agent` = :agent;", ["agent" => $agent['id']]);
    $leads = $leads['total'];
} catch (PDOException $e) {}

// Close session for AJAX requests
if (!empty($_POST['ajax'])) {
    @session_write_close();
}

// Confirm Reassign
if (!empty($_POST['reassign'])) {
    // Require Leads
    if (!empty($_POST['leads_count'])) {
        // Re-Assign to Agent
        $reassign = Backend_Agent::load($_POST['agent']);

        // Re-Assigned Leads
        $assigned = array();

        // Update Lead's Status
        $status = $_POST['status'];

        // Get Agent's Leads
        try {
            $query = $db->prepare("SELECT `id` FROM `users` WHERE `agent` = :agent LIMIT " . intval($_POST['limit']) . ";");
            $query->execute(['agent' => $_POST['id']]);
            $status_update = $db->prepare("UPDATE `users` SET `status` = :status WHERE `id` = :id;");
            while ($lead = $query->fetch()) {
                // Use Backend_Lead
                $lead = new Backend_Lead($lead, $db);
                if (!empty($status) && $status != $lead['status']) {
                    try {
                        $status_update
                            ->execute([
                                'status' => $status,
                                'id' => $lead['id']
                            ]);
                        // Log Event: Lead's Status has been updated
                        $history_lead = new History_User_Lead($lead->getId(), $db);
                        $history_agent = new History_User_Agent($authuser->info('id'), $db);
                        $event = new History_Event_Update_Status([
                            'new' => ucwords($status),
                            'old' => ucwords($lead['status'])
                        ], [
                            $history_lead,
                            $history_agent
                        ], $db);

                        // Save to DB
                        $event->save();

                        // Update Lead
                        $lead->info('status', $status);
                    } catch (PDOException $e) {}
                }

                // Re-Assign Lead
                if (!empty($reassign)) {
                    $lead->assign($reassign, $authuser);
                    $assigned[] = $lead;
                }
            }
        } catch (PDOException $e) {}

        // Do not need these objects anymore
        unset($lead, $event, $history_lead, $history_agent);

        // Send "New Leads" Notification
        if (!empty($reassign) && !empty($assigned)) {
            $reassign->notifyAgent($assigned, $authuser);
        }
    }
}

// Confirm Delete
if (!empty($_POST['delete'])) {
    $agent = Backend_Agent::load($_POST['id']);

    // Delete Agent Photo
    if (!empty($agent['image'])) {
        @unlink(DIR_AGENT_IMAGES . $agent['image']);
    }

    // Delete Auth Record
    try {
        $db->prepare("DELETE FROM `" . Auth::$table . "` WHERE `id` = :id;")
        ->execute([
            'id' => $agent['auth']
        ]);
        // Delete Agent
        try {
            $db->prepare("DELETE FROM `agents` WHERE `id` = :id;")
            ->execute([
                'id' => $agent['id']
            ]);
            $success[] = __('%s has successfully been deleted.', Format::htmlspecialchars($agent->getName()));

            // If Agent Has A Subdomain, Notify The IDX Team
            if ($agent['cms'] === 'true') {
                if (!\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->sendAgentSubdomainCancellationNotice($agent)) {
                    $errors[] = __('Unable to send cancellation notice to IDX department.  Please contact the IDX department at idx@realestatewebmasters.com');
                }
            }

            // Trigger hook after agent account is removed
            Hooks::hook(Hooks::HOOK_AGENT_DELETE)->run($agent->getRow());

        // Query Error
        } catch (PDOException $e) {
            $errors[] = __('An error occurred while trying to delete this Agent.');
        }

    // Query Error
    } catch (PDOException $e) {
        $errors[] = __('An error occurred while attempting to delete the selected account.');
    }
}

// AJAX Request
if (!empty($_POST['ajax'])) {
    $json = [];
    if (!empty($errors)) {
        $json['errors'] = $errors;
    }
    if (!empty($success)) {
        $json['success'] = $success;
    }
    die(json_encode($json));
}

// Lead Statuses
$statuses = Backend_Lead::$statuses;

// Available Agents (To Re-Assign Leads To)
try {
    $agents = $db->fetchAll("SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `agents` WHERE `id` != :id ORDER BY `first_name` ASC;", ['id' => $agent['id']]);
} catch (PDOException $e) {}

// Re-Assign to Super Admin
$_POST['agent'] = isset($_POST['agent']) ? $_POST['agent'] : 1;
