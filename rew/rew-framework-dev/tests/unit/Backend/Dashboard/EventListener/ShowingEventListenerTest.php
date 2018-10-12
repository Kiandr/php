<?php
namespace REW\Test\Backend\Dashboard\EventListener;

use Mockery as m;
use REW\Backend\Dashboard\EventListener\FormEvents\ShowingEventListener;
use REW\Backend\Dashboard\EventFactory\FormEvents\ShowingEventFactory;
use REW\Backend\Dashboard\EventId;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Backend\Auth\LeadsAuth;

class ShowingEventListenerTest extends \Codeception\Test\Unit
{

    protected function _before()
    {
        if (!defined('LM_TABLE_FORMS')) {
            define('LM_TABLE_FORMS', 'users_forms');
        }
        if (!defined('LM_TABLE_LEADS')) {
            define('LM_TABLE_LEADS', 'users');
        }

        $this->auth = m::mock(AuthInterface::class);
        $this->db = m::mock(DBInterface::class);
        $this->container = m::mock(ContainerInterface::class);
        $this->leadsAuth = m::mock(LeadsAuth::class);
    }

    protected function _after()
    {
        m::close();
    }

    /**
     * @covers \REW\Backend\Dashboard\EventListener\FormEvents\ShowingEventListener::__construct()
     * @covers \REW\Backend\Dashboard\EventListener\AbstractFormEventListener ::__construct()
     * @covers \REW\Backend\Dashboard\AbstractEventListener::__construct()
     */
    public function testContruct()
    {
        $showingEventListener = new ShowingEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);
        $this->assertInstanceOf('REW\Backend\Dashboard\EventListener\FormEvents\ShowingEventListener', $showingEventListener);
    }

    /**
     * @covers \REW\Backend\Dashboard\EventListener\FormEvents\ShowingEventListener::getMode()
     * @covers \REW\Backend\Dashboard\EventListener\AbstractFormEventListener ::getMode()
     * @covers \REW\Backend\Dashboard\AbstractEventListener::getMode()
     */
    public function testGetMode()
    {
        $showingEventListener = new ShowingEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);
        $this->assertEquals(ShowingEventListener::MODE, $showingEventListener->getMode());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventListener\FormEvents\ShowingEventListener::getFactory()
     * @covers \REW\Backend\Dashboard\EventListener\AbstractFormEventListener ::getFactory()
     * @covers \REW\Backend\Dashboard\AbstractEventListener::getFactory()
     */
    public function testGetFactory()
    {

        $factory= m::mock(ShowingEventFactory::class);
        $this->container->shouldReceive('get')
            ->with("REW\Backend\Dashboard\EventFactory\FormEvents\ShowingEventFactory")
            ->once()
            ->andReturn($factory);

        $showingEventListener = new ShowingEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);
        $this->assertSame($factory, $showingEventListener->getFactory());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventListener\FormEvents\ShowingEventListener::getEventsIds()
     * @covers \REW\Backend\Dashboard\EventListener\AbstractFormEventListener ::getEventsIds()
     * @covers \REW\Backend\Dashboard\AbstractEventListener::getEventsIds()
     */
    public function testGetEventsIds()
    {

        $userId = 4;

        $this->auth->shouldReceive('info')
            ->with('id')
            ->times(3)
            ->andReturn($userId);

        $this->leadsAuth->shouldReceive('canAssignLeads')
            ->once()
            ->andReturn(true);

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')
            ->once()
            ->with(array_merge([$userId, $userId], ShowingEventListener::SHOWING_FORMS, [4, 'showing']));
        $stmt->shouldReceive('fetchAll')
            ->once()
            ->andReturn([
                ['id' => "61", 'timestamp' => "2017-07-19 13:45:27", 'form' => "Quick Inquire", 'status' => "pending"],
                ['id' => "62", 'timestamp' => "2017-09-19 11:12:54", 'form' => "Buyer Form", 'status' => "pending"],
                ['id' => "65", 'timestamp' => "2017-09-20 12:25:17", 'form' => "CMA Form", 'status' => "pending"]
            ]);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with("SELECT `uf`.`id`, `uf`.`timestamp`, `uf`.`form`, `u`.`status` FROM users_forms `uf` JOIN users `u` ON `u`.`id` = `uf`.`user_id` WHERE `uf`.`reply` IS NULL AND (`u`.`agent` = ? OR `u`.`agent` = 1 OR (`u`.`timestamp_assigned` < DATE_SUB(NOW(), INTERVAL 1 DAY) AND `u`.`status` = 'pending')) AND (`u`.`site_type` != 'agent' OR `u`.`site` = ? ) AND (`uf`.`form` IN (?, ?) OR (`uf`.`form` = 'IDX Inquiry' AND `uf`.`data` LIKE '%s:12:\"inquire_type\";s:16:\"Property Showing\";%')) AND `uf`.`id` NOT IN (SELECT `dd`.`event_id` FROM `dashboard_dismissed` `dd` WHERE `dd`.`agent` = ? AND `dd`.`event_mode` = ? GROUP BY `dd`.`event_id`) ORDER BY `uf`.`timestamp` DESC, `uf`.`id` ASC LIMIT 21")
            ->andReturn($stmt);

        $showingEventListener = new ShowingEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);

        $eventIds = $showingEventListener->getEventsIds(20);
        $this->assertInternalType('array', $eventIds);
        $this->assertCount(3, $eventIds);
        $this->assertInstanceOf('REW\Backend\Dashboard\EventId', $eventIds[0]);
        $this->assertEquals(61, $eventIds[0]->getId());
        $this->assertEquals('showing', $eventIds[0]->getMode());
        $this->assertEquals('pending', $eventIds[0]->getStatus());
        $this->assertEquals(strtotime("2017-07-19 13:45:27"), $eventIds[0]->getTimestamp());
        $this->assertInstanceOf('REW\Backend\Dashboard\EventId', $eventIds[1]);
        $this->assertEquals(62, $eventIds[1]->getId());
        $this->assertEquals('showing', $eventIds[1]->getMode());
        $this->assertEquals('pending', $eventIds[0]->getStatus());
        $this->assertEquals(strtotime("2017-09-19 11:12:54"), $eventIds[1]->getTimestamp());
        $this->assertInstanceOf('REW\Backend\Dashboard\EventId', $eventIds[2]);
        $this->assertEquals(65, $eventIds[2]->getId());
        $this->assertEquals('showing', $eventIds[2]->getMode());
        $this->assertEquals('pending', $eventIds[0]->getStatus());
        $this->assertEquals(strtotime("2017-09-20 12:25:17"), $eventIds[2]->getTimestamp());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventListener\FormEvents\ShowingEventListener::getEventsIds()
     * @covers \REW\Backend\Dashboard\EventListener\AbstractFormEventListener ::getEventsIds()
     * @covers \REW\Backend\Dashboard\AbstractEventListener::getEventsIds()
     */
    public function testGetEventsIdsWithoutLimit()
    {

        $userId = 4;

        $this->auth->shouldReceive('info')
            ->with('id')
            ->times(3)
            ->andReturn($userId);

        $this->leadsAuth->shouldReceive('canAssignLeads')
            ->once()
            ->andReturn(true);

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')
            ->once()
            ->with(array_merge([$userId, $userId], ShowingEventListener::SHOWING_FORMS, [4, 'showing']));
        $stmt->shouldReceive('fetchAll')
            ->once()
            ->andReturn([
                ['id' => "61", 'timestamp' => "2017-07-19 13:45:27", 'form' => "Quick Inquire", 'status' => "pending"],
                ['id' => "62", 'timestamp' => "2017-09-19 11:12:54", 'form' => "Buyer Form", 'status' => "pending"],
                ['id' => "65", 'timestamp' => "2017-09-20 12:25:17", 'form' => "CMA Form", 'status' => "pending"]
            ]);

        $this->db->shouldReceive('prepare')
        ->once()
        ->with("SELECT `uf`.`id`, `uf`.`timestamp`, `uf`.`form`, `u`.`status` FROM users_forms `uf` JOIN users `u` ON `u`.`id` = `uf`.`user_id` WHERE `uf`.`reply` IS NULL AND (`u`.`agent` = ? OR `u`.`agent` = 1 OR (`u`.`timestamp_assigned` < DATE_SUB(NOW(), INTERVAL 1 DAY) AND `u`.`status` = 'pending')) AND (`u`.`site_type` != 'agent' OR `u`.`site` = ? ) AND (`uf`.`form` IN (?, ?) OR (`uf`.`form` = 'IDX Inquiry' AND `uf`.`data` LIKE '%s:12:\"inquire_type\";s:16:\"Property Showing\";%')) AND `uf`.`id` NOT IN (SELECT `dd`.`event_id` FROM `dashboard_dismissed` `dd` WHERE `dd`.`agent` = ? AND `dd`.`event_mode` = ? GROUP BY `dd`.`event_id`) ORDER BY `uf`.`timestamp` DESC, `uf`.`id` ASC")
        ->andReturn($stmt);

        $showingEventListener = new ShowingEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);

        $eventIds = $showingEventListener->getEventsIds();
        $this->assertInternalType('array', $eventIds);
    }

    /**
     * @covers \REW\Backend\Dashboard\EventListener\FormEvents\ShowingEventListener::getEventsIds()
     * @covers \REW\Backend\Dashboard\EventListener\AbstractFormEventListener ::getEventsIds()
     * @covers \REW\Backend\Dashboard\AbstractEventListener::getEventsIds()
     */
    public function testGetEventsIdsRestrictedToAgent()
    {

        $userId = 4;

        $this->auth->shouldReceive('info')
            ->with('id')
            ->times(3)
            ->andReturn($userId);

        $this->leadsAuth->shouldReceive('canAssignLeads')
            ->once()
            ->andReturn(false);

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')
            ->once()
            ->with(array_merge([$userId, $userId], ShowingEventListener::SHOWING_FORMS, [4, 'showing']));
        $stmt->shouldReceive('fetchAll')
            ->once()
            ->andReturn([
                ['id' => "61", 'timestamp' => "2017-07-19 13:45:27", 'form' => "Quick Inquire", 'status' => "pending"],
                ['id' => "62", 'timestamp' => "2017-09-19 11:12:54", 'form' => "Buyer Form", 'status' => "pending"],
                ['id' => "65", 'timestamp' => "2017-09-20 12:25:17", 'form' => "CMA Form", 'status' => "pending"]
            ]);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with("SELECT `uf`.`id`, `uf`.`timestamp`, `uf`.`form`, `u`.`status` FROM users_forms `uf` JOIN users `u` ON `u`.`id` = `uf`.`user_id` WHERE `uf`.`reply` IS NULL AND `u`.`agent` = ? AND (`u`.`site_type` != 'agent' OR `u`.`site` = ? ) AND (`uf`.`form` IN (?, ?) OR (`uf`.`form` = 'IDX Inquiry' AND `uf`.`data` LIKE '%s:12:\"inquire_type\";s:16:\"Property Showing\";%')) AND `uf`.`id` NOT IN (SELECT `dd`.`event_id` FROM `dashboard_dismissed` `dd` WHERE `dd`.`agent` = ? AND `dd`.`event_mode` = ? GROUP BY `dd`.`event_id`) ORDER BY `uf`.`timestamp` DESC, `uf`.`id` ASC LIMIT 21")
            ->andReturn($stmt);

        $showingEventListener = new ShowingEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);

        $eventIds = $showingEventListener->getEventsIds(20);
        $this->assertInternalType('array', $eventIds);
    }

    /**
     * @covers \REW\Backend\Dashboard\EventListener\FormEvents\ShowingEventListener::getNewerEventIds()
     * @covers \REW\Backend\Dashboard\EventListener\AbstractFormEventListener ::getNewerEventIds()
     * @covers \REW\Backend\Dashboard\AbstractEventListener::getNewerEventIds()
     */
    public function testGetNewerEventIds()
    {

        $userId = 4;

        $this->auth->shouldReceive('info')
            ->with('id')
            ->times(3)
            ->andReturn($userId);

        $this->leadsAuth->shouldReceive('canAssignLeads')
        ->once()
        ->andReturn(true);

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')
            ->once()
            ->with(array_merge([$userId, $userId], ShowingEventListener::SHOWING_FORMS, [date("Y-m-d H:i:s", intval(1500661420))], [4, 'showing']));
        $stmt->shouldReceive('fetchAll')
        ->once()
        ->andReturn([
            ['id' => "61", 'timestamp' => "2017-07-19 13:45:27", 'form' => "Quick Inquire", 'status' => "pending"],
            ['id' => "62", 'timestamp' => "2017-09-19 11:12:54", 'form' => "Buyer Form", 'status' => "pending"],
            ['id' => "65", 'timestamp' => "2017-09-20 12:25:17", 'form' => "CMA Form", 'status' => "pending"]
        ]);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with("SELECT `uf`.`id`, `uf`.`timestamp`, `uf`.`form`, `u`.`status` FROM users_forms `uf` JOIN users `u` ON `u`.`id` = `uf`.`user_id` WHERE `uf`.`reply` IS NULL AND (`u`.`agent` = ? OR `u`.`agent` = 1 OR (`u`.`timestamp_assigned` < DATE_SUB(NOW(), INTERVAL 1 DAY) AND `u`.`status` = 'pending')) AND (`u`.`site_type` != 'agent' OR `u`.`site` = ? ) AND (`uf`.`form` IN (?, ?) OR (`uf`.`form` = 'IDX Inquiry' AND `uf`.`data` LIKE '%s:12:\"inquire_type\";s:16:\"Property Showing\";%')) AND (`uf`.`timestamp` > ?) AND `uf`.`id` NOT IN (SELECT `dd`.`event_id` FROM `dashboard_dismissed` `dd` WHERE `dd`.`agent` = ? AND `dd`.`event_mode` = ? GROUP BY `dd`.`event_id`) ORDER BY `uf`.`timestamp` DESC, `uf`.`id` ASC")
            ->andReturn($stmt);

        $showingEventListener = new ShowingEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);

        $eventIds = $showingEventListener->getNewerEventIds(1500661420);
        $this->assertInternalType('array', $eventIds);
        $this->assertCount(3, $eventIds);
        $this->assertInstanceOf('REW\Backend\Dashboard\EventId', $eventIds[0]);
        $this->assertEquals(61, $eventIds[0]->getId());
        $this->assertEquals('showing', $eventIds[0]->getMode());
        $this->assertEquals('pending', $eventIds[0]->getStatus());
        $this->assertEquals(strtotime("2017-07-19 13:45:27"), $eventIds[0]->getTimestamp());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventListener\FormEvents\ShowingEventListener::getOlderEventIds()
     * @covers \REW\Backend\Dashboard\EventListener\AbstractFormEventListener ::getOlderEventIds()
     * @covers \REW\Backend\Dashboard\AbstractEventListener::getOlderEventIds()
     */
    public function testGetOlderEventIds()
    {

        $userId = 4;

        $this->auth->shouldReceive('info')
            ->with('id')
            ->times(3)
            ->andReturn($userId);

        $this->leadsAuth->shouldReceive('canAssignLeads')
            ->once()
            ->andReturn(true);

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')
            ->once()
            ->with(array_merge([$userId, $userId], ShowingEventListener::SHOWING_FORMS, ["2017-07-21 18:23:32", "2017-07-21 18:23:32",11], [4, 'showing']));
        $stmt->shouldReceive('fetchAll')
            ->once()
            ->andReturn([
                ['id' => "61", 'timestamp' => "2017-07-19 13:45:27", 'form' => "Quick Inquire", 'status' => "pending"],
                ['id' => "62", 'timestamp' => "2017-09-19 11:12:54", 'form' => "Buyer Form", 'status' => "pending"],
                ['id' => "65", 'timestamp' => "2017-09-20 12:25:17", 'form' => "CMA Form", 'status' => "pending"]
            ]);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with("SELECT `uf`.`id`, `uf`.`timestamp`, `uf`.`form`, `u`.`status` FROM users_forms `uf` JOIN users `u` ON `u`.`id` = `uf`.`user_id` WHERE `uf`.`reply` IS NULL AND (`u`.`agent` = ? OR `u`.`agent` = 1 OR (`u`.`timestamp_assigned` < DATE_SUB(NOW(), INTERVAL 1 DAY) AND `u`.`status` = 'pending')) AND (`u`.`site_type` != 'agent' OR `u`.`site` = ? ) AND (`uf`.`form` IN (?, ?) OR (`uf`.`form` = 'IDX Inquiry' AND `uf`.`data` LIKE '%s:12:\"inquire_type\";s:16:\"Property Showing\";%')) AND (`uf`.`timestamp` < ? OR (`uf`.`timestamp` = ? AND `uf`.`id` >= ?)) AND `uf`.`id` NOT IN (SELECT `dd`.`event_id` FROM `dashboard_dismissed` `dd` WHERE `dd`.`agent` = ? AND `dd`.`event_mode` = ? GROUP BY `dd`.`event_id`) ORDER BY `uf`.`timestamp` DESC, `uf`.`id` ASC LIMIT 21")
            ->andReturn($stmt);

        $showingEventListener = new ShowingEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);

        $eventIds = $showingEventListener->getOlderEventIds(1500661412, 11, 20);
        $this->assertInternalType('array', $eventIds);
        $this->assertCount(3, $eventIds);
        $this->assertInstanceOf('REW\Backend\Dashboard\EventId', $eventIds[0]);
        $this->assertEquals(61, $eventIds[0]->getId());
        $this->assertEquals('showing', $eventIds[0]->getMode());
        $this->assertEquals('pending', $eventIds[0]->getStatus());
        $this->assertEquals(strtotime("2017-07-19 13:45:27"), $eventIds[0]->getTimestamp());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventListener\FormEvents\ShowingEventListener::getEventsCount()
     * @covers \REW\Backend\Dashboard\EventListener\AbstractFormEventListener ::getEventsCount()
     * @covers \REW\Backend\Dashboard\AbstractEventListener::getEventsCount()
     */
    public function testGetEventsCount()
    {

        $userId = 4;

        $this->auth->shouldReceive('info')
            ->with('id')
            ->times(3)
            ->andReturn($userId);

        $this->leadsAuth->shouldReceive('canAssignLeads')
            ->once()
            ->andReturn(true);

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')
            ->once()
            ->with(array_merge([$userId, $userId], ShowingEventListener::SHOWING_FORMS, [4, 'showing']));
        $stmt->shouldReceive('fetchColumn')
            ->once()
            ->andReturn(78);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with("SELECT COUNT(`uf`.`id`) FROM users_forms `uf` JOIN users `u` ON `u`.`id` = `uf`.`user_id` WHERE `uf`.`reply` IS NULL AND (`u`.`agent` = ? OR `u`.`agent` = 1 OR (`u`.`timestamp_assigned` < DATE_SUB(NOW(), INTERVAL 1 DAY) AND `u`.`status` = 'pending')) AND (`u`.`site_type` != 'agent' OR `u`.`site` = ? ) AND (`uf`.`form` IN (?, ?) OR (`uf`.`form` = 'IDX Inquiry' AND `uf`.`data` LIKE '%s:12:\"inquire_type\";s:16:\"Property Showing\";%')) AND `uf`.`id` NOT IN (SELECT `dd`.`event_id` FROM `dashboard_dismissed` `dd` WHERE `dd`.`agent` = ? AND `dd`.`event_mode` = ? GROUP BY `dd`.`event_id`)")
            ->andReturn($stmt);

        $showingEventListener = new ShowingEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);
        $this->assertEquals(78, $showingEventListener->getEventsCount());
    }
}
