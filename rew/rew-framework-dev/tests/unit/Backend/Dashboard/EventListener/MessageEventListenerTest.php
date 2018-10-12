<?php
namespace REW\Test\Backend\Dashboard\EventListener;

use Mockery as m;
use REW\Backend\Dashboard\EventListener\MessageEventListener;
use REW\Backend\Dashboard\EventFactory\MessageEventFactory;
use REW\Backend\Dashboard\EventId;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Backend\Auth\LeadsAuth;

class MessageEventListenerTest extends \Codeception\Test\Unit
{

    protected function _before()
    {
        if (!defined('LM_TABLE_MESSAGES')) {
            define('LM_TABLE_MESSAGES', 'users_messages');
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
     * @covers \REW\Backend\Dashboard\EventListener\MessageEventListener::__construct()
     * @covers \REW\Backend\Dashboard\AbstractEventListener::__construct()
     */
    public function testContruct()
    {
        $messageEventListener = new MessageEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);
        $this->assertInstanceOf('REW\Backend\Dashboard\EventListener\MessageEventListener', $messageEventListener);
    }

    /**
     * @covers \REW\Backend\Dashboard\EventListener\MessageEventListener::getMode()
     * @covers \REW\Backend\Dashboard\AbstractEventListener::getMode()
     */
    public function testGetMode()
    {
        $messageEventListener = new MessageEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);
        $this->assertEquals(MessageEventListener::MODE, $messageEventListener->getMode());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventListener\MessageEventListener::getFactory()
     * @covers \REW\Backend\Dashboard\AbstractEventListener::getFactory()
     */
    public function testGetFactory()
    {

        $factory= m::mock(MessageEventFactory::class);
        $this->container->shouldReceive('get')
            ->with("REW\Backend\Dashboard\EventFactory\MessageEventFactory")
            ->once()
            ->andReturn($factory);

        $messageEventListener = new MessageEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);
        $this->assertSame($factory, $messageEventListener->getFactory());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventListener\MessageEventListener::getEventsIds()
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
            ->with([$userId, $userId, $userId, 'message']);
        $stmt->shouldReceive('fetchAll')
            ->once()
            ->andReturn([
                ['id' => "61", 'timestamp' => "2017-07-19 13:45:27", 'agent' => "4", 'status' => "pending"],
                ['id' => "62", 'timestamp' => "2017-09-19 11:12:54", 'agent' => "4", 'status' => "pending"],
                ['id' => "65", 'timestamp' => "2017-09-20 12:25:17", 'agent' => "4", 'status' => "pending"]
            ]);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with("SELECT `um`.`id`, `u`.`agent`, `um`.`timestamp`, `u`.`status` FROM users_messages `um` JOIN users `u` ON `u`.`id` = `um`.`user_id` WHERE  `um`.`sent_from` = 'lead' AND `um`.`agent_read` = 'N' AND `um`.`user_del` = 'N' AND (`u`.`agent` = ? OR `u`.`agent` = 1 OR (`u`.`timestamp_assigned` < DATE_SUB(NOW(), INTERVAL 1 DAY) AND `u`.`status` = 'pending')) AND (`u`.`site_type` != 'agent' OR `u`.`site` = ? ) AND `um`.`id` NOT IN (SELECT `dd`.`event_id` FROM `dashboard_dismissed` `dd` WHERE `dd`.`agent` = ? AND `dd`.`event_mode` = ? GROUP BY `dd`.`event_id`) ORDER BY `um`.`timestamp` DESC, `um`.`id` ASC LIMIT 21")
            ->andReturn($stmt);

        $messageEventListener = new MessageEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);

        $eventIds = $messageEventListener->getEventsIds(20);
        $this->assertInternalType('array', $eventIds);
        $this->assertCount(3, $eventIds);
        $this->assertInstanceOf('REW\Backend\Dashboard\EventId', $eventIds[0]);
        $this->assertEquals(61, $eventIds[0]->getId());
        $this->assertEquals('message', $eventIds[0]->getMode());
        $this->assertEquals('pending', $eventIds[0]->getStatus());
        $this->assertEquals(strtotime("2017-07-19 13:45:27"), $eventIds[0]->getTimestamp());
        $this->assertInstanceOf('REW\Backend\Dashboard\EventId', $eventIds[1]);
        $this->assertEquals(62, $eventIds[1]->getId());
        $this->assertEquals('message', $eventIds[1]->getMode());
        $this->assertEquals('pending', $eventIds[0]->getStatus());
        $this->assertEquals(strtotime("2017-09-19 11:12:54"), $eventIds[1]->getTimestamp());
        $this->assertInstanceOf('REW\Backend\Dashboard\EventId', $eventIds[2]);
        $this->assertEquals(65, $eventIds[2]->getId());
        $this->assertEquals('message', $eventIds[2]->getMode());
        $this->assertEquals('pending', $eventIds[0]->getStatus());
        $this->assertEquals(strtotime("2017-09-20 12:25:17"), $eventIds[2]->getTimestamp());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventListener\MessageEventListener::getEventsIds()
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
            ->with([$userId, $userId, $userId, 'message']);
        $stmt->shouldReceive('fetchAll')
            ->once()
            ->andReturn([
                ['id' => "61", 'timestamp' => "2017-07-19 13:45:27", 'agent' => "4", 'status' => "pending"],
                ['id' => "62", 'timestamp' => "2017-09-19 11:12:54", 'agent' => "4", 'status' => "pending"],
                ['id' => "65", 'timestamp' => "2017-09-20 12:25:17", 'agent' => "4", 'status' => "pending"]
            ]);

        $this->db->shouldReceive('prepare')
        ->once()
        ->with("SELECT `um`.`id`, `u`.`agent`, `um`.`timestamp`, `u`.`status` FROM users_messages `um` JOIN users `u` ON `u`.`id` = `um`.`user_id` WHERE  `um`.`sent_from` = 'lead' AND `um`.`agent_read` = 'N' AND `um`.`user_del` = 'N' AND (`u`.`agent` = ? OR `u`.`agent` = 1 OR (`u`.`timestamp_assigned` < DATE_SUB(NOW(), INTERVAL 1 DAY) AND `u`.`status` = 'pending')) AND (`u`.`site_type` != 'agent' OR `u`.`site` = ? ) AND `um`.`id` NOT IN (SELECT `dd`.`event_id` FROM `dashboard_dismissed` `dd` WHERE `dd`.`agent` = ? AND `dd`.`event_mode` = ? GROUP BY `dd`.`event_id`) ORDER BY `um`.`timestamp` DESC, `um`.`id` ASC")
        ->andReturn($stmt);

        $messageEventListener = new MessageEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);

        $eventIds = $messageEventListener->getEventsIds();
        $this->assertInternalType('array', $eventIds);
    }

    /**
     * @covers \REW\Backend\Dashboard\EventListener\MessageEventListener::getEventsIds()
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
            ->with([$userId, $userId, $userId, 'message']);
        $stmt->shouldReceive('fetchAll')
            ->once()
            ->andReturn([
                ['id' => "61", 'timestamp' => "2017-07-19 13:45:27", 'agent' => "4", 'status' => "pending"],
                ['id' => "62", 'timestamp' => "2017-09-19 11:12:54", 'agent' => "4", 'status' => "pending"],
                ['id' => "65", 'timestamp' => "2017-09-20 12:25:17", 'agent' => "4", 'status' => "pending"]
            ]);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with("SELECT `um`.`id`, `u`.`agent`, `um`.`timestamp`, `u`.`status` FROM users_messages `um` JOIN users `u` ON `u`.`id` = `um`.`user_id` WHERE  `um`.`sent_from` = 'lead' AND `um`.`agent_read` = 'N' AND `um`.`user_del` = 'N' AND `u`.`agent` = ? AND (`u`.`site_type` != 'agent' OR `u`.`site` = ? ) AND `um`.`id` NOT IN (SELECT `dd`.`event_id` FROM `dashboard_dismissed` `dd` WHERE `dd`.`agent` = ? AND `dd`.`event_mode` = ? GROUP BY `dd`.`event_id`) ORDER BY `um`.`timestamp` DESC, `um`.`id` ASC LIMIT 21")
            ->andReturn($stmt);

        $messageEventListener = new MessageEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);

        $eventIds = $messageEventListener->getEventsIds(20);
        $this->assertInternalType('array', $eventIds);
    }

    /**
     * @covers \REW\Backend\Dashboard\EventListener\MessageEventListener::getNewerEventIds()
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
            ->with([$userId, $userId, "2017-07-21 18:23:40", $userId, 'message']);
        $stmt->shouldReceive('fetchAll')
        ->once()
        ->andReturn([
            ['id' => "61", 'timestamp' => "2017-07-19 13:45:27", 'agent' => "4", 'status' => "pending"],
            ['id' => "62", 'timestamp' => "2017-09-19 11:12:54", 'agent' => "4", 'status' => "pending"],
            ['id' => "65", 'timestamp' => "2017-09-20 12:25:17", 'agent' => "4", 'status' => "pending"]
        ]);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with("SELECT `um`.`id`, `u`.`agent`, `um`.`timestamp`, `u`.`status` FROM users_messages `um` JOIN users `u` ON `u`.`id` = `um`.`user_id` WHERE  `um`.`sent_from` = 'lead' AND `um`.`agent_read` = 'N' AND `um`.`user_del` = 'N' AND (`u`.`agent` = ? OR `u`.`agent` = 1 OR (`u`.`timestamp_assigned` < DATE_SUB(NOW(), INTERVAL 1 DAY) AND `u`.`status` = 'pending')) AND (`u`.`site_type` != 'agent' OR `u`.`site` = ? ) AND (`um`.`timestamp` > ?) AND `um`.`id` NOT IN (SELECT `dd`.`event_id` FROM `dashboard_dismissed` `dd` WHERE `dd`.`agent` = ? AND `dd`.`event_mode` = ? GROUP BY `dd`.`event_id`) ORDER BY `um`.`timestamp` DESC, `um`.`id` ASC")
            ->andReturn($stmt);

        $messageEventListener = new MessageEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);

        $eventIds = $messageEventListener->getNewerEventIds(1500661420);
        $this->assertInternalType('array', $eventIds);
        $this->assertCount(3, $eventIds);
        $this->assertInstanceOf('REW\Backend\Dashboard\EventId', $eventIds[0]);
        $this->assertEquals(61, $eventIds[0]->getId());
        $this->assertEquals('message', $eventIds[0]->getMode());
        $this->assertEquals('pending', $eventIds[0]->getStatus());
        $this->assertEquals(strtotime("2017-07-19 13:45:27"), $eventIds[0]->getTimestamp());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventListener\MessageEventListener::getOlderEventIds()
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
            ->with([$userId, $userId, "2017-07-21 18:23:32", "2017-07-21 18:23:32", 11, $userId, 'message']);
        $stmt->shouldReceive('fetchAll')
            ->once()
            ->andReturn([
                ['id' => "61", 'timestamp' => "2017-07-19 13:45:27", 'agent' => "4", 'status' => "pending"],
                ['id' => "62", 'timestamp' => "2017-09-19 11:12:54", 'agent' => "4", 'status' => "pending"],
                ['id' => "65", 'timestamp' => "2017-09-20 12:25:17", 'agent' => "4", 'status' => "pending"]
            ]);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with("SELECT `um`.`id`, `u`.`agent`, `um`.`timestamp`, `u`.`status` FROM users_messages `um` JOIN users `u` ON `u`.`id` = `um`.`user_id` WHERE  `um`.`sent_from` = 'lead' AND `um`.`agent_read` = 'N' AND `um`.`user_del` = 'N' AND (`u`.`agent` = ? OR `u`.`agent` = 1 OR (`u`.`timestamp_assigned` < DATE_SUB(NOW(), INTERVAL 1 DAY) AND `u`.`status` = 'pending')) AND (`u`.`site_type` != 'agent' OR `u`.`site` = ? ) AND (`um`.`timestamp` < ? OR (`um`.`timestamp` = ? AND `um`.`id` >= ?)) AND `um`.`id` NOT IN (SELECT `dd`.`event_id` FROM `dashboard_dismissed` `dd` WHERE `dd`.`agent` = ? AND `dd`.`event_mode` = ? GROUP BY `dd`.`event_id`) ORDER BY `um`.`timestamp` DESC, `um`.`id` ASC LIMIT 21")
            ->andReturn($stmt);

        $messageEventListener = new MessageEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);

        $eventIds = $messageEventListener->getOlderEventIds(1500661412, 11, 20);
        $this->assertInternalType('array', $eventIds);
        $this->assertCount(3, $eventIds);
        $this->assertInstanceOf('REW\Backend\Dashboard\EventId', $eventIds[0]);
        $this->assertEquals(61, $eventIds[0]->getId());
        $this->assertEquals('message', $eventIds[0]->getMode());
        $this->assertEquals('pending', $eventIds[0]->getStatus());
        $this->assertEquals(strtotime("2017-07-19 13:45:27"), $eventIds[0]->getTimestamp());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventListener\MessageEventListener::getEventsCount()
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
            ->with([$userId, $userId, $userId, 'message']);
        $stmt->shouldReceive('fetchColumn')
            ->once()
            ->andReturn(78);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with("SELECT COUNT(`um`.`id`) FROM users_messages `um` JOIN users `u` ON `u`.`id` = `um`.`user_id` WHERE  `um`.`sent_from` = 'lead' AND `um`.`agent_read` = 'N' AND `um`.`user_del` = 'N' AND (`u`.`agent` = ? OR `u`.`agent` = 1 OR (`u`.`timestamp_assigned` < DATE_SUB(NOW(), INTERVAL 1 DAY) AND `u`.`status` = 'pending')) AND (`u`.`site_type` != 'agent' OR `u`.`site` = ? ) AND `um`.`id` NOT IN (SELECT `dd`.`event_id` FROM `dashboard_dismissed` `dd` WHERE `dd`.`agent` = ? AND `dd`.`event_mode` = ? GROUP BY `dd`.`event_id`)")
            ->andReturn($stmt);

        $messageEventListener = new MessageEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);
        $this->assertEquals(78, $messageEventListener->getEventsCount());
    }
}
