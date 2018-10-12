<?php

/**
 * API_Object_Lead
 *
 */
class API_Object_Lead extends API_Object
{

    /**
     * Cached lead data
     * @var array
     */
    private $_data;

    /**
     * Get the object's data
     * @return array
     */
    public function getData()
    {

        // Get data
        if (is_null($this->_data)) {
            $db = $this->_db;
            $row = $this->_row;
            $data = array();

            // Fetch agent
            $agent_row = $db->{'agents'}->search(array(
                '$eq' => array(
                    'id' => $row['agent'],
                ),
            ))->fetch();

            // API object
            $agent = new API_Object_Agent($db, $agent_row);

            // Fetch Groups
            $groups = array();
            $sql = "SELECT `g`.* FROM `users_groups` ug "
                    . "LEFT JOIN `groups` g ON g.`id` = ug.`group_id` "
                    . "WHERE `user_id` = " . $db->quote($row['id']) . ";";

            // Execute
            if ($lead_groups = $db->fetchAll($sql)) {
                foreach ($lead_groups as $lead_row) {
                    $object = new API_Object_Group($db, $lead_row);
                    $groups[] = $object->getData();
                }
            }

            // Data subset
            $data = array(
                'id'                => intval($row['id']),
                'agent'             => $agent->getData(),
                'first_name'        => html_entity_decode($row['first_name'], ENT_COMPAT | ENT_HTML401, 'UTF-8'),
                'last_name'         => html_entity_decode($row['last_name'], ENT_COMPAT | ENT_HTML401, 'UTF-8'),
                'email'             => $row['email'],
                'email_alt'         => $row['email_alt'],
                'groups'            => $groups,
                'address'           => html_entity_decode($row['address1'], ENT_COMPAT | ENT_HTML401, 'UTF-8'),
                'city'              => html_entity_decode($row['city'], ENT_COMPAT | ENT_HTML401, 'UTF-8'),
                'state'             => html_entity_decode($row['state'], ENT_COMPAT | ENT_HTML401, 'UTF-8'),
                'zip'               => html_entity_decode($row['zip'], ENT_COMPAT | ENT_HTML401, 'UTF-8'),
                'phone'             => $row['phone'],
                'phone_cell'        => $row['phone_cell'],
                'phone_work'        => $row['phone_work'],
                'phone_fax'         => $row['phone_fax'],
                'heat'              => $row['heat'],
                'comments'          => html_entity_decode($row['comments'], ENT_COMPAT | ENT_HTML401, 'UTF-8'),
                'origin'            => html_entity_decode($row['referer'], ENT_COMPAT | ENT_HTML401, 'UTF-8'),
                'keywords'          => (!empty($row['keywords']) ? $row['keywords'] : null),
                'opt_marketing'     => $row['opt_marketing'],
                'opt_searches'      => $row['opt_searches'],
                'opt_texts'         => $row['opt_texts'],
                'auto_rotate'       => ($row['auto_rotate'] == 'true' ? 'true' : 'false'),
                'source_user_id'    => (!empty($row['source_user_id']) ? intval($row['source_user_id']) : null),
                'num_visits'        => intval($row['num_visits']),
                'timestamp'         => strtotime($row['timestamp']),
            );

            // Cache data
            $this->_data = $data;
        }

        return $this->_data;
    }
}
