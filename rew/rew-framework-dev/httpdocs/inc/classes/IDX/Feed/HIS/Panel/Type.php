<?php

/**
 * HIS Panel: Search by Property Type
 * @package IDX_Panel
 */
class IDX_Feed_HIS_Panel_Type extends IDX_Panel_Type
{

    /**
     * Filter Out Property Types Containg Commas
     * @see IDX_Panel_Type::getOptions()
     */
    public function getOptions()
    {
        return array_filter(parent::getOptions(), function ($option) {
            return (stristr($option['title'], ',') === false);
        });
    }
}
