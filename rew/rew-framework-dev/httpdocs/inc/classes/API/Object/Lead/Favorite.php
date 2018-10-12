<?php

/**
 * API_Object_Lead_Favorite
 *
 */
class API_Object_Lead_Favorite extends API_Object
{

    /**
     * Get the object's data
     * @return array
     */
    public function getData()
    {
        $row = $this->_row;
        $data = array(
            'id'            => intval($row['id']),
            'mls_number'    => $row['mls_number'],
            'type'          => $row['type'],
            'price'         => intval($row['price']),
            'city'          => $row['city'],
            'subdivision'   => $row['subdivision'],
            'num_bedrooms'  => ($row['bedrooms'] == 0 ? null : intval($row['bedrooms'])),
            'num_bathrooms' => ($row['bathrooms'] == 0 ? null : floatval($row['bathrooms'])),
            'num_sqft'      => ($row['sqft'] == 0 ? null : intval($row['sqft'])),
            'feed'          => $row['idx'],
            'source'        => $row['table'],
        );
        return $data;
    }
}
