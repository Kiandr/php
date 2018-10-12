<?php

namespace REW\Backend\Dashboard\Interfaces;

/**
 * IUnterface EventIdInterface
 *
 * @category Dashboard
 * @package  REW\Backend\Dashboard
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
interface EventIdInterface
{

    /**
     * Return Event Id
     * @return int
     */
    public function getId();

    /**
     * Return Event Mode
     * @return string
     */
    public function getMode();

    /**
     * Return Timestamp
     * @return int
     */
    public function getTimestamp();

    /**
     * Return Cursor
     * @return int
     */
    public function getCursor();

    /**
     * Return Hash
     * @return string
     */
    public function getHash();
}
