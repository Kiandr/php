<?php

/**
 * Source_Link extends Source and is used for Including External Source Code via HTTP(s) Links
 * @package REW
 * @subpackage Source
 */
class Source_Link extends Source
{

    /**
     * Link
     * @var string
     */
    protected $link;

    /**
     * @var bool
     */
    protected $async;

    /**
     * Create Link
     * @param string $link Link
     * @param string $type
     * @param string $load
     */
    public function __construct($link, $type, $load = "none")
    {
        $this->link = $link;
        $this->load = $load;
        parent::__construct($type);
    }

    /**
     * Get Link
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }
}
