<?php

/**
 * Search by Subdivision
 * @package IDX_Panel
 */
class IDX_Panel_Subdivision extends IDX_Panel_Type_Input
{

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'Subdivision';

    /**
     * Input Name
     * @var string
     */
    protected $inputName = 'search_subdivision';

    /**
     * Input Class
     * @var string
     */
    protected $inputClass = 'x12 autocomplete location';

    /**
     * IDX Field
     * @var string
     */
    protected $field = 'AddressSubdivision';

    /**
     * @see IDX_Panel_Interface_Taggable::getTags
     * @return IDX_Search_Tag[]
     */
    public function getTags()
    {
        return array_map(function ($value) {
            return new IDX_Search_Tag(
                $value,
                array($this->inputName => $value)
            );
        }, $this->getValues());
    }
}
