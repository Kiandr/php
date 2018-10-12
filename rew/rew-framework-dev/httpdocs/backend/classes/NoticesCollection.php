<?php

namespace REW\Backend;

use REW\Backend\Interfaces\NoticesCollectionInterface;
use REW\Backend\Interfaces\SessionInterface;

class NoticesCollection implements NoticesCollectionInterface
{

    /**
     * Notices Structure
     * @var array
     */
    protected $notices = self::DEFAULT_NOTICES;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * Constucts a notices collection
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;

        $notices = $this->session->get('notices');

        if (!empty($notices) && is_string($notices)) {
            $this->notices = unserialize($notices);
        }
    }

    /**
     * Appends a notice to the notices collection
     * @param string $type
     * @param string $notice
     * @return void
     */
    public function add($type, $notice)
    {
        $this->notices[$type][] = $notice;

        $this->session->set('notices', serialize($this->notices));
    }

    /**
     * Adds a success notice
     * @param string $notice
     * @return void
     */
    public function success($notice)
    {
        $this->add(self::TYPE_SUCCESS, $notice);
    }

    /**
     * Adds a warning notice
     * @param string $notice
     * @return void
     */
    public function warning($notice)
    {
        $this->add(self::TYPE_WARNING, $notice);
    }

    /**
     * Adds an error notice
     * @param string $notice
     * @return void
     */
    public function error($notice)
    {
        $this->add(self::TYPE_ERROR, $notice);
    }

    /**
     * Returns the notices collection
     * @return array
     */
    public function getAll()
    {
        return $this->notices;
    }

    /**
     * Resets notices to its initial state.
     * @return void
     */
    public function clear()
    {
        $this->notices = self::DEFAULT_NOTICES;

        $this->session->set('notices', serialize($this->notices));
    }
}
