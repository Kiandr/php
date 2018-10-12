<?php

namespace REW\Backend\Dashboard\Interfaces;

use REW\Backend\Dashboard\Interfaces\EventIdInterface;

/**
 * EventFactoryInterface
 *
 * @category Dashboard
 * @package  REW\Backend\Dashboard
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
interface EventFactoryInterface
{

    /**
     * 24 Hours In Seconds
     * @var integer
     */
    const CACHE_EXPIRES = 86400;

    /**
     * Get Event Factory Mode
     * @return string
     */
    public function getMode();

    /**
     * Creates Event From Id
     * @param EventIdInterface $eventId
     * @return array
     */
    public function getEvent(EventIdInterface $eventId);
}
