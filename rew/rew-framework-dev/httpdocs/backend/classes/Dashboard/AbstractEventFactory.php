<?php

namespace REW\Backend\Dashboard;

use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\FormatInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Backend\Dashboard\Interfaces\EventFactoryInterface;
use REW\Backend\Dashboard\Interfaces\EventIdInterface;
use REW\Core\Interfaces\CacheInterface;

/**
 * Class AbstractEventFactory
 *
 * @category Dashboard
 * @package  REW\Backend\Dashboard
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
abstract class AbstractEventFactory implements EventFactoryInterface
{

    /**
     * @var DBInterface
     */
    protected $db;

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     *
     * @var CacheInterface
     */
    protected $cache;

    /**
     *
     * @var FormatInterface
     */
    protected $format;

    /**
     * Create AbstractEventFactory
     * @param DBInterface $db
     * @param AuthInterface $auth
     * @param SettingsInterface $settings
     * @param FormatInterface $format
     * @param CacheInterface $cache
     */
    public function __construct(
        DBInterface $db,
        AuthInterface $auth,
        SettingsInterface $settings,
        FormatInterface $format,
        CacheInterface $cache
    ) {
        $this->db = $db;
        $this->auth = $auth;
        $this->settings = $settings;
        $this->format = $format;
        $this->cache = $cache;
    }

    /**
     * Get Event Factory Mode
     */
    abstract public function getMode();

    /**
     * Query Events
     * @param EventIdInterface $eventId
     * @return array | Null
     */
    public function getEvent(EventIdInterface $eventId)
    {

        // Set up event hash
        $event = [];
        $event['hash'] = $eventId->getHash();

        // Load From Cache
        $cached = $this->loadCachedEvent($event['hash']);
        if (!empty($cached)) {
            return $cached;
        }

        // Set up event timeline
        $event['mode']      = $eventId->getMode();
        $event['timestamp'] = $eventId->getTimestamp();

        // Set up data
        $event['data'] = [];

        // Get Event Data
        $eventData = $this->queryEvent($eventId);

        // Skip missing data
        if (empty($eventData)) {
            return null;
        }

        // Parse Event
        $event = $this->parseEvent($event, $eventData);

        // Cache and Return Form Event
        $this->cacheEvent($event);
        return $event;
    }

    /**
     * Query Event Data
     * @param EventIdInterface $eventId
     * @return array|null
     */
    abstract protected function queryEvent(EventIdInterface $eventId);

    /**
     * Parse Event from Query Results
     * @param array $event
     * @param array $eventData
     * @return array
     */
    abstract protected function parseEvent(array $event, array $eventData);

    /**
     * Load Cached Event If Caching Is Enabled
     * @param int
     * @return array | NULL
     */
    protected function loadCachedEvent($index)
    {

        if ($this->cache instanceof CacheInterface && !empty($index)) {
            $cached = $this->cache->getCache(__CLASS__ . ':' . $index);
            return $cached;
        }
        return null;
    }

    /**
     * Cache's The Parsed Event If Caching Is Enabled
     * @param array $event
     */
    protected function cacheEvent(array $event)
    {

        if ($this->cache instanceof CacheInterface && !empty($event)) {
            $this->cache->setCache(__CLASS__ . ':' . $event['hash'], $event, false, self::CACHE_EXPIRES);
        }
    }

    /**
     * Parse Event Id
     * @param array $lead
     * @return array
     */
    protected function parseEventLead(array $lead)
    {
        return [
            'id' => $lead['user_id'],
            'name' => htmlspecialchars(trim(implode(' ', [$lead['first_name'], $lead['last_name']]))),
            'email' => $lead['email'],
            'emailLink' => URL_BACKEND . 'email/?id=' . $lead['user_id'] . '&type=leads&redirect=' . URL_BACKEND,
            'phone' => $lead['phone_cell'],
            'phoneLink' => (!empty($lead['phone_cell'])
                ? 'tel:+' . implode('-', explode(' ', $lead['phone_cell']))
                : null),
            'link' => URL_BACKEND . 'leads/lead/summary/?id=' . $lead['user_id'],
            'agent' => $lead['agent'],
            'agentName' => !empty($lead['agent_name']) ? htmlspecialchars(trim($lead['agent_name'])) : null,
            'agentLink' => URL_BACKEND . 'agents/agent/summary/?id=' . $lead['agent'],
            'defaultClass' => strtolower($lead['last_name'][0]),
            'defaultText' => strtoupper($lead['first_name'][0] . $lead['last_name'][0]),
            'image' => (!empty($lead['image'])
                ? '/thumbs/312x312/uploads/leads/' . $lead['image']
                : null),
            'status' => $lead['status']
        ];
    }
}
