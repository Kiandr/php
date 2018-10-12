<?php

namespace REW\Backend\Controller\Calendar\Event;

use REW\Backend\Controller\AbstractController;
use REW\Backend\Exceptions\UnauthorizedPageException;
use REW\Backend\Exceptions\MissingId\Calendar\MissingEventException;
use REW\Backend\View\Interfaces\FactoryInterface;
use REW\Backend\Auth\CalendarAuth;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\LogInterface;
use \PDOException;
use \Exception;

/**
 * ViewController
 * @package REW\Backend\Controller\Calendar\Event
 */
class ViewController extends AbstractController
{

    /**
     * @var FactoryInterface
     */
    protected $view;

    /**
     * @var DBInterface
     */
    protected $db;

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var CalendarAuth
     */
    protected $calendarAuth;

    /**
     * @var LogInterface
     */
    protected $log;

    /**
     * ViewController constructor.
     * @param FactoryInterface $view
     * @param DBInterface $db
     * @param AuthInterface $auth
     * @param CalendarAuth $calendarAuth
     * @param LogInterface $log
     */
    public function __construct(
        FactoryInterface $view,
        DBInterface $db,
        AuthInterface $auth,
        CalendarAuth $calendarAuth,
        LogInterface $log
    ) {
        $this->view = $view;
        $this->db = $db;
        $this->auth = $auth;
        $this->calendarAuth = $calendarAuth;
        $this->log = $log;
    }

    /**
     * @throws UnauthorizedPageException If can't manage calendars
     * @throws MissingEventException If event not found
     * @throws Exception If PDO error occurs
     */
    public function __invoke()
    {
        // authorized to load calendars
        $this->canViewCalendar();

        // Limit to own events if can't manage all calendars
        $sql_agent = !$this->calendarAuth->canManageCalendars($this->auth);

        // Load Event
        try {
            $event_id = $this->getId();

            if (empty($event_id)) {
                throw new MissingEventException();
            }

            $event = $this->getEvent($event_id, $sql_agent);

            if (empty($event['id'])) {
                throw new MissingEventException();
            }

            $event['start_date'] = date('Y-m-d', $event['start']);
            $event['start_time'] = date('H:i', ($event['all_day'] == 'true' ? time() : $event['start']));

            $event['end_date'] = date('Y-m-d', $event['end']);
            $event['end_time'] = date('H:i', ($event['all_day'] == 'true' ? time() : $event['end']));
        } catch (PDOException $e) {
            $this->log->error($e);
            throw new Exception("Unable To Load The Requested Event");
        }

        // Calendar Event Types
        try {
            $types = $this->getTypes();
        } catch (PDOException $e) {
            $this->log->error($e);
            throw new Exception('Error Loading Calendar Event Types');
        }

        // Render template file
        echo $this->view->render('::pages/calendar/event/view', [
            'event' => $event,
            'types' => $types,
        ]);
    }

    /**
     * Get Id from Super Globals
     * @return string
     */
    public function getId()
    {
        return isset($_POST['id']) ? $_POST['id'] : $_GET['id'];
    }

    /**
     * @param string $event_id
     * @param boolean $sql_agent
     * @return \REW\Core\Interfaces\DB\QueryInterface
     */
    public function getEvent($event_id, $sql_agent = false)
    {
        $query = sprintf(
            "SELECT
                `t1`.`id`,
                `t1`.`title` AS `title`,
                `body`,
                `google_event_id`,
                `microsoft_event_id`,
                `t1`.`type`,
                `t3`.`id` as `date_id`,
                UNIX_TIMESTAMP(`t3`.`start`) AS `start`,
                UNIX_TIMESTAMP(`t3`.`end`) AS `end`,
                `t3`.`all_day`,
                GROUP_CONCAT(`t4`.`user`) AS `agents` 
                FROM `%s` `t1` 
                LEFT JOIN `%s` `t3` ON `t1`.`id` = `t3`.`event` 
                LEFT JOIN `%s` `t4` ON `t1`.`id` = `t4`.`event` AND `t4`.`type` = 'Agent'
                WHERE `t1`.`id` = ?" . (!empty($sql_agent) ? ' AND (`t4`.`user` = ? AND `t4`.`event` = ?)' : ''),
            TABLE_CALENDAR_EVENTS,
            TABLE_CALENDAR_DATES,
            TABLE_CALENDAR_ATTENDEES
        );

        $params = [$event_id];
        if (!empty($sql_agent)) {
            $params[] = $this->auth->info('id');
            $params[] = $event_id;
        }

        return $this->db->fetch($query, $params);
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->db->fetchAll(sprintf("SELECT `id` AS `value`, `title` FROM `%s`;", TABLE_CALENDAR_TYPES));
    }

    /**
     * @throws UnauthorizedPageException
     */
    public function canViewCalendar()
    {
        if (!$this->calendarAuth->canManageCalendars($this->auth)) {
            if (!$this->calendarAuth->canManageOwnCalendars($this->auth)) {
                throw new UnauthorizedPageException(
                    'You do not have permission to manage calendars.'
                );
            }
        }
    }
}
