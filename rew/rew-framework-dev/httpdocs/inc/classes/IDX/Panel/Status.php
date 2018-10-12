<?php

/**
 * Listing Status
 * @package IDX_Panel
 */
class IDX_Panel_Status extends IDX_Panel_Type_Dynamic
{

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'Listing Status';

    /**
     * Input Name
     * @var string
     */
    protected $inputName = 'search_status';

    /**
     * IDX Field
     * @var string|null
     */
    protected $field = 'ListingStatus';

    /**
     * Field Type
     * @var string
     */
    protected $fieldType = 'Checklist';

    /**
     * @see IDX_Panel::fetchOptions()
     */
    public static function fetchOptions($field, $where = null, $order = null)
    {

        // IDX Feed
        $idx = Util_IDX::getIdx();
        $db_idx = Util_IDX::getDatabase();

        // Filter Sub-Type By Type
        $types = isset($_REQUEST['search_type']) ? $_REQUEST['search_type'] : "";
        $types = is_array($types) ? Format::trim($types) : Format::trim(explode(',', $types));
        $types = array_filter($types);

        // Filter Options
        $where = is_null($where) ? '' : $where . ' AND ';
        $type = $idx->field('ListingType');
        if (!empty($types) && !empty($type)) {
            $types = array_map(array(&$db_idx,'cleanInput'), $types);
            $where .= "`" . $type . "` = '" . implode("' OR `" . $type . "` = '", $types) . "'";
        }

        // Fetch Options
        return parent::fetchOptions($field, $where, $order);
    }
}
