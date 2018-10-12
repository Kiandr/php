<?php

/**
 * Backend_Group
 * @package Backend
 */
abstract class Backend_Group
{

    /**
     * Lead Groups
     * @var string
     */
    const LEAD = 1;

    /**
     * Agent Groups
     * @var string
     */
    const AGENT = 2;

    /**
     * Campaign Groups
     * @var string
     */
    const CAMPAIGN = 3;

    /**
     * Load Available Groups (for Specific Type of Row)
     * <code>
     * $groups = Backend_Group::getGroups($errors); // Available Groups
     * $groups = Backend_Group::getGroups($errors, Backend_Group::LEAD, $lead['id']); // Groups Assigned to Lead
     * $groups = Backend_Group::getGroups($errors, Backend_Group::AGENT, $agent['id']); // Groups Available to Agent
     * $groups = Backend_Group::getGroups($errors, Backend_Group::CAMPAIGN, $campaign['id']); // Groups Assigned to Campaign
     * </code>
     * @param array $errors Error report will be appended to collection. Passed by reference.
     * @param string $type Can be Backend_Group::LEAD, Backend_Group::AGENT or Backend_Group::CAMPAIGN
     * @param int $row Row ID for specified $type. Must be set if $type is supplied.
     * @param Auth $authuser
     * @return array Available Groups (Array)
     * @throws InvalidArgumentException If invalid arguments supplied
     * @throws UnexpectedValueException If Auth::get could not be loaded
     * @uses Auth To get id and check current mode
     */
    public static function getGroups(&$errors = array(), $type = null, $row = null, $authuser = null)
    {

        // Load Groups
        $groups = array();

        try {
            // DB Connection
            $db = DB::get('users');

            // Auth User
            $authuser = is_null($authuser) ? Auth::get() : $authuser;

            // Require Auth User
            if (empty($authuser)) {
                throw new UnexpectedValueException('Unauthorized User');
            }

            // SELECT & JOIN Agents/Associates
            $sql_select = "CONCAT(`agents`.`first_name`, ' ', `agents`.`last_name`) AS `agent`, CONCAT(`associates`.`first_name`, ' ', `associates`.`last_name`) AS `associate`";
            $sql_join   = "LEFT JOIN `agents` ON `agents`.`id` = `g`.`agent_id` LEFT JOIN `associates` ON `associates`.`id` = `g`.`associate`";

            // Lead Groups
            if ($type === self::LEAD && !empty($row)) {
                $query = "SELECT `g`.`id`, `g`.`name`, `g`.`agent_id`, `g`.`description`, `g`.`style`, `g`.`user`, " . $sql_select
                    . " FROM `groups` `g`"
                    . " JOIN `users_groups` `ug` ON `g`.`id` = `ug`.`group_id` AND `ug`.`user_id` = " . $db->quote($row)
                    . " LEFT JOIN `users` `u` ON `u`.`id` = `ug`.`user_id`"
                    . $sql_join
                    . ($authuser->info('mode') == 'agent'   ? " WHERE ((`g`.`agent_id` IS NULL AND `g`.`associate` IS NULL) OR `g`.`agent_id`	= '" . $authuser->info('id') . "')" : "")
                    . ($authuser->isAssociate()             ? " WHERE ((`g`.`agent_id` IS NULL AND `g`.`associate` IS NULL) OR `g`.`agent_id` = `u`.`agent` OR `g`.`associate`	= '" . $authuser->info('id') . "')" : "")
                    . " ORDER BY `g`.`name` ASC;";

            // Agent Groups
            } elseif ($type === self::AGENT && !empty($row)) {
                $query = "SELECT `g`.`id`, `g`.`name`, `g`.`description`, `g`.`style`, `g`.`user`, " . $sql_select
                    . " FROM `groups` `g`"
                    . $sql_join
                    . " WHERE (`g`.`agent_id` = " . $db->quote($row) . " OR (`g`.`agent_id` IS NULL AND `g`.`associate` IS NULL))"
                    . " ORDER BY `g`.`name` ASC;";

            // Campaign Groups
            } elseif ($type === self::CAMPAIGN && !empty($row)) {
                $query = "SELECT `g`.`id`, `g`.`name`, `g`.`description`, `g`.`style`, `g`.`user`, " . $sql_select
                    . " FROM `campaigns_groups` `cg`"
                    . " LEFT JOIN `groups` `g` ON `cg`.`group_id` = `g`.`id`"
                    . $sql_join
                    . " WHERE `cg`.`campaign_id` = " . $db->quote($row)
                    . ($authuser->info('mode') == 'agent'   ? " AND ((`g`.`agent_id` IS NULL AND `g`.`associate` IS NULL) OR `g`.`agent_id`	= '" . $authuser->info('id') . "')" : "")
                    . ($authuser->isAssociate()             ? " AND ((`g`.`agent_id` IS NULL AND `g`.`associate` IS NULL) OR `g`.`associate`	= '" . $authuser->info('id') . "')" : "")
                    . " ORDER BY `g`.`name` ASC;";

            // All Available Groups
            } elseif (is_null($type) && is_null($row)) {
                $query = "SELECT `g`.`id`, `g`.`name`, `g`.`description`, `g`.`style`, `g`.`user`, " . $sql_select
                    . " FROM `groups` `g`"
                    . $sql_join
                    . ($authuser->info('mode') == 'admin'   ? " WHERE (`g`.`agent_id` IS NULL OR `g`.`agent_id`	= '" . $authuser->info('id') . "')" : "")
                    . ($authuser->info('mode') == 'agent'   ? " WHERE (`g`.`agent_id` IS NULL AND `g`.`associate` IS NULL) OR `g`.`agent_id`	= '" . $authuser->info('id') . "'" : "")
                    . ($authuser->isAssociate()             ? " WHERE (`g`.`agent_id` IS NULL AND `g`.`associate` IS NULL) OR `g`.`associate`	= '" . $authuser->info('id') . "'" : "")
                    . " ORDER BY `name` ASC;";

            // Invalid Arguments
            } else {
                throw new InvalidArgumentException('Invalid arguments supplied.');
            }

            // Fetch Groups & Build Collection
            array_walk($db->fetchAll($query), function (&$group) use ($authuser, &$groups) {

                // Group Title (for Mouseover)
                $group['title'] = (is_null($group['agent']) && is_null($group['associate']) ? ($group['user'] == 'false' ? '(Global)' : '(Shared)')
                    : (!empty($group['agent']) ? '(' . $group['agent'] . ')' : (!empty($group['associate']) ? '(' . $group['associate'] . ')' : ''))
                );

                // Add to Collection
                $groups[$group['id']] = $group;
            });

        // Runtime Error (Invalid Auth)
        } catch (UnexpectedValueException $e) {
            $errors[] = $e->getMessage();
            Log::error($e);

        // Database Error
        } catch (PDOException $e) {
            //$errors[] = $e->getMessage();
            $errors[] = 'Error loading'  . ($type ? ' ' . $type : '') . ' groups.';
            Log::error($e);
        }

        // Return Groups
        return $groups;
    }
}
