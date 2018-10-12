<?php

/**
 * API_Object_Agent
 *
 */
class API_Object_Agent extends API_Object
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
            'name' => html_entity_decode($row['first_name'] . ' ' . $row['last_name'], ENT_COMPAT | ENT_HTML401, 'UTF-8'),
            'email' => $row['email'],
            'title' => html_entity_decode($row['title'], ENT_COMPAT | ENT_HTML401, 'UTF-8'),
        );
        return $data;
    }
}
