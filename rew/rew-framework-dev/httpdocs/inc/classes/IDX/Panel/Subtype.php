<?php

/**
 * Property Sub-Type
 * @package IDX_Panel
 */
class IDX_Panel_Subtype extends IDX_Panel_Type_Select
{

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'Property Sub-Type';

    /**
     * Input Name
     * @var string
     */
    protected $inputName = 'search_subtype';

    /**
     * IDX Field
     * @var string|null
     */
    protected $field = 'ListingSubType';

    /**
     * Placeholder Option
     * @var string
     */
    protected $placeholder = 'All Properties';

    /**
     * Split options by delimiter
     * @var string
     */
    protected $delimiter;

    /**
     * @see IDX_Panel::__construct()
     */
    public function __construct($options = array())
    {
        if (isset($options['delimiter'])) {
            $this->delimiter = $options['delimiter'];
        }
        parent::__construct($options);
    }

    /**
     * @see IDX_Panel::getOptions()
     */
    public function getOptions()
    {

        // Update Placeholder
        if ($this->placeholder !== false && isset($_REQUEST['search_type'])) {
            $types = Format::trim($_REQUEST['search_type']);
            if (!empty($types)) {
                if (is_array($types)) {
                    if (count($types) === 1) {
                        $this->placeholder = 'All ' . $types[0] . ' Listings';
                    } else {
                        $this->placeholder = 'All Properties';
                    }
                } else {
                    $this->placeholder = 'All ' . $types . ' Listings';
                }
            }
        }

        // Available options
        $options = parent::getOptions();

        // Split by delimiter
        $delimiter = $this->delimiter;
        if (!empty($delimiter)) {
            $opts = array();
            $extras = array();
            foreach ($options as $k => $option) {
                if (stristr($option['title'], $delimiter) !== false) {
                    foreach (explode($delimiter, $option['title']) as $opt) {
                        $opt = trim($opt);
                        if (empty($opt)) {
                            continue;
                        }
                        if (empty($opts[$opt])) {
                            $extras[$opt] = 1;
                        } else {
                            $extras[$opt]++;
                        }
                    }
                } elseif (empty($opts[$option['title']])) {
                    $opts[$option['title']] = $option;
                }
            }
            arsort($extras);
            $extras = array_filter($extras, function ($opt) use ($opts) {
                return !array_key_exists($opt, $opts);
            }, ARRAY_FILTER_USE_KEY);
            $options = array_values(array_merge($opts, array_map(function ($opt) {
                return array('value' => $opt, 'title' => $opt);
            }, array_keys($extras))));
        }

        // Return options
        return $options;
    }

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
            $where .= "(`" . $type . "` = '" . implode("' OR `" . $type . "` = '", $types) . "')";
        }
        $where .= (!empty($where) ? " AND " : "") . "`" . $type . "` != ''";

        // Order by # of Records
        $field = $idx->field($field);
        $order = is_null($order) ? '' : $order . ',';
        $order .= "COUNT(`" . $field . "`) DESC";

        // Fetch Options
        return parent::fetchOptions($field, $where, $order);
    }

    /**
     * Fetches the type-subtype relationships for this feed, for type-subtype field accuracy without needing to use AJAX
     * @return array
     */
    public static function getAllTypes()
    {

        // IDX Feed
        $idx = Util_IDX::getIdx();
        $db_idx = Util_IDX::getDatabase();

        // Build SELECT Query
        $table = $idx->getTable();
        $field_type = $idx->field('ListingType');
        $field_subtype = $idx->field('ListingSubType');

        // Build WHERE Clause
        $sql_where = "`" . $field_type . "` != '' AND `" . $field_type . "` IS NOT NULL AND `" . $field_subtype . "` != '' AND `" . $field_subtype . "` IS NOT NULL";

        // Any global criteria
        $idx->executeSearchWhereCallback($sql_where);

        $query ="SELECT DISTINCT `" . $field_type . "` AS `type`, `" . $field_subtype . "` AS `sub_type`"
            . " FROM `" .$table . "`"
            . " WHERE " . $sql_where
            . " GROUP BY `" . $field_type . "`, `" . $field_subtype . "`"
        . ";";

        // Cache Index
        $index = __METHOD__ . ':' . $idx->getName() . ':' . $db_idx->db() . ':' . $table . ':' . md5($query);

        // Is Cached (Server-Wide)
        $options = IDX_Panel::$useCache && !IDX_Panel::$reCache ? Cache::getCache($index, true) : null;
        if (!is_array($options)) {
            // Load Options
            $options = array();
            if ($result = $db_idx->query($query)) {
                while ($option = $db_idx->fetchArray($result)) {
                    $options[$option['type']][] = array ('value' => $option['sub_type'], 'title' => ucwords(strtolower($option['sub_type'])));
                }
            }

            // Save Cache (Server-Wide)
            if (static::$useCache || static::$reCache) {
                Cache::setCache($index, $options, true);
            }
        }

        // Return Options
        return $options;
    }

    /**
     * Return delimiter set for splitting values
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }
}
