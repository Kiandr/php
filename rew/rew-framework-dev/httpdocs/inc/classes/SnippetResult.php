<?php

use REW\Core\Interfaces\Snippet\ResultInterface;

class SnippetResult implements ResultInterface
{
    private $name;
    private $html;
    private $row;

    /**
     * SnippetResult constructor.
     * @param string $name
     * @param string|null $html
     * @param array $row
     */
    public function __construct($name, $html = null, array $row = array())
    {
        $this->html = $html;
        $this->row = $row;
    }

    /**
     * Return unformatted raw html
     * @return string
     */
    public function __toString()
    {
        return $this->html !== null ? $this->html : '#' . $this->name . '#';
    }

    /**
     * Return true if this is a valid snippet
     * @return bool
     */
    public function isValid()
    {
        return !empty($this->html);
    }

    /**
     * Return the manipulated row
     * @return array
     */
    public function getRow()
    {
        return $this->row;
    }
}
