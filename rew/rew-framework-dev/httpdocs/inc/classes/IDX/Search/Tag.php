<?php

/**
 * IDX_Search_Tag
 * @package IDX
 */
class IDX_Search_Tag
{

    /**
     * Display title
     * @var string
     */
    protected $title;

    /**
     * Field data
     * @var array
     */
    protected $field;

    /**
     * Create search tag
     * @param string $title
     * @param array $field
     */
    public function __construct($title, $field = null)
    {
        $this->title = $title;
        $this->field = $field;
    }

    /**
     * Get tag title
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get field data
     * @return array
     */
    public function getField()
    {
        return $this->field;
    }
}
