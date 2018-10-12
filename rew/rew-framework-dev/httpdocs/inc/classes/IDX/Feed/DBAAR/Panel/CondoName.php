<?php

/**
 * Search by CondoName
 * @package IDX_Panel
 */
class IDX_Feed_DBAAR_Panel_CondoName extends IDX_Panel_Type_Dynamic
{

    /**
     * Panel Title
     * @var string
     */
    public $title = 'Condo Name';

    /**
     * Input Name
     * @var string
     */
    public $inputName = 'search_condoname';

    /**
     * Class Name for Input Field
     * @var string
     */
    protected $inputClass = 'location';

    /**
     * IDX Field
     * @var string
     */
    protected $field = 'CondoName';

    /**
     * Field Type
     * @var string
     */
    protected $fieldType = 'Checklist';

    /**
     * Panel Class
     * @var string
     */
    protected $panelClass = 'scrollable';

    /**
     * @see IDX_Panel_Type_Dynamic::__construct()
     */
    public function __construct($options = array())
    {
        parent::__construct($options);
        if ($this->fieldType !== 'Checklist') {
            $this->inputClass .= ' x12';
        }
    }
}
