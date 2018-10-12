<?php

namespace REW\Backend\Interfaces\Page;

interface TimelineInterface
{

    /**
     * Parameter denoting
     * @var string
     */
    const MODE = 'timeline';

    /**
     * Parameter required to recreate page
     * @var array
     */
    const SAVED_VARS  = ['id', 'lead_id', 'agent', 'team', 'feed', 'category', 'filters', 'post_task', 'popup', 'sort', 'order', 'type', 'before', 'after'];

    /**
     * @param string $mode
     * @return string
     */
    public function getLink($mode);

    /**
     * @return string
     */
    public function encode();

    /**
     * @return TimelineInterface
     */
    public function getLast();

    /**
     * @param TimelineInterface $lastPage
     * @return void
     */
    public function setLast(TimelineInterface $lastPage);

    /**
     * Clear Timeline History
     */
    public function clearPast();

    /**
     * @param TimelineInterface $page
     * @return bool
     */
    public function compare(TimelineInterface $page);

    /**
     * Get the database record GUID for this page.
     * @return int|null
     */
    public function getGUID();

    /**
     * Get the url for this page.
     * @return string
     */
    public function getUrl();

    /**
     * Get the GET vars for this page.
     * @return array
     */
    public function getGet();
}
