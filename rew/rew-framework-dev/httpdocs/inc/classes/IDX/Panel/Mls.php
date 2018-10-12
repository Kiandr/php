<?php

/**
 * Search by MLS #
 * @package IDX_Panel
 */
class IDX_Panel_Mls extends IDX_Panel_Type_Input
{

    /**
     * Input Name
     * @var string
     */
    protected $inputName = 'search_mls';

    /**
     * @see IDX_Panel_Type_Input::__construct
     * @param array $options
     */
    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->title = Lang::write('MLS') . ' Number';
    }

    /**
     * @see IDX_Panel_Interface_Taggable::getTags
     * @return IDX_Search_Tag[]
     */
    public function getTags()
    {
        return array_map(function ($value) {
            return new IDX_Search_Tag(
                Lang::write('MLS_NUMBER') . $value,
                array($this->inputName => $value)
            );
        }, $this->getValues());
    }
}
