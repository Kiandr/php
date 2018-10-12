<?php

/**
 * API_Object_Group
 *
 */
class API_Object_Group extends API_Object
{

    /**
     * Get the object's data
     * @return array
     */
    public function getData()
    {
        $row = $this->_row;
        $data = array(
            'id' => intval($row['id']),
            'agent_id' => (!empty($row['agent_id']) ? intval($row['agent_id']) : null),
            'name' => html_entity_decode($row['name'], ENT_COMPAT | ENT_HTML401, 'UTF-8'),
            'description' => html_entity_decode($row['description'], ENT_COMPAT | ENT_HTML401, 'UTF-8'),
            'system' => ($row['user'] === 'true' ? false : true),
            'timestamp' => strtotime($row['timestamp']),
        );
        return $data;
    }
}
