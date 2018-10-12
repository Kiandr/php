<?php

namespace REW\Backend\Interfaces;

interface NoticesCollectionInterface
{

    /**
     * Success notice type
     * @var string
     */
    const TYPE_SUCCESS = 'success';

    /**
     * Warning notice type
     * @var string
     */
    const TYPE_WARNING = 'warning';

    /**
     * Error notice type
     * @var string
     */
    const TYPE_ERROR = 'error';

    /**
     * Default notices array structure
     * @var array
     */
    const DEFAULT_NOTICES = [
        self::TYPE_SUCCESS => [],
        self::TYPE_WARNING => [],
        self::TYPE_ERROR => []
    ];

    /**
     * Appends a notice to the notices collection
     * @param string $type
     * @param string $notice
     * @return void
     */
    public function add($type, $notice);

    /**
     * Adds a success notice
     * @param string $notice
     * @return void
     */
    public function success($notice);

    /**
     * Adds a warning notice
     * @param string $notice
     * @return void
     */
    public function warning($notice);

    /**
     * Adds an error notice
     * @param string $notice
     * @return void
     */
    public function error($notice);

    /**
     * Returns the notices collection
     * @return array
     */
    public function getAll();

    /**
     * Resets notices to its initial state.
     * @return void
     */
    public function clear();
}
