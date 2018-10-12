<?php

/**
 * Backend_Lead is a class used for managing leads. Eg: Change Status, Assign to Agent, Assign to Group, Remove from Group, Delete Lead
 *
 * Load Lead by ID:
 * <code>
 * $lead = Backend_Lead::load(1);
 * $lead->getId(); // 1
 * </code>
 *
 * Create Lead from Row:
 * <code>
 * $lead = new Backend_Lead ($row); // $row is assoc. array from `users`
 * $lead->info('first_name'); // $row['first_name']
 * </code>
 *
 * Accept Lead:
 * <code>
 * $lead->status('accepted'); // Accept Lead
 * </code>
 *
 * Reject Lead:
 * <code>
 * $lead->info('rejectwhy', 'Reason for Rejection'); // Set Reason
 * $lead->status('rejected'); // Reject Lead
 * </code>
 *
 * Assign Lead to Agent:
 * <code>
 * $agent = Backend_Agent::load(1); // Load Agent by ID
 * $lead->assign($agent); // Assign to Agent
 * </code>
 *
 * Assign Lead to Group:
 * <code>
 * $group = array('id' => 1, 'name' => 'Group Name'); // Group Row from DB
 * $lead->assignGroup($group); // Assign to Group
 * </code>
 *
 * Remove Lead from Group:
 * <code>
 * $group = array('id' => 1, 'name' => 'Group Name'); // Group Row from DB
 * $lead->removeGroup($group); // Remove from Group
 * </code>
 *
 * Delete Lead:
 * <code>
 * // Delete Lead
 * $lead->delete($authuser); // $authuser is required
 * </code>
 *
 * @package Backend
 */
class Backend_Lead implements ArrayAccess
{

    /**
     * Lead ID
     * @var int $id
     */
    protected $id;

    /**
     * Lead Row
     * @var array $row
     */
    protected $row = array();

    /**
     * Lead Database
     * @var DB
     */
    protected $db;

    /**
     * Lead Statuses
     * @var array
     */
    public static $statuses = array(
        'unassigned' => 'Unassigned',
        'pending'   => 'Pending',
        'accepted'  => 'Accepted',
        'rejected'  => 'Rejected',
        'closed'    => 'Closed'
    );

    /**
     * Phone Status Types
     * @var array
     */
    public static $phone_status = array(
        '1' => 'Good Number Talked',
        '2' => 'Good Personal VM',
        '3' => 'Good Family VM',
        '4' => 'First Name VM',
        '5' => 'Generic VM',
        '6' => 'No Answer',
        '7' => 'Wrong / Bad Number'
    );

    /**
     * Create Lead from Row
     *
     * <code>
     * <?php
     *
     * // Create Lead from $row
     * $lead = new Backend_Lead ($row);
     *
     * // Display First Name
     * echo $lead->info('first_name'); // $row['first_name']
     *
     * ?>
     * </code>
     *
     * @param array $row Lead Row
     * @param DB $db Lead DB
     */
    public function __construct($row = array(), DB &$db = null)
    {

        // Lead ID
        if (isset($row['id'])) {
            $this->id = $row['id'];
        }

        // Lead Row
        $this->row = $row;

        // Lead DB
        $this->db = !is_null($db) ? $db : DB::get('users');
    }

    /**
     * Load Lead by ID
     *
     * <code>
     * <?php
     *
     * // Load Lead from ID
     * $lead = Backend_Lead::load(1);
     *
     * // Get Agent ID (1)
     * $lead->getId();
     *
     * ?>
     * </code>
     *
     * @param int $id Lead ID
     * @return Backend_Lead
     * @throws PDOException
     */
    public function load($id)
    {

        // App DB
        $db = DB::get('users');

        // Fetch DB Row
        $row = $db->getCollection('users')->getRow($id);

        // Return Instance
        return new self ($row, $db);
    }

    /**
     * Update Lead Status
     *
     * Accept Lead:
     * <code>
     * <?php
     *
     * // Load Lead by ID
     * $lead = Backend_Lead::load(1);
     *
     * // Accept Lead
     * $lead->status('accepted')
     *
     * ?>
     * </code>
     *
     * Reject Lead:
     * <code>
     * <?php
     *
     * // Set Reason
     * $lead->info('rejectwhy', 'Reason for Rejection');
     *
     * // Reject Lead
     * $lead->status('rejected');
     *
     * ?>
     * </code>
     *
     * @param string $status Status
     * @param Auth $authuser
     * @return void
     * @throws PDOException
     */
    public function status($status, Auth $authuser = null, $save_history = true)
    {

        // Ignore If Already Set
        if ($this->info('status') == $status) {
            return;
        }

        // Update Status
        $this->db->query("UPDATE `users` SET `status` = '" . $status . "' WHERE `id` = '" . $this->getId() . "';");

        // Log Event: Set Lead's Status
        if($save_history) {
            $event = new History_Event_Update_Status(array(
                'new' => ucwords($status),
                'old' => ucwords($this->info('status'))
            ), array(
                new History_User_Lead($this->getId()),
                (!empty($authuser) ? $authuser->getHistoryUser() : null)
            ));

            // Save to DB
            $event->save($this->db);
        }

        // Check Status
        switch ($status) {
            // Accept Lead
            case 'accepted':
                // Get Assigned Agent
                $agent = Backend_Agent::load($this->info('agent'));

                // Agent Auto-Responder is Active, Send It
                if ($agent->info('ar_active') == 'Y') {
                    $agent->sendAutoResponder($this);
                }

                // Agent SMS Auto-Responder
                $agent->sendTextAutoResponder($this);

                // Fire Lead Accepted Hook
                Hooks::hook(Hooks::HOOK_AGENT_LEAD_ACCEPT)->run($this->getRow());

                break;

            // Unassign Lead
            case 'unassigned':
                // Force-Assign unassigned leads to Super Admin
                $admin = Backend_Agent::load(1);
                $this->assign($admin);

                break;

            // Reject Lead
            case 'rejected':
                // Track Rejection
                $this->db->query("REPLACE INTO `users_rejected` SET `user_id` = '" . $this->getId() . "', `agent_id` = '" . $authuser->info('id') . "', `why` = " . $this->db->quote($this->info('rejectwhy')) . ";");

                if($save_history) {
                    // Log Event: Agent Rejected Lead
                    $event = new History_Event_Update_Rejected(array(
                        'reason' => $this->info('rejectwhy')
                    ), array(
                        new History_User_Lead($this->getId()),
                        $authuser->getHistoryUser()
                    ));

                    // Save to DB
                    $event->save($this->db);
                }

                // Load Super Admin
                $admin = Backend_Agent::load(1);

                // Assign to Super Admin
                $this->assign($admin);

                // Rejected by Agent, Notify Admin
                if ($admin->info('id') != $authuser->info('id')) {
                    // Setup Mailer
                    $mailer = new Backend_Mailer_LeadRejected(array(
                        'agent' => array('id' => $authuser->info('id'), 'first_name' => $authuser->info('first_name'), 'last_name' => $authuser->info('last_name'), 'email' => $authuser->info('email')),
                        'lead'  => $this->getRow()
                    ));

                    // Add Recipient
                    $mailer->setRecipient($admin->info('email'), $admin->info('first_name') . ' ' . $admin->info('last_name'));

                    // Send Email
                    $mailer->Send();
                }

                // Fire Lead Accepted Hook
                Hooks::hook(Hooks::HOOK_AGENT_LEAD_REJECT)->run($this->getRow());

                break;
        }

        // Update Instance
        $this->info('status', $status);
    }

    /**
     * Assign Lead to Agent/Lender
     * @param Backend_Agent|Backend_Lender $assign
     * @param Auth $authuser
     * @uses self::assignAgent
     * @uses self::assignLender
     * @return void
     */
    public function assign($assign, Auth $authuser = null, $save_history = true)
    {
        // Assign to Agent
        if ($assign instanceof Backend_Agent) {
            $this->assignAgent($assign, $authuser, $save_history);

            // Make sure assigned leads don't retain unassigned status
            if ($this->info('status') === 'unassigned' && $assign->getId() > 1) {
                $this->status('pending', $authuser);
            }

            // Assign to Lender
        } else if ($assign instanceof Backend_Lender) {
            $this->assignLender($assign, $authuser);
        }
    }

    /**
     * Assign Lead to Agent
     *
     * <code>
     * <?php
     *
     * // Load Agent by ID
     * $agent = Backend_Agent::load(1);
     *
     * // Load Lead by ID
     * $lead = Backend_Lead::load(1);
     *
     * // Assign to Agent
     * $lead->assign($agent);
     *
     * ?>
     * </code>
     *
     * @param Backend_Agent $agent Agent to Assign
     * @param Auth $authuser
     * @return void
     * @throws PDOException
     */
    public function assignAgent(Backend_Agent $agent, Auth $authuser = null, $save_history = true)
    {

        // Ignore If Already Assigned
        if ($this->info('agent') == $agent->getId()) {
            return;
        }

        // Assign Lead to Agent, Set Timestamp
        $this->db->query("UPDATE `users` SET `agent` = '" . $agent->getId() . "', `timestamp_assigned` = NOW() WHERE `id` = '" . $this->getId() . "';");

        // Make sure assigned leads don't retain unassigned status
        if ($this->info('status') === 'unassigned' && $agent->getId() > 1) {
            $this->status('pending', $authuser);
        }

        // Remove From Existing Groups that do not belong to the Super Admin or Assigned Agent
        $rows = $this->db->fetchAll("SELECT t1.* FROM `users_groups` t1 LEFT JOIN `groups` t2 ON t1.group_id = t2.id WHERE `t2`.`agent_id` != 1 AND `t2`.`agent_id` != '" . $agent->getId() . "' AND `t2`.`agent_id` IS NOT NULL AND `t1`.`user_id` = '" . $this->getId() . "';");
        foreach ($rows as $row) {
            $this->db->query("DELETE FROM `users_groups` WHERE `user_id` = '" . $row['user_id'] . "' AND `group_id` = '" . $row['group_id'] . "';");
        }

        // Un-Assigned from Agent
        if ($this->info('agent') > 0) {
            if($save_history) {
                // Log Event: Un-Assign Lead from Agent
                $event = new History_Event_Update_UnAssign(array(
                    'agent_id' => $this->info('agent')
                ), array(
                    new History_User_Lead($this->getId()),
                    new History_User_Agent($this->info('agent')),
                    (!empty($authuser) && $authuser->info('id') != $agent->getId() ? $authuser->getHistoryUser() : null)
                ));

                // Save to DB
                $event->save($this->db);
            }

            // Fire Lead Reassigned Hook
            Hooks::hook(Hooks::HOOK_AGENT_LEAD_REASSIGN)->run($this->getRow());
        }

        if($save_history) {
            // Log Event: Assign Lead to Agent
            $event = new History_Event_Update_Assign(array(
                'agent_id' => $agent->getId()
            ), array(
                new History_User_Lead($this->getId()),
                new History_User_Agent($agent->getId()),
                (!empty($authuser) && $authuser->info('id') != $agent->getId() ? $authuser->getHistoryUser() : null)
            ));

            // Save to DB
            $event->save($this->db);
        }
        // Update Instance
        $this->info('agent', $agent->getId());
    }

    /**
     * Assign Lead to Lender
     *
     * @param Backend_Lender $lender Lender to Assign
     * @param Auth $authuser
     * @return void
     * @throws PDOException
     */
    public function assignLender(Backend_Lender $lender = null, Auth $authuser = null)
    {

        // Ignore If Already Assigned
        if (!empty($lender) && $this->info('lender') == $lender->getId()) {
            return;
        }

        // Un-Assign Lender
        if ($this->info('lender') > 0) {
            // Un-Assign Lender from Lead
            $this->db->query("UPDATE `users` SET `lender` = NULL WHERE `id` = '" . $this->getId() . "';");

            // Update Instance
            $this->info('lender', null);

            // Log Event: Un-Assign Lead from Lender
            $event = new History_Event_Update_UnAssign(array(), array(
                new History_User_Lead($this->getId()),
                new History_User_Lender($this->info('lender')),
                (!empty($authuser) ? $authuser->getHistoryUser() : null)
            ));

            // Save to DB
            $event->save($this->db);
        }

        // Assign Lender
        if (!empty($lender)) {
            // Assign Lead to Lender
            $this->db->query("UPDATE `users` SET `lender` = '" . $lender->getId() . "' WHERE `id` = '" . $this->getId() . "';");

            // Update Instance
            $this->info('lender', $lender->getId());

            // Log Event: Assign Lead to Lender
            $event = new History_Event_Update_Assign(array(), array(
                new History_User_Lead($this->getId()),
                new History_User_Lender($lender->getId()),
                (!empty($authuser) ? $authuser->getHistoryUser() : null)
            ));

            // Save to DB
            $event->save($this->db);
        }
    }

    /**
     * Assign Lead to Group
     *
     * <code>
     * <?php
     *
     * // Group Row from DB
     * $group = array('id' => 1, 'name' => 'Group Name');
     *
     * // Load Lead by ID
     * $lead = Backend_Lead::load(1);
     *
     * // Assign to Group
     * $lead->assignGroup($group);
     *
     * ?>
     * </code>
     *
     * @param array $group Group Row
     * @param Auth $authuser
     * @return void
     * @throws PDOException
     */
    public function assignGroup($group, Auth $authuser = null)
    {

        // Check if already assigned to group
        $query = $this->db->prepare("SELECT `user_id` FROM `users_groups` WHERE `group_id` = :group_id AND `user_id` = :user_id;");
        $query->execute(array('group_id' => $group['id'], 'user_id' => $this->getId()));
        $check = $query->fetchColumn();

        // Already in group - do not continue
        if (!empty($check)) {
            return;
        }

        // Add Lead to Group
        $query = $this->db->prepare("INSERT IGNORE INTO `users_groups` SET `group_id` = :group_id, `user_id` = :user_id;");
        $query->execute(array('group_id' => $group['id'], 'user_id' => $this->getId()));

        // Log Event: Added to Group
        $event = new History_Event_Update_GroupAdd(array(
            'group' => $group['name']
        ), array(
            new History_User_Lead($this->getId()),
            (!empty($authuser) ? $authuser->getHistoryUser() : null)
        ));

        // Save to DB
        $event->save($this->db);
    }

    /**
     * Remove Lead from Group
     *
     * <code>
     * <?php
     *
     * // Group Row from DB
     * $group = array('id' => 1, 'name' => 'Group Name');
     *
     * // Load Lead by ID
     * $lead = Backend_Lead::load(1);
     *
     * // Assign to Group
     * $lead->removeGroup($group);
     *
     * ?>
     * </code>
     *
     * @param array $group Group Row
     * @param Auth $authuser
     * @return void
     * @throws PDOException
     */
    public function removeGroup($group, Auth $authuser = null)
    {

        // Remove Lead from Group
        $this->db->query("DELETE FROM `users_groups` WHERE `group_id` = '" . $group['id'] . "' AND `user_id` = '" . $this->getId() . "';");

        // Log Event: Removed to Group
        $event = new History_Event_Update_GroupRemove(array(
            'group' => $group['name']
        ), array(
            new History_User_Lead($this->getId()),
            (!empty($authuser) ? $authuser->getHistoryUser() : null)
        ));

        // Save to DB
        $event->save($this->db);
    }

    /**
     * Sync Lead's Partner Groups
     *
     * @param Auth $authuser
     * @return void
     */
    public function syncPartners (Auth $authuser) {

        // Current lead Owner
        $agent = Backend_Agent::load($this->info('agent'));

        // Current lead groups
        $groups = Backend_Group::getGroups($errors, Backend_Group::LEAD, $this->getID());

        // Sync all partners
        Hooks::hook(Hooks::HOOK_LEAD_PARTNER_SYNC)->run($this, $agent, $groups);

    }

    /**
     * Delete Lead
     *
     * <code>
     * <?php
     *
     * // Load Lead by ID
     * $lead = Backend_Lead::load(1);
     *
     * // Delete Lead
     * $lead->delete($authuser); // $authuser is required
     *
     * ?>
     * </code>
     *
     * @param Auth $authuser Required
     * @return void
     * @throws PDOException
     */
    public function delete(Auth $authuser)
    {

        // Delete Row
        $this->db->query("DELETE FROM `users` WHERE `id` = '" . $this->getId() . "';");

        // Remove unnecessary fields
        $row = $this->getRow();
        $row = array(
            'id'         => $row['id'],
            'first_name' => $row['first_name'],
            'last_name'  => $row['last_name']
        );

        // Delete Lead Photo
        if (!empty($row['image'])) {
            @unlink(DIR_LEAD_IMAGES . $row['image']);
        }

        // Log Event: Agent Deleted Lead Row
        $event = new History_Event_Delete_Lead(array(
            'row' => $row
        ), array(
            $authuser->getHistoryUser()
        ));

        // Save to DB
        $event->save($this->db);
    }

    /**
     * Get ID
     *
     * @return int Lead ID
     */
    public function getId()
    {
        return intval($this->id);
    }

    /**
     * Get Lead's Name
     * @return string
     */
    public function getName()
    {
        $name = $this->info('first_name') . ' ' . $this->info('last_name');
        return trim($name);
    }

    /**
     * Return name or (if empty) return email
     * @return string
     */
    public function getNameOrEmail()
    {
        return $this->getName() ?: $this->getEmail();
    }

    /**
     * Return name or (if empty) substitute
     * @return string
     */
    public function getNameOrSubstitute()
    {
        $name = $this->getName();
        if (empty($name)) {
            if (preg_match('/guaranteedsale.com$/', $this->info('referer'))) {
                $name = 'Guaranteed Sold';
            } else {
                $query = "SELECT COUNT(`id`) AS `total` FROM `users_forms` WHERE `user_id` = '" . $this->id . "'"
                    ." AND `form` IN ('Guaranteed Sold CTA','Guaranteed Sold Form');";
                $total = $this->db->fetch($query);
                if ($total['total'] > 0) {
                    $name = 'Guaranteed Sold';
                }
            }
        }
        return $name?:'No Name Provided';
    }

    /**
     * Get Lead's Email
     * @return string
     */
    public function getEmail()
    {
        return $this->info('email');
    }

    /**
     * Get Row
     *
     * @return array Lead Row
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * Get an API Application row for a provided app ID
     * @param int $app_id Application ID
     * @param string $field
     * @param DB $db Lead DB
     * @return array|NULL
     */
    public static function apiSource($app_id, $field = null, $db = null)
    {

        // Require  ID
        if (empty($app_id)) {
            return null;
        }

        // Cached results
        static $api_source;
        $api_source = is_array($api_source) ? $api_source : array();

        // Check cache
        if (isset($api_source[$app_id])) {
            return !empty($field) ? $api_source[$app_id][$field] : $api_source[$app_id];
        }

        // Database
        $db = !is_null($db) ? $db : DB::get('users');

        // Fetch API Application
        $api_source[$app_id] = $db->{'api_applications'}->getRow($app_id);
        return !empty($field) ? $api_source[$app_id][$field] : $api_source[$app_id];
    }

    /**
     * Get the lead's API source application row, if available
     * @param string $field
     * @return array|NULL
     */
    public function getAPISource($field = null)
    {
        return self::apiSource($this->row['source_app_id'], $field, $this->db);
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

    /**
     * Save Lead (Update Existing or Create New)
     *
     * @param DB $db
     * @return bool True on success, False on failure
     * @throws Exception_ValidationError If Missing Required Data
     */
    public function save(DB $db = null)
    {

        // Lead Database
        $db = is_null($db) ? $this->db : $db;

        // Lead Collection
        $leads = $db->getCollection('users');

        // Lead Row
        $row = $this->getRow();

        // Unset Row Columns that are not Actual DB Columns (or else update/insert will break)
        unset($row['scores'], $row['scoring']);

        // Require Email
        if (empty($row['email'])) {
            throw new Exception_ValidationError('Missing Email Address');
        }

        // Check Existing Lead
        $exists = $leads->count(array('$eq' => array('email' => $row['email']), '$neq' => array('id' => $row['id'])));
        if ($exists > 0) {
            throw new Exception_ValidationError('Duplicate Email Address');
        }

        // Existing Lead
        if (!empty($this->id)) {
            // Update Existing Row
            return $leads->update($row, array('$eq' => array('id' => $this->id)));
        } else {
            // Auto-Assign Settings
            $settings = $db->fetch("SELECT `auto_assign`, `auto_assign_lenders` FROM `default_info` WHERE `agent` = 1;");

            // Agent is Assigned, Don't Auto-Rotate
            if (!empty($row['agent'])) {
                $row['auto_rotate'] = 'false';
            } else {
                // Assign Super Admin
                $row['agent'] = 1;

                // Agent Sub-Domain
                if (Settings::getInstance()->SETTINGS['agent'] !== 1) {
                    $row['agent'] = Settings::getInstance()->SETTINGS['agent'];
                }

                // Team Sub-Domain
                if (Settings::getInstance()->SETTINGS['team'] && !empty(Settings::getInstance()->MODULES['REW_TEAMS'])) {
                    $team = Backend_Team::load(Settings::getInstance()->SETTINGS['team']);
                    $team_agents = $team->getAgents([Backend_Team::PERM_ASSIGN]);
                    if (!empty($team_agents)) {
                        $team_query = " AND `id` IN (".implode(',', array_map([$db, 'quote'], $team_agents)).")";
                    }
                }

                // Lead Auto-Assign
                if ($settings['auto_assign'] == 'true' && $row['agent'] <= 1) {
                    // Auto-assign agent
                    $auto_assign_agent = null;

                    // Lead came from API source
                    if (!empty($row['source_app_id']) && !empty(Settings::getInstance()->MODULES['REW_CRM_API'])) {
                        // Grab agent from matching source
                        $auto_assign_agent = $db->fetch("SELECT `id` FROM `agents` WHERE `auto_assign_admin` = 'true' AND `auto_assign_agent` = 'true' AND `auto_assign_app_id` = '" . $row['source_app_id'] . "'" . $team_query . " ORDER BY `auto_assign_time` ASC LIMIT 1;");

                        // All agents fallback
                        if (empty($auto_assign_agent)) {
                            $auto_assign_agent = $db->fetch("SELECT `id` FROM `agents` WHERE `auto_assign_admin` = 'true' AND `auto_assign_agent` = 'true' AND `auto_assign_app_id`" . $team_query . " IS NULL ORDER BY `auto_assign_time` ASC LIMIT 1;");
                        }
                    } else { // Local lead
                        $auto_assign_agent = $db->fetch("SELECT `id` FROM `agents` WHERE `auto_assign_admin` = 'true' AND `auto_assign_agent` = 'true' AND `auto_assign_app_id` IS NULL" . $team_query . " ORDER BY `auto_assign_time` ASC LIMIT 1;");
                    }

                    // Update assign timestamp
                    if (!empty($auto_assign_agent)) {
                        $db->query("UPDATE `agents` SET `auto_assign_time` = NOW() WHERE `id` = '" . $auto_assign_agent['id'] . "';");
                        $row['agent'] = $auto_assign_agent['id'];
                    }
                }
            }

            // Lender Auto-Assignment
            if (!empty(Settings::getInstance()->MODULES['REW_LENDERS_MODULE'])) {
                if ($settings['auto_assign_lenders'] == 'true' && empty($row['lender'])) {
                    $lender = $db->fetch("SELECT `id` FROM `lenders` WHERE `auto_assign_admin` = 'true' AND `auto_assign_optin` = 'true' ORDER BY `auto_assign_time` ASC LIMIT 1;");
                    if (!empty($lender)) {
                        $db->query("UPDATE `lenders` SET `auto_assign_time` = NOW() WHERE `id` = '" . $lender['id'] . "';");
                        $row['lender'] = $lender['id'];
                    }
                }
            }

            // Timestamp Data
            $row = array_merge($row, array(
                "`timestamp_assigned` = NOW()",
                "`timestamp_active`   = NOW()",
                "`timestamp`          = NOW()"
            ));

            // Insert Row
            $this->row = $leads->insert($row);

            // Lead ID
            $this->id = $this->row['id'];

            // Log Event: New Lead Created
            $event = new History_Event_Create_Lead(array(
                'lead_id' => $this->id
            ), array (
                new History_User_Lead($this->id)
            ));

            // Save Event
            $event->save($db);

            // Assign Lead to Agent
            $this->info('agent', 0);
            $this->assign(Backend_Agent::load($row['agent']));

            // Assign Lead to Lender
            if (!empty(Settings::getInstance()->MODULES['REW_LENDERS_MODULE']) && !empty($row['lender'])) {
                $lender = Backend_Lender::load($row['lender']);
                if (!empty($lender)) {
                    $this->info('lender', 0);
                    $this->assign($lender);
                }
            }

            // Update Lead Score
            $this->updateScore();

            // Success
            return true;
        }
    }

    /**
     * Update Lead Score
     *
     * @return int Lead Score
     */
    public function updateScore()
    {

        // Score Settings
        $settings = $this->db->fetch("SELECT `scoring` FROM `default_info` WHERE `agent` = 1;");

        // Require Array
        $settings['scoring'] = is_array($settings['scoring']) ? $settings['scoring'] : unserialize($settings['scoring']);

        // Score System
        $scores = array(
            'manual'    => array('title' => 'Manual Lead',            'max' => 1,  'value' => isset($settings['scoring']['manual'])    ? $settings['scoring']['manual']    : 5),
            'calls'     => array('title' => 'Phone Lead',             'max' => 1,  'value' => isset($settings['scoring']['calls'])     ? $settings['scoring']['calls']     : 5),
            'visits'    => array('title' => '# of Visits',            'max' => 10, 'value' => isset($settings['scoring']['visits'])    ? $settings['scoring']['visits']    : 5),
            'listings'  => array('title' => '# of Viewed Listings',   'max' => 10, 'value' => isset($settings['scoring']['listings'])  ? $settings['scoring']['listings']  : 5),
            'favorites' => array('title' => '# of ' . Locale::spell('Favorite') . ' Listings', 'max' => 5,  'value' => isset($settings['scoring']['favorites']) ? $settings['scoring']['favorites'] : 5),
            'searches'  => array('title' => '# of Saved Searches',    'max' => 5,  'value' => isset($settings['scoring']['searches'])  ? $settings['scoring']['searches']  : 5),
            'inquiries' => array('title' => '# of Inquiries',         'max' => 3,  'value' => isset($settings['scoring']['inquiries']) ? $settings['scoring']['inquiries'] : 5),
            'price'     => array('title' => 'Average Price',          'max' => 1,  'value' => isset($settings['scoring']['price'])     ? $settings['scoring']['price']     : 5),
        );

        // Max Score
        $total = 0;
        foreach ($scores as $key => $score) {
            $total += $score['value'];
        }

        // Empty Score Settings - Everyone gets 0!
        if (empty($total)) {
            $score = 0;
        } else {
            // Value / Max
            $manual        = ($this->row['manual'] === 'yes') ? 1 : 0;
            $phone         = (in_array($this->row['phone_home_status'], array('1', '2', '3')) || in_array($this->row['phone_cell_status'], array('1', '2', '3')) || in_array($this->row['phone_work_status'], array('1', '2', '3'))) ? 1 : 0;
            $num_visits    = ($this->row['num_visits']    > $scores['visits']['max'])    ? $scores['visits']['max']    : $this->row['num_visits'];
            $num_forms     = ($this->row['num_forms']     > $scores['inquiries']['max']) ? $scores['inquiries']['max'] : $this->row['num_forms'];
            $num_listings  = ($this->row['num_listings']  > $scores['listings']['max'])  ? $scores['listings']['max']  : $this->row['num_listings'];
            $num_favorites = ($this->row['num_favorites'] > $scores['favorites']['max']) ? $scores['favorites']['max'] : $this->row['num_favorites'];
            $num_saved     = ($this->row['num_saved']     > $scores['searches']['max'])  ? $scores['searches']['max']  : $this->row['num_saved'];
            $price_avg     = ($this->row['search_minimum_price'] + $this->row['search_maximum_price']) / 2;

            if ($settings['scoring']['rental'] == 'yes') {
                $min_price = $settings['scoring']['min_rent'];
                $max_price = $settings['scoring']['max_rent'];
            } else {
                $min_price = $settings['scoring']['min_price'];
                $max_price = $settings['scoring']['max_price'];
            }
            // If both values are empty set to zero
            $price         = ( !(empty($min_price) && empty($max_price)) &&
                // If min value is empty skip to next check or check min
                (empty($min_price) || $price_avg >= $min_price) &&
                // If max value is empty skip check or check max
                (empty($max_price) || $price_avg <= $max_price)
            ) ? 1 : 0;


            // Lead Scores
            $this->row['scores'] = array(
                'manual'    => ceil(($manual        / $scores['manual']['max'])    * ceil(($scores['manual']['value']    / $total) * 100)),
                'calls'     => ceil(($phone         / $scores['calls']['max'])     * ceil(($scores['calls']['value']     / $total) * 100)),
                'visits'    => ceil(($num_visits    / $scores['visits']['max'])    * ceil(($scores['visits']['value']    / $total) * 100)),
                'inquiries' => ceil(($num_forms     / $scores['inquiries']['max']) * ceil(($scores['inquiries']['value'] / $total) * 100)),
                'listings'  => ceil(($num_listings  / $scores['listings']['max'])  * ceil(($scores['listings']['value']  / $total) * 100)),
                'favorites' => ceil(($num_favorites / $scores['favorites']['max']) * ceil(($scores['favorites']['value'] / $total) * 100)),
                'searches'  => ceil(($num_saved     / $scores['searches']['max'])  * ceil(($scores['searches']['value']  / $total) * 100)),
                'price'     => ceil(($price         / $scores['price']['max'])     * ceil(($scores['price']['value']     / $total) * 100)),
            );

            // Lead Score Breakdown
            $this->row['scoring'] = array(
                'manual'    => array('score' => $this->row['scores']['manual'],    'value' => $manual,        'maximum' => $scores['calls']['manual'],  'weight' => $settings['scoring']['manual'],    'total' => ceil(($scores['manual']['value']    / $total) * 100)),
                'calls'     => array('score' => $this->row['scores']['calls'],     'value' => $phone,         'maximum' => $scores['calls']['max'],     'weight' => $settings['scoring']['calls'],     'total' => ceil(($scores['calls']['value']     / $total) * 100)),
                'visits'    => array('score' => $this->row['scores']['visits'],    'value' => $num_visits,    'maximum' => $scores['visits']['max'],    'weight' => $settings['scoring']['visits'],    'total' => ceil(($scores['visits']['value']    / $total) * 100)),
                'inquiries' => array('score' => $this->row['scores']['inquiries'], 'value' => $num_forms,     'maximum' => $scores['inquiries']['max'], 'weight' => $settings['scoring']['inquiries'], 'total' => ceil(($scores['inquiries']['value'] / $total) * 100)),
                'listings'  => array('score' => $this->row['scores']['listings'],  'value' => $num_listings,  'maximum' => $scores['listings']['max'],  'weight' => $settings['scoring']['listings'],  'total' => ceil(($scores['listings']['value']  / $total) * 100)),
                'favorites' => array('score' => $this->row['scores']['favorites'], 'value' => $num_favorites, 'maximum' => $scores['favorites']['max'], 'weight' => $settings['scoring']['favorites'], 'total' => ceil(($scores['favorites']['value'] / $total) * 100)),
                'searches'  => array('score' => $this->row['scores']['searches'],  'value' => $num_saved,     'maximum' => $scores['searches']['max'],  'weight' => $settings['scoring']['searches'],  'total' => ceil(($scores['searches']['value']  / $total) * 100)),
                'price'     => array('score' => $this->row['scores']['price'],     'value' => $price,         'maximum' => $scores['price']['max'],     'weight' => $settings['scoring']['price'],     'total' => ceil(($scores['price']['value']     / $total) * 100)),
            );

            // Days Since Active
            $sla = round((time() - strtotime($this->row['timestamp_active'])) / 86400);
            $sla = !empty($sla) ? $sla : 1;

            // Lead Score
            $score = ceil(array_sum($this->row['scores']));
            $score = ceil($score * (0.85 * (1 / $sla) + .15));
        }

        // Save Score
        $this->row['score'] = $score;

        // Update DB Row
        $this->db->query("UPDATE `users` SET `score` = '" . $score . "', `timestamp_score` = NOW() WHERE `id` = '" . $this->id . "';");

        // Return Score
        return $score;
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

    /************************* Lead Information *************************/

    /**
     * Update # of Texts and Last Text Details Performed by $authuser
     * @param Auth $authuser
     * @return void
     */
    public function updateTexts($authuser = null)
    {
        if ($authuser && $authuser->isAgent()) {
            $query = "SELECT
					COUNT(IF(`subtype` IN ('Incoming', 'OptIn', 'OptOut'), 1, NULL)) AS `total_incoming`,
					COUNT(IF(`subtype` IN ('Outgoing', 'Listing'), 1, NULL)) AS `total_outgoing`,
					`subtype`, MAX(`timestamp`) AS `timestamp`
					FROM (SELECT  `he`.`subtype`, `he`.`timestamp` AS `timestamp`"
                . " FROM `history_events` `he`"
                . " JOIN `history_users` `hl` ON `he`.`id` = `hl`.`event` AND `hl`.`user` = '" . $this->id . "' AND `hl`.`type` = 'Lead'"
                . " LEFT JOIN `history_users` `a` ON `he`.`id` = `a`.`event` AND `a`.`type` = 'Agent' AND (`a`.`user` IS NULL OR  `a`.`user` = '" . $authuser->info('id') . "')"
                . " WHERE `he`.`type` = 'Text' "
                . " ORDER BY `timestamp` DESC) `latest`;";
            if ($texts = $this->db->fetch($query)) {
                $this->row['num_texts_incoming'] = $texts['total_incoming'];
                $this->row['num_texts_outgoing'] = $texts['total_outgoing'];
                $this->row['num_texts'] = $texts['total_incoming'] + $texts['total_outgoing'];
                $this->row['last_text'] = !empty($texts['timestamp']) ? array('type' => $texts['subtype'], 'timestamp' => strtotime($texts['timestamp'])) : false;
            }
        }
    }

    /**
     * Update # of Calls and Last Call Details Performed by $authuser
     * @param Auth $authuser
     * @return void
     */
    public function updateCalls($authuser = null)
    {
        if ($authuser && ($authuser->isAgent() || $authuser->isLender())) {
            $query = "SELECT COUNT(*) AS `total`, `subtype`, MAX(`timestamp`) AS `timestamp` FROM (SELECT  `he`.`subtype`, `he`.`timestamp` AS `timestamp`"
                . " FROM `history_events` `he`"
                . " JOIN `history_users` `hl` ON `he`.`id` = `hl`.`event` AND `hl`.`user` = '" . $this->id . "' AND `hl`.`type` = 'Lead'"
                . ($authuser->isAgent() ? " JOIN `history_users` `a` ON `he`.`id` = `a`.`event` AND `a`.`type` = 'Agent' AND `a`.`user` = '" . $authuser->info('id') . "'" : "")
                . ($authuser->isLender() ? " JOIN `history_users` `l` ON `he`.`id` = `l`.`event` AND `l`.`type` = 'Lender' AND `l`.`user` = '" . $authuser->info('id') . "'" : "")
                . " WHERE `he`.`type` = 'Phone'"
                . " GROUP BY `timestamp` ORDER BY `timestamp` DESC) `latest`;";
            if ($calls = $this->db->fetch($query)) {
                $this->row['num_calls'] = $calls['total'];
                $this->row['last_call'] = !empty($calls['timestamp']) ? array('type' => $calls['subtype'], 'timestamp' => strtotime($calls['timestamp'])) : false;
            }
        }
    }

    /**
     * Update # of Emails and Last Email Details Performed by $authuser
     * @param Auth $authuser
     * @return void
     */
    public function updateEmails($authuser = null)
    {
        // Number of Emails and Last Email Details (Made by Viewer)
        if ($authuser && ($authuser->isAgent() || $authuser->isLender())) {
            $query = "SELECT COUNT(*) AS `total`, `subtype`, MAX(`timestamp`) AS `timestamp` FROM (SELECT `he`.`subtype`, `he`.`timestamp` AS `timestamp`"
                . " FROM `history_events` `he`"
                . " JOIN `history_users` `hl` ON `he`.`id` = `hl`.`event` AND `hl`.`user` = '" . $this->id . "' AND `hl`.`type` = 'Lead'"
                . ($authuser->isAgent() ? " JOIN `history_users` `a` ON `he`.`id` = `a`.`event` AND `a`.`type` = 'Agent' AND `a`.`user` = '" . $authuser->info('id') . "'" : "")
                . ($authuser->isLender() ? " JOIN `history_users` `l` ON `he`.`id` = `l`.`event` AND `l`.`type` = 'Lender' AND `l`.`user` = '" . $authuser->info('id') . "'" : "")
                . " WHERE `he`.`type` = 'Email'"
                . " GROUP BY `timestamp` ORDER BY `timestamp` DESC) `latest`;";
            if ($emails = $this->db->fetch($query)) {
                $this->row['num_emails'] = $emails['total'];
                $this->row['last_email'] = !empty($emails['total']) ? array('type' => $emails['subtype'], 'timestamp' => strtotime($emails['timestamp'])) : false;
            }
        }
    }

    /**
     * Get # of Notes and Last Note Timestamp
     * @param Auth $authuser
     * @param bool $showAll Show all lead notes
     * @return array
     */
    public function getNotes($authuser = null, $showAll = false)
    {
        // Fetch Lead Notes
        $query = "SELECT COUNT(`id`) AS `total`, UNIX_TIMESTAMP(MAX(`timestamp`)) AS `timestamp` FROM `users_notes` WHERE `user_id` = '" . $this->id . "'"
            // Only Show Agent's Own Notes (and Global/Shared Notes)
            . (!$showAll && $authuser && $authuser->isAgent() ? " AND (`agent_id` = '" . $authuser->info('id') . "' OR `share` = 'true' OR (`agent_id` IS NULL AND `lender` IS NULL AND `associate` IS NULL))" : "")
            // Only Show Lender's Own Notes (and Global/Shared Notes)
            . (!$showAll && $authuser && $authuser->isLender() ? " AND (`lender` = '" . $authuser->info('id') . "' OR `share` = 'true' OR (`agent_id` IS NULL AND `lender` IS NULL AND `associate` IS NULL))" : "")
            . ";";
        // Return Data
        return $this->db->fetch($query);
    }

    /**
     * Get Lead Reminders
     * @param Auth $authuser
     * @param bool $showAll Show all lead reminders
     * @return array
     */
    public function getReminders($authuser = null, $showAll = false)
    {
        // Lender cannot see Reminders
        if ($authuser && $authuser->isLender()) {
            return array();
        }
        // Fetch Lead Reminders (Agent's Own or Shared)
        $query = "SELECT COUNT(`id`) AS `total`, IF(MIN(`timestamp`) < NOW(), true, false) AS `overdue` FROM ("
            . "SELECT `id`, `timestamp` FROM `users_reminders` WHERE `user_id` = '" . $this->id . "' AND `completed` != 'true'"
            . (!$showAll && $authuser && $authuser->isAgent() ? " AND (`agent` = '" . $authuser->info('id') . "' OR `share` = 'true')" : "")
            . ") `r` HAVING `total` > 0;";
        // Return Data
        return $this->db->fetch($query);
    }

    /**
     * Get Assigned Agent's Activity
     * @return array
     */
    public function getAgentActivity()
    {
        // Assigned Agent's Calls & Emails
        $query = "SELECT COUNT(IF(`e`.`type` = 'Phone', 1, NULL)) AS `calls`, COUNT(IF(`e`.`type` = 'Email', 1, NULL)) AS `emails` FROM `history_events` `e`"
            . " JOIN `history_users` `u` ON `e`.`id` = `u`.`event` AND `u`.`type` = 'Lead' AND `u`.`user` = '" . $this->id . "'"
            . " JOIN `history_users` `a` ON `e`.`id` = `a`.`event` AND `a`.`type` = 'Agent' AND `a`.`user` = '" . $this->row['agent'] . "'"
            . " WHERE `e`.`type` IN ('Phone', 'Email')"
            . ";";
        // Return Data
        return $this->db->fetch($query);
    }

    /**
     * Get Assigned Lender's Activity
     * @return array
     */
    public function getLenderActivity()
    {
        // Assigned Lender's Activity (Calls & Emails)
        $query = "SELECT COUNT(IF(`e`.`type` = 'Phone', 1, NULL)) AS `calls`, COUNT(IF(`e`.`type` = 'Email', 1, NULL)) AS `emails` FROM `history_events` `e`"
            . " JOIN `history_users` `u` ON `e`.`id` = `u`.`event` AND `u`.`type` = 'Lead' AND `u`.`user` = '" . $this->id . "'"
            . " JOIN `history_users` `l` ON `e`.`id` = `l`.`event` AND `l`.`type` = 'Lender' AND `l`.`user` = '" . $this->row['lender'] . "'"
            . " WHERE `e`.`type` IN ('Phone', 'Email')"
            . ";";
        // Return Data
        return $this->db->fetch($query);
    }

    /**
     * Get Activity NOT Performed by $authuser
     * @param Auth $authuser
     * @return array
     */
    public function getOtherActivity(Auth $authuser)
    {
        // "Other Activity"
        $query = "SELECT COUNT(IF(`e`.`type` = 'Phone', 1, NULL)) AS `calls`, COUNT(IF(`e`.`type` = 'Email', 1, NULL)) AS `emails`"
            . " FROM `history_events` `e`"
            . " JOIN `history_users` `u` ON `e`.`id` = `u`.`event` AND `u`.`type` = 'Lead' AND `u`.`user` = '" . $this->id . "'"
            . ($authuser->isAgent() ? " LEFT JOIN `history_users` `a` ON `e`.`id` = `a`.`event` AND `a`.`type` = 'Agent'" : "")
            . ($authuser->isLender() ? " LEFT JOIN `history_users` `l` ON `e`.`id` = `l`.`event` AND `l`.`type` = 'Lender'" : "")
            . " WHERE `e`.`type` IN ('Phone', 'Email')"
            . ($authuser->isAgent() ? " AND (`a`.`user` IS NULL OR `a`.`user` != '" . $authuser->info('id') . "')" : "")
            . ($authuser->isLender() ? " AND (`l`.`user` IS NULL OR  `l`.`user` != '" . $authuser->info('id') . "')" : "")
            . ";";
        // Return Data
        return $this->db->fetch($query);
    }

    /**
     * @deprecated
     * Update lead's cell phone number
     * @param string $phone_number
     * @param Auth $authuser
     * @throws UnexpectedValueException
     * @return array Phone number details
     */
    public function updateCellNumber($phone_number, Auth $authuser = null)
    {
        try {
            // Validate provided phone number
            $phone_check = $this->validateCellNumber($phone_number);

            // Track cell phone change
            $phone_cell = $this->info('phone');
            $phone_number = $phone_check['phone_number'];
            $friendly_name = $phone_check['friendly_name'];
            $old_digits = preg_replace('/[^0-9]/', '', $phone_cell);
            $new_digits = preg_replace('/[^0-9]/', '', $friendly_name);
            if ($old_digits !== $new_digits) {
                // Update lead's cell phone number
                $query = $this->db->prepare("UPDATE `users` SET `phone` = :phone WHERE `id` = :user_id;");
                $query->execute(array('phone' => $friendly_name, 'user_id' => $this->getId()));
                $this->info('phone', $friendly_name);

                // Track cell phone change
                (new History_Event_Update_Lead(array(
                    'field' => 'phone',
                    'old' => $phone_cell,
                    'new' => $friendly_name
                ), array(
                    new History_User_Lead($this->getId()),
                    (!empty($authuser) ? $authuser->getHistoryUser() : null)
                )))->save($this->db);
            }

            // Assign phone number to lead
            $assign_number = $this->db->prepare("INSERT IGNORE INTO `twilio_verified_user` SET `phone_number` = :phone_number, `user_id` = :user_id;");
            $assign_number->execute(array('phone_number' => $phone_number, 'user_id' => $this->getId()));

            // Remove previously assigned number
            $unassign_number = $this->db->prepare("DELETE FROM `twilio_verified_user` WHERE `phone_number` != :phone_number AND `user_id` = :user_id;");
            $unassign_number->execute(array('phone_number' => $phone_number, 'user_id' => $this->getId()));

            // Return phone details
            return $phone_check;

            // Validation error occurred
        } catch (UnexpectedValueException $e) {
            throw $e;
        }
    }

    /**
     * @deprecated
     * Clear lead's cell phone number
     * @param Auth $authuser
     * @return void
     */
    public function removeCellNumber(Auth $authuser = null)
    {

        // Remove cell phone number
        $phone_cell = $this->info('phone_cell');
        if (!empty($phone_cell)) {
            $this->info('phone_cell', '');

            // Update lead's database record
            $query = $this->db->prepare("UPDATE `users` SET `phone_cell` = '' WHERE `id` = :user_id;");
            $query->execute(array('user_id' => $this->getId()));

            // Remove assigned number
            $unassign_number = $this->db->prepare("DELETE FROM `twilio_verified_user` WHERE `user_id` = :user_id;");
            $unassign_number->execute(array('user_id' => $this->getId()));

            // Track cell phone change
            (new History_Event_Update_Lead(array(
                'field' => 'phone_cell',
                'old' => $phone_cell
            ), array(
                new History_User_Lead($this->getId()),
                (!empty($authuser) ? $authuser->getHistoryUser() : null)
            )))->save($this->db);
        }
    }

    /**
     * Validate phone number and return details
     * @throws UnexpectedValueException
     * @return array Phone number details
     */
    public function validateCellNumber($phone_number, $phone_text = 'primary')
    {

        // Require php-libphonenumber for formatting and validating phone numbers
        require_once Settings::getInstance()->DIRS['LIB'] . 'libphonenumber/autoload.php';
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();

        // Fail on empty input
        $phone_number = Format::trim($phone_number);
        if (empty($phone_number)) {
            throw new UnexpectedValueException('You must provide a valid ' . $phone_text . ' phone number.');
        }

        // Need atleast 10 digits: (###) ###-####
        $phoneDigits = preg_replace('/[^0-9]/', '', $phone_number);
        if (strlen($phoneDigits) < 10) {
            throw new UnexpectedValueException('Please provide a full 10-digit ' . $phone_text . ' phone number.');
        }

        try {
            // Must be valid phone number
            $phoneNumber = $phoneUtil->parse($phoneDigits, 'US');
            if (!$phoneUtil->isValidNumber($phoneNumber)) {
                // If It Fails, Try Again With North American Country Code In Place
                $phoneNumber = $phoneUtil->parse('+1' . $phoneDigits, 'US');
                if (!$phoneUtil->isValidNumber($phoneNumber)) {
                    throw new UnexpectedValueException('The ' . $phone_text . ' phone number does not seem to be valid.');
                }
            }

            // Lookup phone number is database
            $query = $this->db->prepare("SELECT `verified`, `optout` FROM `twilio_verified` WHERE `phone_number` = :phone_number LIMIT 1;");
            $query->execute(array('phone_number' => $phoneUtil->format($phoneNumber, \libphonenumber\PhoneNumberFormat::E164)));
            $check_number = $query->fetch();

            // Add number to database
            if (empty($check_number)) {
                $save_number = $this->db->prepare("INSERT INTO `twilio_verified` SET `phone_number` = :phone_number, `created_ts` = NOW();");
                $save_number->execute(array('phone_number' => $phoneUtil->format($phoneNumber, \libphonenumber\PhoneNumberFormat::E164)));
            }

            // Phone data
            return array(
                'phone_number'  => $phoneUtil->format($phoneNumber, \libphonenumber\PhoneNumberFormat::E164),
                'friendly_name' => $phone_number,
                'verified'      => $check_number['verified'],
                'optout'        => $check_number['optout']
            );

            // Validation error
        } catch (\libphonenumber\NumberParseException $e) {
            throw new UnexpectedValueException('The ' . $phone_text . ' phone number does not seem to be valid.');
        }
    }

    /**
     * Returns list of assigned action plans and the number of tasks currently due
     * @param Auth $authuser
     * @param $showAll Show All Action Plans
     * @return $info array
     */
    public function getActionPlanInfo(Auth $authuser = null, $showAll = false)
    {

        $info = array();

        // Get assigned action plans
        $query = $this->db->query("SELECT `a`.`id`, `a`.`name`, `a`.`style`, `au`.`timestamp_assigned`, IF(`au`.`timestamp_completed` IS NULL, 'N', 'Y') AS `completed` "
            . "FROM `action_plans` `a` JOIN `users_action_plans` `au` ON `a`.`id` = `au`.`actionplan_id` "
            . "WHERE `au`.`user_id` = '" . $this->getId()  . "' ORDER BY `au`.`timestamp_assigned` DESC;");
        while ($plan = $query->fetch()) {
            if ($plan['completed'] == 'Y') {
                $info['completed'][] = $plan;
            } else {
                $info['in_progress'][] = $plan;
            }
        }

        // If there are plans in progress, count due tasks
        if (!empty($info['in_progress'])) {
            // Get assigned tasks - only show task info for those concerning the authuser (Admin unrestricted)
            if (!$showAll) {
                if ($authuser && $authuser->isAgent()) {
                    $performer_sql = " AND `at`.`performer` = 'Agent'";
                } else {
                    if ($authuser && $authuser->isLender()) {
                        $performer_sql = " AND `at`.`performer` = 'Lender'";
                    } else {
                        if ($authuser && $authuser->isAssociate()) {
                            $performer_sql = " AND `at`.`performer` = 'Associate'";
                        }
                    }
                }
            }
            $tasks = $this->db->fetch("SELECT COUNT(IF((`atu`.`timestamp_due` < NOW() AND `atu`.`status` = 'Pending' AND `at`.`automated` = 'N'), 1, NULL)) AS `due` "
                . "FROM `tasks` `at` JOIN `users_tasks` `atu` ON `at`.`id` = `atu`.`task_id` "
                . "WHERE `atu`.`user_id` = '" . $this->getId() . "' AND `at`.`automated` = 'N' " . (!empty($performer_sql) ? $performer_sql : '') . ";");
            $info['due_tasks'] = $tasks['due'];
        }

        return $info;
    }
}
