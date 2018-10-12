<?php

namespace REW\Backend\Dashboard;

use REW\Backend\Dashboard\Interfaces\EventIdInterface;
use REW\Backend\Dashboard\Interfaces\EventFactoryInterface;

/**
 * Class EventIdCollection
 *
 * @category Dashboard
 * @package  REW\Backend\Dashboard
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class EventIdCollection
{

    /**
     * Type of event ids in collection
     * @var string
     */
    protected $eventType;

    /**
     * List of event ids that can be loaded
     * @var EventIdInterface[]
     */
    protected $eventIds;

    /**
     * Event Count
     * @var int
     */
    protected $eventCount;

    /**
     * EventFactory used to translate EventId's to Events
     * @var EventFactoryInterface
     */
    protected $eventFactory;

    /**
     * The Event id to be next loaded
     * @var EventIdInterface
     */
    protected $currentEvent;

    /**
     * Get Event
     * @param string $eventType
     * @param array $eventIds
     * @param int $eventsCount
     * @param EventFactoryInterface $eventFactory
     */
    public function __construct($eventType, array $eventIds, $eventsCount, EventFactoryInterface $eventFactory)
    {
        $this->eventType    = $eventType;
        $this->eventIds     = $eventIds;
        $this->eventsCount  = $eventsCount;
        $this->eventFactory = $eventFactory;
        $this->currentEvent = 0;
    }

    /**
     * Return Event Type
     * @return string
     */
    public function getType()
    {
        return $this->eventType;
    }

    /**
     * Return Event Factory
     * @return EventFactoryInterface
     */
    public function getFactory()
    {
        return $this->eventFactory;
    }

    /**
     * Get the next event
     * @return EventIdInterface | null
     */
    public function getNextEvent()
    {
        return isset($this->eventIds[$this->currentEvent]) ? $this->eventIds[$this->currentEvent] : null;
    }

    /**
     * Get next event cursor
     * @return string | null
     */
    public function getNextCursor()
    {
        $nextEvent = $this->getNextEvent();
        return isset($nextEvent) ? $nextEvent->getCursor() : null;
    }

    /**
     * Get the timestamp of the next event
     * @return int | null
     */
    public function getNextTimestamp()
    {
        $nextEvent = $this->getNextEvent();
        return isset($nextEvent) ? $nextEvent->getTimestamp() : null;
    }

    /**
     * Iterate next event to check/fetch
     */
    public function iterateCurrentEvent()
    {
        $this->currentEvent = $this->currentEvent + 1;
    }

    /**
     * Reset event list to start
     */
    public function resetCurrentEvent()
    {
        $this->currentEvent = 0;
    }

    /**
     * Set event key
     * @param int $key
     */
    public function setCurrentEvent($key)
    {
        $this->currentEvent = $key;
    }

    /**
     * Get event key
     * @return int $key
     */
    public function getCurrentEvent()
    {
        return $this->currentEvent;
    }

    /**
     * Get number events which have not been loaded
     * @return int
     */
    public function getUnloadedEventCount()
    {
        return $this->eventsCount - $this->currentEvent;
    }
}
