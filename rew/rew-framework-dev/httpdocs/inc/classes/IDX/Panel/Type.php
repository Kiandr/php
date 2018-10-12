<?php

/**
 * Search by Property Type
 * @package IDX_Panel
 */
class IDX_Panel_Type extends IDX_Panel_Type_Dynamic
{

    /**
     * Panel Title
     * @var string
     */
    public $title = 'Property Type';

    /**
     * Input Name
     * @var string
     */
    protected $inputName = 'search_type';

    /**
     * IDX Field
     * @var string
     */
    protected $field = 'ListingType';

    /**
     * Field Type
     * @var string
     */
    protected $fieldType = 'Checklist';

    /**
     * Placeholder Option
     * @var string
     */
    protected $placeholder = 'All Properties';

    /**
     * @see IDX_Panel::__construct()
     */
    public function __construct($options = array())
    {
        if (isset($options['placeholder'])) {
            $this->placeholder = $options['placeholder'];
        }
        parent::__construct($options);
    }

    /**
     * Add 'All Properties' Option to Top of List
     * @see IDX_Panel::getOptions()
     */
    public function getOptions()
    {
        if (!isset($this->options)) {
            $this->options = parent::getOptions();
            if (!empty($this->placeholder) && $this->fieldType != 'Select') {
                $this->options = array_merge(array(
                    array('value' => '', 'title' => $this->placeholder)
                ), $this->options);
            }
        }
        return $this->options;
    }

    /**
     * @see IDX_Panel::fetchOptions()
     */
    public static function fetchOptions($field, $where = null, $order = null)
    {

        // IDX Feed
        $idx = Util_IDX::getIdx();

        // Order by # of Records
        $orderField = $idx->field($field);
        $order = is_null($order) ? '' : $order . ',';
        $order .= "COUNT(`" . $orderField . "`) DESC";

        // Fetch Options
        return parent::fetchOptions($field, $where, $order);
    }

    /**
     * Include Hidden Input to Indentify IDX Feed
     * @see IDX_Panel_Type_Radiolist::getMarkup()
     */
    public function getMarkup()
    {

        // IDX Feed
        $idx = Util_IDX::getIdx();

        // Field HTML Markup
        $html = parent::getMarkup();

        // Return HTML Markup
        return $html . '<input type="hidden" name="idx" value="' . $idx->getLink() . '">';
    }

    /**
     * Returns Placeholder Option
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    public function typeSpecificJsonSerialize()
    {
        return array_merge(parent::typeSpecificJsonSerialize(),
            [
                'options' => $this->getOptions()
            ]);
    }
}
