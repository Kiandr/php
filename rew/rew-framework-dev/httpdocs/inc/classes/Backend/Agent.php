<?php

/**
 * Backend_Agent is a class used for interacting with agent data.
 *
 * Load Agent by ID:
 * <code>
 * $admin = Backend_Agent::load(1);
 * $admin->getId(); // 1
 * </code>
 *
 * Create Agent from Row:
 * <code>
 * $agent = new Backend_Agent ($row); // $row is assoc. array from `agents`
 * $agent->info('first_name'); // $row['first_name']
 * </code>
 *
 * Send Agent Auto-Responder to Lead:
 * <code>
 * $lead = new Backend_Lead (1); // Load Lead from ID
 * $agent = new Backend_Agent ($lead->info('agent')); // Load Lead's Agent
 * $agent->sendAutoResponder($lead); // Send Auto-Responder
 * </code>
 *
 * @package Backend
 */
class Backend_Agent implements ArrayAccess
{

    /**
     * Agent ID
     * @var int $id
     */
    protected $id;

    /**
     * Agent Row
     * @var array $row
     */
    protected $row = array();

    /**
     * Agent Notifications
     * @var Backend_Agent_Notifications
     */
    protected $notifications;

    /**
     * Create Agent from Row
     *
     * <code>
     * <?php
     *
     * // Create Agent from $row
     * $agent = new Backend_Agent ($row);
     *
     * // Display First Name
     * echo $agent->info('first_name'); // $row['first_name']
     *
     * ?>
     * </code>
     *
     * @param array $row Data Row
     */
    public function __construct($row = array())
    {

        // Agent ID
        if (isset($row['id'])) {
            $this->id = $row['id'];
        }

        // Agent Row
        $this->row = $row;
    }

    /**
     * Load Backend_Agent by ID
     *
     * <code>
     * <?php
     *
     * // Load Agent from ID
     * $agent = Backend_Agent::load(1);
     *
     * // Get Agent ID (1)
     * $agent->getId();
     *
     * ?>
     * </code>
     *
     * @param int $id Agent ID
     * @return Backend_Agent|null
     * @throws PDOException
     */
    public static function load($id)
    {

        // Load Agent Row
        $row = self::findById($id);

        // Agent not Found
        if (empty($row)) {
            return null;
        }

        // Return Backend_Agent
        return new self ($row);
    }

    /**
     * Load Agent by ID
     *
     * @param int $id Agent ID
     * @return array DB Row
     * @throws PDOException
     */
    public static function findById($id)
    {

        // App DB
        $db = DB::get('users');

        // Find Agent by ID
        $stmt = $db->prepare("SELECT `a`.*, `auth`.`username`, `auth`.`password`, `auth`.`last_logon`"
            . " FROM `agents` `a` JOIN `" . Auth::$table . "` `auth` ON `a`.`auth` = `auth`.`id`"
            . " WHERE `a`.`id` = :id"
        . ";");

        // Execute Query
        $stmt->execute(array(
            'id' => $id
        ));

        // Return Row
        return $stmt->fetch();
    }

    /**
     * Send Agent's Auto-Responder to Lead
     *
     * <code>
     * <?php
     *
     * // Load Lead from ID
     * $lead = new Backend_Lead (1);
     *
     * // Load Lead's Agent
     * $agent = new Backend_Agent ($lead->info('agent'));
     *
     * // Send Auto-Responder
     * $agent->sendAutoResponder($lead);
     *
     * ?>
     * </code>
     *
     * @param Backend_Lead $lead
     * @return bool Backend_Mailer_AgentAutoResponder::send
     */
    public function sendAutoResponder(Backend_Lead $lead)
    {

        // Mailer: Agent Auto-Responder
        $mailer = new Backend_Mailer_AgentAutoResponder(array(
            'agent' => $this->getRow()
        ));

        // Send from Agent
        $mailer->setSender($this->info('email'), $this->info('first_name') . ' ' . $this->info('last_name'));

        // Send to Lead
        $mailer->setRecipient($lead->info('email'), $lead->info('first_name') . ' ' . $lead->info('last_name'));

        // Send Email
        return $mailer->Send(array(
            'id'         => $lead->getId(),
            'first_name' => $lead->info('first_name'),
            'last_name'  => $lead->info('last_name'),
            'email'      => $lead->info('email')
        ));
    }

    /**
     * Send SMS auto-responder to lead
     * @param Backend_Lead $lead
     * @return boolean|null
     */
    public function sendTextAutoResponder(Backend_Lead $lead)
    {
        if (!empty(Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO'])) {
            try {
                // Send to lead's cell phone #
                $to = $lead->info('phone_cell');
                if (empty($to)) {
                    return null; // No known cell #
                }

                // Load agent's SMS auto-responder
                $autoresponder = $this->getTextAutoResponder();
                if (empty($autoresponder)) {
                    return null; // No active auto-responder
                }

                // Twilio API Client
                $twilio = Partner_Twilio::getInstance();

                // Choose first available number to send from
                $numbers = $twilio->getTwilioNumbers();
                if (empty($numbers)) {
                    return null; // No available phone numbers
                }
                $from = $numbers ? array_pop($numbers)['phone_number'] : false;

                // Format message body
                $body = $autoresponder['body'];
                $body = str_replace('{first_name}', $lead->info('first_name'), $body);
                $body = str_replace('{last_name}', $lead->info('last_name'), $body);

                // Attached media
                $media = $autoresponder['media'];

                // Send agent's SMS auto-responder
                if ($twilio->sendSmsMessage($to, $from, $body, $media)) {
                    // Track text message auto-responder
                    (new History_Event_Text_AutoResponder(array(
                        'to'        => $to,
                        'from'      => $from,
                        'body'      => $body,
                        'media'     => $media
                    ), array(
                        new History_User_Lead($lead->getId()),
                        new History_User_Agent($this->getId())
                    )))->save();

                    // Success
                    return true;
                }

            // Error occurred
            } catch (Exception $e) {
                //$e->getMessage();
                return false;
            }
        }
        return null;
    }

    /**
     * Load twilio SMS auto-responder details (only returns if auto-responder is active)
     * @return array|nul;
     */
    public function getTextAutoResponder()
    {
        $query = DB::get('users')->prepare('SELECT `body`, `media` FROM `twilio_autoresponder` WHERE `active` > 0 AND `agent_id` = :agent_id LIMIT 1;');
        $query->execute(array('agent_id' => $this->getId()));
        return $query->fetch();
    }

    /**
     * Assign Leads to Agent
     * @param array|Backend_Lead $leads Collection of Leads or Single Lead
     * @param Auth $authuser Authorized User
     * @param array $errors If present, error report will be appended to collection
     * @throws InvalidArgumentException If $leads Param is Invalid
     * @return array Collection of Assigned Leads
     */
    public function assign($leads, Auth $authuser = null, &$errors = array())
    {

        // Check Arguments
        if ($leads instanceof Backend_Lead) {
            $leads = array($leads);
        }
        if (!is_array($leads)) {
            throw new InvalidArgumentException;
        }

        // Assigned Leads
        $assigned = array();
        foreach ($leads as $lead) {
            // Require Backend_Lead
            if (!$lead instanceof Backend_Lead) {
                continue;
            }

            // Skip If Already Assigned to Agent
            if ($lead->info('agent') == $this->getId() && ($lead->info('agent') != 1 && $lead->info('status') != 'pending')) {
                continue;
            }

            try {
                // Assign Lead
                $lead->assign($this, $authuser);

                // Set Status to 'accepted' If Assigning to Self, Otherwise set as 'pending'
                $lead->status((!empty($authuser) && $this->getId() == $authuser->info('id') ? 'accepted' : 'pending'), $authuser);

                // Add to Collection
                $assigned[] = $lead;

            // Database Error
            } catch (PDOException $e) {
                $errors[] = $lead->getName() . ' could not be assigned to ' . $this->getName() . '.';
                Log::error($e);
            }
        }

        // Send "New Leads" Notification
        $this->notifyAgent($assigned, $authuser);

        // Return Leads
        return $assigned;
    }

    /**
     * Notify Agent that they have been assigend new Leads
     * @param array $assigned
     * @param Auth $authuser
     * @return void
     */
    public function notifyAgent($assigned, Auth $authuser = null)
    {

        // Send notification email, If leads were assigned by admin or system
        if (!empty($assigned) && (empty($authuser) || $this->getId() != $authuser->info('id'))) {
            // Setup Notification Mailer
            $mailer = new Backend_Mailer_AgentAssigned(array(
                'leads' => $assigned
            ));

            // Check Incoming Notification Settings
            $check = $this->checkIncomingNotifications($mailer, Backend_Agent_Notifications::INCOMING_LEAD_ASSIGNED);

            // Send Email
            if (!empty($check)) {
                $mailer->Send();
            }
        }
    }

    /**
     * Get ID
     *
     * @return int Agent ID
     */
    public function getId()
    {
        return intval($this->id);
    }

    /**
     * Get Agent Name
     * @return string
     */
    public function getName()
    {
        return $this->info('first_name') . ' ' . $this->info('last_name');
    }

    /**
     * Get Agent Email
     * @return string
     */
    public function getEmail()
    {
        return $this->info('email');
    }

    /**
     * Get Agent SMS Email
     * @return null|string
     */
    public function getSmsEmail()
    {
        $sms = $this->info('sms_email');
        return !empty($sms) ? $sms : null;
    }

    /**
     * Get Row
     *
     * @return array Agent Row
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * Get / Set Info
     *
     * @param string $info
     * @param mixed $value
     * @return void|mixed
     */
    public function info($info, $value = null)
    {
        // Get Information
        if (is_string($info) && is_null($value)) {
            return $this->row[$info];
        }
        // Set Information
        if (is_string($info) && !is_null($value)) {
            $this->row[$info] = $value;
        }
    }

    /************************* Agent Notifications *************************/

    /**
     * Get Notification Settings
     * @return Backend_Agent_Notifications
     */
    public function getNotifications()
    {
        if (is_null($this->notifications)) {
            $this->notifications = new Backend_Agent_Notifications($this->row['notifications']);
        }
        return $this->notifications;
    }

    /**
     * Apply Incoming Notification Settings
     * @param Backend_Mailer $mailer Mailer Instance (Referenced, Sets Proper Email/SMS Recipient & Adds CC Recipient)
     * @param string $notice Notice Type
     * @return boolean True if notifications (email/sms) turned on, False if turned off
     */
    public function checkIncomingNotifications(Backend_Mailer &$mailer, $notice)
    {

        // Check Incoming Notification Settings
        $check = $this->getNotifications()->checkIncoming($notice);

        // Notification Turned Off
        if (empty($check['email']) && empty($check['sms'])) {
            return false;
        }

        // Add CC Recipient
        if (!empty($check['cc'])) {
            $mailer->AddCC($check['cc']);
        }

        // Add SMS Recipient
        if (!empty($check['sms']) && $mailer instanceof Backend_Mailer_SMS && $this->getSmsEmail()) {
            $mailer->setSmsRecipient($this->getSmsEmail(), $this->getName());
        }

        // Add Email Recipient
        if (!empty($check['email'])) {
            $mailer->setRecipient($this->getEmail(), $this->getName());
        }

        // True, Send Email
        return true;
    }

    /**
     * Apply Outgoing Notification Settings
     * @param Backend_Mailer $mailer Mailer Instance
     * @param string $notice Notice Type
     * @return Backend_Mailer
     */
    public function checkOutgoingNotifications(Backend_Mailer $mailer, $notice)
    {

        // Check Outgoing Notification Settings
        $check = $this->getNotifications()->checkOutgoing($notice);

        // Add CC Recipient
        if (!empty($check['cc'])) {
            $mailer->AddCC($check['cc']);
        }

        // Add BCC Recipient
        if (!empty($check['bcc'])) {
            $mailer->AddBCC($check['bcc']);
        }

        // Return Mailer
        return $mailer;
    }

    /************************* ArrayAccess *************************/

    /**
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($index)
    {
        return isset($this->row[$index]);
    }

    /**
     * @see ArrayAccess::offsetGet()
     */
    public function &offsetGet($index)
    {
        if ($this->offsetExists($index)) {
            return $this->row[$index];
        }
        return false;
    }

    /**
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($index, $value)
    {
        if ($index) {
            $this->row[$index] = $value;
        } else {
            $this->row[] = $value;
        }
        return true;
    }

    /**
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($index)
    {
        unset($this->row[$index]);
        return true;
    }

    /**
     * Processes a PDO statement returning social media networks
     * @param PDOStatement $stmt
     * @return array
     */
    protected static function processSocialNetworks(PDOStatement $stmt)
    {
        $networks = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // There can be duplicates
            $lcName = strtolower($row['name']);
            if (array_key_exists($lcName, $networks)) {
                continue;
            }
            $row['slug'] = Format::slugify($row['name']);
            $row['form_field'] = 'social_media_' . $row['slug'];
            $networks[$lcName] = $row;
        }

        return array_values($networks);
    }

    /**
     * Get all available social media networks
     * @return array
     */
    public static function getAvailableSocialNetworks()
    {
        $db = DB::get('users');
        $query = "SELECT DISTINCT `name`, NULL AS `url` FROM `" . Settings::getInstance()->TABLES['LM_AGENTS_SOCIAL_NETWORKS'] . "`";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $networks = static::processSocialNetworks($stmt);

        $stmt->closeCursor();

        return $networks;
    }

    /**
     * Get social media networks for this agent
     * @param bool $allAvailable Should we fetch everything possible (even if there is no data)?
     * @return array
     */
    public function getSocialNetworks($allAvailable = false)
    {
        if ($social_networks = $this->info('social_networks')) {
            return $social_networks;
        }

        $db = DB::get('users');
        $query = "SELECT `name`, `url` FROM `" . Settings::getInstance()->TABLES['LM_AGENTS_SOCIAL_NETWORKS'] . "` WHERE `agent_id` = :agent_id AND `url` IS NOT NULL";

        if ($allAvailable) {
            $query .= " UNION SELECT DISTINCT `name`, NULL AS `url` FROM `" . Settings::getInstance()->TABLES['LM_AGENTS_SOCIAL_NETWORKS'] . "`";
        }
        $stmt = $db->prepare($query);

        $stmt->execute(array('agent_id' => $this->info('id')));
        $networks = $this->processSocialNetworks($stmt);
        $stmt->closeCursor();

        $this->info('social_networks', $networks);

        return $networks;
    }

    /**
     * Saves social media networks for this agent
     * @param array $data The request data to use
     * @throws PDOException on DB error
     */
    public function setSocialNetworks(array $data)
    {
        $db = DB::get('users');

        $db->beginTransaction();

        $agentId = $this->info('id');
        $agentBind = array('agent_id' => $agentId);

        if ($agentId != 1) {
            // Don't delete for super admin. The idea is that super admin should always have every network
            // (even if it doesn't have a value) in order to ensure the whole list stays in the DB.
            $query = "DELETE FROM `" . Settings::getInstance()->TABLES['LM_AGENTS_SOCIAL_NETWORKS'] . "` WHERE `agent_id` = :agent_id";
            $stmt = $db->prepare($query);

            try {
                $stmt->execute($agentBind);
            } catch (PDOException $ex) {
                $db->rollBack();
                throw $ex;
            }
        }

        $query = "INSERT INTO `" . Settings::getInstance()->TABLES['LM_AGENTS_SOCIAL_NETWORKS'] . "` SET `agent_id` = :agent_id, `name` = :name, `url` = :url"
            . " ON DUPLICATE KEY UPDATE `url` = VALUES(`url`)";
        $stmt = $db->prepare($query);
        foreach ($this->getSocialNetworks(true) as $network) {
            if (!empty($data[$network['form_field']]) || $agentId == 1) {
                try {
                    $stmt->execute($agentBind + array('url' => $data[$network['form_field']] ?: null, 'name' => $network['name']));
                } catch (PDOException $ex) {
                    $db->rollBack();
                    throw $ex;
                }
            }
        }
        $stmt->closeCursor();

        $db->commit();

        // Clear cache so it will be reloaded, if it's needed again
        $this->info('social_networks', null);
    }

    /**
     * Determines Whether This Agent Has Specified Permission
     * @param $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        return (boolean) (
            ($permission & $this->info('permissions_admin'))
                ||
            ($permission & $this->info('permissions_user'))
        );
    }
}
