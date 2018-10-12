<?php

namespace REW\Backend\Controller\Dashboard;

use REW\Backend\Controller\AbstractController;
use REW\Backend\Exceptions\PageNotFoundException;
use REW\Backend\Exceptions\UnauthorizedPageException;
use REW\Backend\Dashboard\EventListener\FormEvents\InquiryEventListener;
use REW\Backend\Dashboard\EventListener\FormEvents\SellingEventListener;
use REW\Backend\Dashboard\EventListener\FormEvents\ShowingEventListener;
use REW\Backend\Dashboard\EventListener\MessageEventListener;
use REW\Backend\Dashboard\EventListener\RegistrationEventListener;
use REW\Backend\View\Interfaces\FactoryInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\LogInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Backend\Auth\DashboardAuth;
use REW\Backend\Dashboard\EventIdCollection;

/**
 * IndexController
 * @package REW\Backend\Controller\Dashboard
 */
class IndexController extends AbstractController
{

    /**
     * Limit number of results loaded at once
     * @var integer
     */
    const LIMIT = 30;

    /**
     * @var FactoryInterface
     */
    protected $view;

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var DBInterface
     */
    protected $db;

    /**
     * @var LogInterface
     */
    protected $log;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var DashboardAuth
     */
    protected $dashboardAuth;

    /**
     * @param FactoryInterface $view
     * @param AuthInterface $auth
     * @param DBInterface $db
     * @param SettingsInterface $settings
     * @param LogInterface $log
     * @param DashboardAuth $dashboardAuth
     * @param InquiryEventListener $inquiryEventListener
     * @param SellingEventListener $sellingEventListener
     * @param ShowingEventListener $showingEventListener
     * @param MessageEventListener $messageEventListener
     * @param RegistrationEventListener $registrationEventListener
     */
    public function __construct(
        FactoryInterface $view,
        AuthInterface $auth,
        DBInterface $db,
        SettingsInterface $settings,
        LogInterface $log,
        DashboardAuth $dashboardAuth,
        InquiryEventListener $inquiryEventListener,
        SellingEventListener $sellingEventListener,
        ShowingEventListener $showingEventListener,
        MessageEventListener $messageEventListener,
        RegistrationEventListener $registrationEventListener
    ) {
        $this->view = $view;
        $this->auth = $auth;
        $this->db = $db;
        $this->settings = $settings;
        $this->log = $log;
        $this->dashboardAuth= $dashboardAuth;
        $this->inquiryEventListener = $inquiryEventListener;
        $this->sellingEventListener = $sellingEventListener;
        $this->showingEventListener = $showingEventListener;
        $this->messageEventListener = $messageEventListener;
        $this->registrationEventListener = $registrationEventListener;
    }

    /**
     * @throws UnauthorizedPageException If auth user does not have permission to view dashboard
     * @throws PageNotFoundException If invalid filter selected
     */
    public function __invoke()
    {

        if ($this->auth->isAssociate() || $this->auth->isLender()) {
            header('Location: /backend/leads/', 301);
            exit;
        }

        // Check Authorization
        $this->canViewDashboard();

        // Get Current Time
        $timestamp = time();

        // Get Event Data
        $eventListeners = $this->getEventListeners();
        $eventCollections = $this->getEventCollections($eventListeners);
        $events = $this->getLoadedEventData($eventCollections);

        // Render template file
        echo $this->view->render('::pages/dashboard/default', [
            'events' => str_replace(['\'', '&quot;'], ['&#x27;', '\&quot;'], (json_encode($events))),
            'apiKey' => $this->getApiKey(),
            'timestamp' => json_encode($timestamp),
            'authId' => $this->auth->info('id'),
            'unloaded' => $this->getUnloadedEventData($eventCollections)
        ]);
    }

    /**
     * Authorized to View Dashboard
     * @throws UnauthorizedPageException
     */
    public function canViewDashboard()
    {
        if (!$this->dashboardAuth->canViewDashboard()) {
            throw new UnauthorizedPageException('You do not have permission to view the dashboard');
        }
    }

    /**
     * Get Event Listeners
     * @return NULL[]
     */
    public function getEventListeners()
    {
        return [
            $this->inquiryEventListener,
            $this->sellingEventListener,
            $this->showingEventListener,
            $this->messageEventListener,
            $this->registrationEventListener
        ];
    }

    /**
     * Get Event Data From Listener
     * @param array $eventListeners
     * @return array[]
     */
    public function getEventCollections(array $eventListeners)
    {
        $eventCollections = [];
        foreach ($eventListeners as $eventListener) {
            $eventCollection = new EventIdCollection(
                $eventListener->getMode(),
                $eventListener->getEventsIds(self::LIMIT),
                $eventListener->getEventsCount(),
                $eventListener->getFactory()
            );
            $eventCollections[] = $eventCollection;
        }
        return $eventCollections;
    }

    /**
     * Get Next Event Collection
     * @param array $eventsData
     * @return NULL|EventIdCollection
     */
    public function getNextEventCollection(array $eventsData)
    {
        $nextEventCollection = $nextEventTimestamp = null;
        foreach ($eventsData as $eventCollection) {
            $eventTimestamp = $eventCollection->getNextTimestamp();

            if (!isset($eventTimestamp)) {
                continue;
            }
            if (!isset($nextEventTimestamp) || $eventTimestamp > $nextEventTimestamp) {
                $nextEventCollection = $eventCollection;
                $nextEventTimestamp = $eventTimestamp;
            }
        }
        return $nextEventCollection;
    }

    /**
     * Get Unloaded Data
     * @param array $eventsData
     * @return []
     */
    public function getLoadedEventData(array $eventsData)
    {

        $events = [];
        for ($i = 0; $i< self::LIMIT; $i++) {
            $nextCollection = $this->getNextEventCollection($eventsData);
            if (!isset($nextCollection)) {
                break;
            }

            // Load Full Event
            $eventId = $nextCollection->getNextEvent();
            $eventFactory = $nextCollection->getFactory();
            $events[] = $eventFactory->getEvent($eventId);

            // Iterate Collection
            $nextCollection->iterateCurrentEvent();
        }
        return $events;
    }

    /**
     * Get Unloaded Data
     * @param array $eventsData
     * @return array
     */
    public function getUnloadedEventData($eventsData)
    {

        // Gut Unloaded Event Data
        $unloadedEventData = [];
        foreach ($eventsData as $eventCollection) {
            // Get Cursor & Count Names
            $type = $eventCollection->getType();
            $cursorName = 'nextUnloaded' . ucfirst(strtolower($type));
            $countName = 'unloaded' . ucfirst(strtolower($type)) . 'Count';

            $unloadedEventData[$countName] = $eventCollection->getUnloadedEventCount();
            $cursor = $eventCollection->getNextCursor();
            if (isset($cursor)) {
                $unloadedEventData[$cursorName] = $cursor;
            }
        }

        // Return Parsed Data Array
        return $unloadedEventData;
    }

    /**
     * Get Google Maps API Key
     * @return string|null
     */
    public function getApiKey()
    {
        return $this->settings->get('google.maps.api_key');
    }
}
