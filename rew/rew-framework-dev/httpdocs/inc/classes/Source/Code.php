<?php

/**
 * Source_Code extends Source is used for Including Inline Source Code
 * @package REW
 * @subpackage Source
 */
class Source_Code extends Source
{

    /**
     * Data
     * @var string
     */
    protected $data;

    /**
     * Size
     * @var int
     */
    protected $size;

    /**
     * Define whether source code is needed for critical path (above-the-fold)
     * @var boolean
     */
    protected $critical;

    /**
     * Create Code
     * @param string $data
     * @param string $type
     * @param bool $critical
     * @param string $load
     */
    public function __construct($data, $type, $critical = false, $load = "none")
    {
        $this->critical = $critical;
        $this->data = $data;
        $this->load = $load;
        parent::__construct($type);
    }

    /**
     * Get Data
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get Size
     * @param bool $reload Force Reload
     * @return int
     */
    public function getSize($reload = false)
    {
        if (is_null($this->size) || $reload) {
            $this->size = mb_strlen($this->data);
        }
        return (int) $this->size;
    }

    /**
     * Is the source code critical (above-the-fold)
     * @return boolean
     */
    public function isCritical()
    {
        return (bool) $this->critical;
    }
}
