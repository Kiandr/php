<?php

/**
 * API_Object_Lead_Search
 *
 */
class API_Object_Lead_Search extends API_Object
{

    /**
     * Get the object's data
     * @return array
     */
    public function getData()
    {
        $row = $this->_row;
        $criteria = @unserialize($row['criteria']);
        $data = array(
            'id'                => intval($row['id']),
            'title'             => $row['title'],
            'criteria'          => (is_array($criteria) ? $criteria : array()),
            'frequency'         => $row['frequency'],
            'times_sent'        => intval($row['sent']),
            'feed'              => $row['idx'],
            'source'            => $row['table'],
            'timestamp_sent'    => (!empty($row['timestamp_sent']) ? strtotime($row['timestamp_sent']) : null),
            'timestamp'         => (!empty($row['timestamp_created']) ? strtotime($row['timestamp_created']) : null),
        );
        return $data;
    }
}
