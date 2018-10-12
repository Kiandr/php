<?php

namespace REW\Backend\Dashboard;

use REW\Backend\Dashboard\Interfaces\EventIdInterface;

/**
 * Class Page
 *
 * @category Dashboard
 * @package  REW\Backend\Dashboard
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class EventId implements EventIdInterface
{

    /**
     * Event Id
     * @var int
     */
    protected $id;

    /**
     * Event Mode
     * @var string
     */
    protected $mode;

    /**
     * Lead Status
     * @var string
     */
    protected $status;

    /**
     * Event Timestamp
     * @var int
     */
    protected $timestamp;

    /**
     * Get Event
     * @param int $id
     * @param string $mode
     * @param string $status
     * @param int $timestamp
     */
    public function __construct($id, $mode, $status, $timestamp)
    {

        $this->id = $id;
        $this->mode = $mode;
        $this->status = $status;
        $this->timestamp = $timestamp;
    }

    /**
     * Return Event Id
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return Event Mode
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Return Event Lead Status
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Return Timestamp
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Return Hash
     * @return string
     */
    public function getHash()
    {
        return md5(serialize([$this->getMode(), $this->getStatus(), $this->getId()]));
    }

    /**
     * Return Cursor
     * @return string
     */
    public function getCursor()
    {
        return $this->getTimestamp() . '::' . $this->getId();
    }
}
