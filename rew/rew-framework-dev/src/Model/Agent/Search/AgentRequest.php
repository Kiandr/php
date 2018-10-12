<?php
namespace REW\Model\Agent\Search;

use \InvalidArgumentException;

/**
 * AgentRequest
 * @package REW\Model\Agent\Search
 */
class AgentRequest
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $letter;

    /**
     * @var string
     */
    protected $link;

    /**
     * @var bool
     */
    protected $display;

    /**
     * @var int
     */
    protected $officeId;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var int
     */
    protected $page;

    /**
     * @var array
     */
    protected $order;

    /**
     * @param int $id
     * @return self
     */
    public function withId($id)
    {
        if (!is_int($id)) {
            throw new InvalidArgumentException('$int must be an integer!');
        }
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @return self
     */
    public function withName($name)
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException('$name must be a string!');
        }
        $clone = clone $this;
        $clone->name = $name;
        return $clone;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $letter
     * @return self
     */
    public function withLetter($letter)
    {
        if (!is_string($letter) || count($letter) > 1 || !(ctype_alpha($letter))) {
            throw new InvalidArgumentException('$letter must be a character!');
        }
        $clone = clone $this;
        $clone->letter = $letter;
        return $clone;
    }

    /**
     * @return string
     */
    public function getLetter()
    {
        return $this->letter;
    }

    /**
     * @param string $link
     * @return self
     */
    public function withlink($link)
    {
        if (!is_string($link)) {
            throw new InvalidArgumentException('$link must be a string!');
        }
        $clone = clone $this;
        $clone->link = $link;
        return $clone;
    }

    /**
     * @return string
     */
    public function getlink()
    {
        return $this->link;
    }

    /**
     * @param int $officeId
     * @return self
     */
    public function withOfficeId($officeId)
    {
        if (!is_int($officeId)) {
            throw new InvalidArgumentException('$officeId must be an integer!');
        }
        $clone = clone $this;
        $clone->officeId = $officeId;
        return $clone;
    }

    /**
     * @return int
     */
    public function getOfficeId()
    {
        return $this->officeId;
    }

    /**
     * @param bool $display
     * @return self
     */
    public function withDisplay($display)
    {
        if (!is_bool($display)) {
            throw new InvalidArgumentException('$display must be a boolean!');
        }
        $clone = clone $this;
        $clone->display = $display;
        return $clone;
    }

    /**
     * @return bool
     */
    public function getDisplay()
    {
        return $this->display;
    }

    /**
     * @param int $limit
     * @return self
     */
    public function withLimit($limit)
    {
        if (!is_int($limit)) {
            throw new InvalidArgumentException('$limit must be an integer!');
        }
        $clone = clone $this;
        $clone->limit = $limit;
        return $clone;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $page
     * @return self
     */
    public function withPage($page)
    {
        if (!is_int($page)) {
            throw new InvalidArgumentException('$page must be an integer!');
        }
        $clone = clone $this;
        $clone->page = $page;
        return $clone;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param array $order
     * @return self
     */
    public function withOrder($order)
    {
        if (!is_array($order)) {
            throw new InvalidArgumentException('$order must be an array!');
        }
        $clone = clone $this;
        $clone->order = $order;
        return $clone;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

}
