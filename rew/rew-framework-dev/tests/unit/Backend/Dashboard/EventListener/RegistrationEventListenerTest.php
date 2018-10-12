<?php
namespace REW\Test\Backend\Dashboard\EventListener;

use Mockery as m;
use REW\Backend\Dashboard\EventListener\RegistrationEventListener;
use REW\Backend\Dashboard\EventFactory\RegistrationEventFactory;
use REW\Backend\Dashboard\EventId;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Backend\Auth\LeadsAuth;

class RegistrationEventListenerTest extends \Codeception\Test\Unit
{

    protected function _before()
    {
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
     * @covers \REW\Backend\Dashboard\EventListener\RegistrationEventListener::__construct()
     * @covers \REW\Backend\Dashboard\AbstractEventListener::__construct()
     */
    public function testContruct()
    {
        $registrationEventListener = new RegistrationEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);
        $this->assertInstanceOf('REW\Backend\Dashboard\EventListener\RegistrationEventListener', $registrationEventListener);
    }

    /**
     * @covers \REW\Backend\Dashboard\EventListener\RegistrationEventListener::getMode()
     * @covers \REW\Backend\Dashboard\AbstractEventListener::getMode()
     */
    public function testGetMode()
    {
        $registrationEventListener = new RegistrationEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);
        $this->assertEquals(RegistrationEventListener::MODE, $registrationEventListener->getMode());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventListener\RegistrationEventListener::getFactory()
     * @covers \REW\Backend\Dashboard\AbstractEventListener::getFactory()
     */
    public function testGetFactory()
    {

        $factory= m::mock(RegistrationEventFactory::class);
        $this->container->shouldReceive('get')
            ->with("REW\Backend\Dashboard\EventFactory\RegistrationEventFactory")
            ->once()
            ->andReturn($factory);

        $registrationEventListener = new RegistrationEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);
        $this->assertSame($factory, $registrationEventListener->getFactory());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventListener\RegistrationEventListener::getEventsIds()
     * @covers \REW\Backend\Dashboard\AbstractEventListener::getEventsIds()
     */
    public function testGetEventsIds()
    {

        $userId = 4;

        $this->auth->shouldReceive('info')
            ->with('id')
            ->times(3)
            ->andReturn($userId);

        $this->leadsAuth->shouldReceive('canManageLeads')
            ->once()
            ->andReturn(true);

        $this->leadsAuth->shouldReceive('canAssignLeads')
            ->once()
            ->andReturn(true);

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')
            ->once()
            ->with([$userId, $userId, $userId, 'register']);
        $stmt->shouldReceive('fetchAll')
            ->once()
            ->andReturn([
                ['id' => "61", 'timestamp' => "2017-07-19 13:45:27", 'status' => "pending"],
                ['id' => "62", 'timestamp' => "2017-09-19 11:12:54", 'status' => "pending"],
                ['id' => "65", 'timestamp' => "2017-09-20 12:25:17", 'status' => "pending"]
            ]);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with("SELECT `id`, (CASE WHEN `timestamp` > `timestamp_assigned` THEN `timestamp` ELSE `timestamp_assigned` END) AS 'timestamp', `status` FROM `users` `u` WHERE  (`status` = 'pending' OR `status` = 'unassigned') AND (`agent` = ? OR `agent` = 1 OR (`timestamp_assigned` < DATE_SUB(NOW(), INTERVAL 1 DAY) AND `status` = 'pending')) AND (`u`.`site_type` != 'agent' OR `u`.`site` = ? ) AND `u`.`id` NOT IN (SELECT `dd`.`event_id` FROM `dashboard_dismissed` `dd` WHERE `dd`.`agent` = ? AND `dd`.`event_mode` = ? GROUP BY `dd`.`event_id`) ORDER BY `timestamp` DESC, `id` ASC LIMIT 21")
            ->andReturn($stmt);

        $registrationEventListener = new RegistrationEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);

        $eventIds = $registrationEventListener->getEventsIds(20);
        $this->assertInternalType('array', $eventIds);
        $this->assertCount(3, $eventIds);
        $this->assertInstanceOf('REW\Backend\Dashboard\EventId', $eventIds[0]);
        $this->assertEquals(61, $eventIds[0]->getId());
        $this->assertEquals('register', $eventIds[0]->getMode());
        $this->assertEquals('pending', $eventIds[0]->getStatus());
        $this->assertEquals(strtotime("2017-07-19 13:45:27"), $eventIds[0]->getTimestamp());
        $this->assertInstanceOf('REW\Backend\Dashboard\EventId', $eventIds[1]);
        $this->assertEquals(62, $eventIds[1]->getId());
        $this->assertEquals('register', $eventIds[1]->getMode());
        $this->assertEquals('pending', $eventIds[0]->getStatus());
        $this->assertEquals(strtotime("2017-09-19 11:12:54"), $eventIds[1]->getTimestamp());
        $this->assertInstanceOf('REW\Backend\Dashboard\EventId', $eventIds[2]);
        $this->assertEquals(65, $eventIds[2]->getId());
        $this->assertEquals('register', $eventIds[2]->getMode());
        $this->assertEquals('pending', $eventIds[0]->getStatus());
        $this->assertEquals(strtotime("2017-09-20 12:25:17"), $eventIds[2]->getTimestamp());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventListener\RegistrationEventListener::getEventsIds()
     * @covers \REW\Backend\Dashboard\AbstractEventListener::getEventsIds()
     */
    public function testGetEventsIdsWithoutLimit()
    {

        $userId = 4;

        $this->auth->shouldReceive('info')
            ->with('id')
            ->times(3)
            ->andReturn($userId);

        $this->leadsAuth->shouldReceive('canManageLeads')
            ->once()
            ->andReturn(true);

        $this->leadsAuth->shouldReceive('canAssignLeads')
            ->once()
            ->andReturn(true);

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')
            ->once()
            ->with([$userId, $userId, $userId, 'register']);
        $stmt->shouldReceive('fetchAll')
            ->once()
            ->andReturn([
                ['id' => "61", 'timestamp' => "2017-07-19 13:45:27", 'status' => "pending"],
                ['id' => "62", 'timestamp' => "2017-09-19 11:12:54", 'status' => "pending"],
                ['id' => "65", 'timestamp' => "2017-09-20 12:25:17", 'status' => "pending"]
            ]);

        $this->db->shouldReceive('prepare')
        ->once()
        ->with("SELECT `id`, (CASE WHEN `timestamp` > `timestamp_assigned` THEN `timestamp` ELSE `timestamp_assigned` END) AS 'timestamp', `status` FROM `users` `u` WHERE  (`status` = 'pending' OR `status` = 'unassigned') AND (`agent` = ? OR `agent` = 1 OR (`timestamp_assigned` < DATE_SUB(NOW(), INTERVAL 1 DAY) AND `status` = 'pending')) AND (`u`.`site_type` != 'agent' OR `u`.`site` = ? ) AND `u`.`id` NOT IN (SELECT `dd`.`event_id` FROM `dashboard_dismissed` `dd` WHERE `dd`.`agent` = ? AND `dd`.`event_mode` = ? GROUP BY `dd`.`event_id`) ORDER BY `timestamp` DESC, `id` ASC")
        ->andReturn($stmt);

        $registrationEventListener = new RegistrationEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);

        $eventIds = $registrationEventListener->getEventsIds();
        $this->assertInternalType('array', $eventIds);
    }

    /**
     * @covers \REW\Backend\Dashboard\EventListener\RegistrationEventListener::getEventsIds()
     * @covers \REW\Backend\Dashboard\AbstractEventListener::getEventsIds()
     */
    public function testGetEventsIdsForPermittedManagers()
    {

        $userId = 4;

        $this->auth->shouldReceive('info')
            ->with('id')
            ->times(3)
            ->andReturn($userId);

        $this->leadsAuth->shouldReceive('canManageLeads')
            ->once()
            ->andReturn(false);

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')
            ->once()
            ->with([$userId, $userId, $userId, 'register']);
        $stmt->shouldReceive('fetchAll')
            ->once()
            ->andReturn([
                ['id' => "61", 'timestamp' => "2017-07-19 13:45:27", 'status' => "pending"],
                ['id' => "62", 'timestamp' => "2017-09-19 11:12:54", 'status' => "pending"],
                ['id' => "65", 'timestamp' => "2017-09-20 12:25:17", 'status' => "pending"]
            ]);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with("SELECT `id`, (CASE WHEN `timestamp` > `timestamp_assigned` THEN `timestamp` ELSE `timestamp_assigned` END) AS 'timestamp', `status` FROM `users` `u` WHERE  (`status` = 'pending' OR `status` = 'unassigned') AND `agent` = ? AND (`u`.`site_type` != 'agent' OR `u`.`site` = ? ) AND `u`.`id` NOT IN (SELECT `dd`.`event_id` FROM `dashboard_dismissed` `dd` WHERE `dd`.`agent` = ? AND `dd`.`event_mode` = ? GROUP BY `dd`.`event_id`) ORDER BY `timestamp` DESC, `id` ASC LIMIT 21")
            ->andReturn($stmt);

        $registrationEventListener = new RegistrationEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);

        $eventIds = $registrationEventListener->getEventsIds(20);
        $this->assertInternalType('array', $eventIds);
    }

    /**
     * @covers \REW\Backend\Dashboard\EventListener\RegistrationEventListener::getEventsIds()
     * @covers \REW\Backend\Dashboard\AbstractEventListener::getEventsIds()
     */
    public function testGetEventsIdsForPermittedAssigners()
    {

        $userId = 4;

        $this->auth->shouldReceive('info')
            ->with('id')
            ->times(3)
            ->andReturn($userId);

        $this->leadsAuth->shouldReceive('canManageLeads')
            ->once()
            ->andReturn(true);

        $this->leadsAuth->shouldReceive('canAssignLeads')
            ->once()
            ->andReturn(false);

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')
            ->once()
            ->with([$userId, $userId, $userId, 'register']);
        $stmt->shouldReceive('fetchAll')
            ->once()
            ->andReturn([
                ['id' => "61", 'timestamp' => "2017-07-19 13:45:27", 'status' => "pending"],
                ['id' => "62", 'timestamp' => "2017-09-19 11:12:54", 'status' => "pending"],
                ['id' => "65", 'timestamp' => "2017-09-20 12:25:17", 'status' => "pending"]
            ]);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with("SELECT `id`, (CASE WHEN `timestamp` > `timestamp_assigned` THEN `timestamp` ELSE `timestamp_assigned` END) AS 'timestamp', `status` FROM `users` `u` WHERE  (`status` = 'pending' OR `status` = 'unassigned') AND `agent` = ? AND (`u`.`site_type` != 'agent' OR `u`.`site` = ? ) AND `u`.`id` NOT IN (SELECT `dd`.`event_id` FROM `dashboard_dismissed` `dd` WHERE `dd`.`agent` = ? AND `dd`.`event_mode` = ? GROUP BY `dd`.`event_id`) ORDER BY `timestamp` DESC, `id` ASC LIMIT 21")
            ->andReturn($stmt);

        $registrationEventListener = new RegistrationEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);

        $eventIds = $registrationEventListener->getEventsIds(20);
        $this->assertInternalType('array', $eventIds);
    }

    /**
     * @covers \REW\Backend\Dashboard\EventListener\RegistrationEventListener::getNewerEventIds()
     * @covers \REW\Backend\Dashboard\AbstractEventListener::getNewerEventIds()
     */
    public function testGetNewerEventIds()
    {

        $userId = 4;

        $this->auth->shouldReceive('info')
            ->with('id')
            ->times(3)
            ->andReturn($userId);

        $this->leadsAuth->shouldReceive('canManageLeads')
            ->once()
            ->andReturn(true);

        $this->leadsAuth->shouldReceive('canAssignLeads')
            ->once()
            ->andReturn(true);

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')
            ->once()
            ->with([$userId, $userId, "2017-07-21 18:23:40", "2017-07-21 18:23:40", $userId, 'register']);
        $stmt->shouldReceive('fetchAll')
        ->once()
        ->andReturn([
            ['id' => "61", 'timestamp' => "2017-07-19 13:45:27", 'status' => "pending"],
            ['id' => "62", 'timestamp' => "2017-09-19 11:12:54", 'status' => "pending"],
            ['id' => "65", 'timestamp' => "2017-09-20 12:25:17", 'status' => "pending"]
        ]);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with("SELECT `id`, (CASE WHEN `timestamp` > `timestamp_assigned` THEN `timestamp` ELSE `timestamp_assigned` END) AS 'timestamp', `status` FROM `users` `u` WHERE  (`status` = 'pending' OR `status` = 'unassigned') AND (`agent` = ? OR `agent` = 1 OR (`timestamp_assigned` < DATE_SUB(NOW(), INTERVAL 1 DAY) AND `status` = 'pending')) AND (`u`.`site_type` != 'agent' OR `u`.`site` = ? ) AND ((`timestamp` > ?) OR (`timestamp_assigned` > ?)) AND `u`.`id` NOT IN (SELECT `dd`.`event_id` FROM `dashboard_dismissed` `dd` WHERE `dd`.`agent` = ? AND `dd`.`event_mode` = ? GROUP BY `dd`.`event_id`) ORDER BY `timestamp` DESC, `id` ASC")
            ->andReturn($stmt);

        $registrationEventListener = new RegistrationEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);

        $eventIds = $registrationEventListener->getNewerEventIds(1500661420);
        $this->assertInternalType('array', $eventIds);
        $this->assertCount(3, $eventIds);
        $this->assertInstanceOf('REW\Backend\Dashboard\EventId', $eventIds[0]);
        $this->assertEquals(61, $eventIds[0]->getId());
        $this->assertEquals('register', $eventIds[0]->getMode());
        $this->assertEquals('pending', $eventIds[0]->getStatus());
        $this->assertEquals(strtotime("2017-07-19 13:45:27"), $eventIds[0]->getTimestamp());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventListener\RegistrationEventListener::getOlderEventIds()
     * @covers \REW\Backend\Dashboard\AbstractEventListener::getOlderEventIds()
     */
    public function testGetOlderEventIds()
    {

        $userId = 4;

        $this->auth->shouldReceive('info')
            ->with('id')
            ->times(3)
            ->andReturn($userId);

        $this->leadsAuth->shouldReceive('canManageLeads')
            ->once()
            ->andReturn(true);

        $this->leadsAuth->shouldReceive('canAssignLeads')
            ->once()
            ->andReturn(true);

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')
            ->once()
            ->with([$userId, $userId, "2017-07-21 18:23:32", "2017-07-21 18:23:32", 11, "2017-07-21 18:23:32", "2017-07-21 18:23:32", 11, $userId, 'register']);
        $stmt->shouldReceive('fetchAll')
            ->once()
            ->andReturn([
                ['id' => "61", 'timestamp' => "2017-07-19 13:45:27", 'status' => "pending"],
                ['id' => "62", 'timestamp' => "2017-09-19 11:12:54", 'status' => "pending"],
                ['id' => "65", 'timestamp' => "2017-09-20 12:25:17", 'status' => "pending"]
            ]);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with("SELECT `id`, (CASE WHEN `timestamp` > `timestamp_assigned` THEN `timestamp` ELSE `timestamp_assigned` END) AS 'timestamp', `status` FROM `users` `u` WHERE  (`status` = 'pending' OR `status` = 'unassigned') AND (`agent` = ? OR `agent` = 1 OR (`timestamp_assigned` < DATE_SUB(NOW(), INTERVAL 1 DAY) AND `status` = 'pending')) AND (`u`.`site_type` != 'agent' OR `u`.`site` = ? ) AND ((`timestamp` >= `timestamp_assigned` AND (`timestamp` < ? OR (`timestamp` = ? AND `id` >= ?))) OR (`timestamp` < `timestamp_assigned` AND (`timestamp_assigned` < ? OR (`timestamp_assigned` = ? AND `id` >= ?)))) AND `u`.`id` NOT IN (SELECT `dd`.`event_id` FROM `dashboard_dismissed` `dd` WHERE `dd`.`agent` = ? AND `dd`.`event_mode` = ? GROUP BY `dd`.`event_id`) ORDER BY `timestamp` DESC, `id` ASC LIMIT 21")
            ->andReturn($stmt);

        $registrationEventListener = new RegistrationEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);

        $eventIds = $registrationEventListener->getOlderEventIds(1500661412, 11, 20);
        $this->assertInternalType('array', $eventIds);
        $this->assertCount(3, $eventIds);
        $this->assertInstanceOf('REW\Backend\Dashboard\EventId', $eventIds[0]);
        $this->assertEquals(61, $eventIds[0]->getId());
        $this->assertEquals('register', $eventIds[0]->getMode());
        $this->assertEquals('pending', $eventIds[0]->getStatus());
        $this->assertEquals(strtotime("2017-07-19 13:45:27"), $eventIds[0]->getTimestamp());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventListener\RegistrationEventListener::getEventsCount()
     * @covers \REW\Backend\Dashboard\AbstractEventListener::getEventsCount()
     */
    public function testGetEventsCount()
    {

        $userId = 4;

        $this->auth->shouldReceive('info')
            ->with('id')
            ->times(3)
            ->andReturn($userId);

        $this->leadsAuth->shouldReceive('canManageLeads')
            ->once()
            ->andReturn(true);

        $this->leadsAuth->shouldReceive('canAssignLeads')
            ->once()
            ->andReturn(true);

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')
            ->once()
            ->with([$userId, $userId, $userId, 'register']);
        $stmt->shouldReceive('fetchColumn')
            ->once()
            ->andReturn(78);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with("SELECT COUNT(`id`) FROM `users` `u` WHERE  (`status` = 'pending' OR `status` = 'unassigned') AND (`agent` = ? OR `agent` = 1 OR (`timestamp_assigned` < DATE_SUB(NOW(), INTERVAL 1 DAY) AND `status` = 'pending')) AND (`u`.`site_type` != 'agent' OR `u`.`site` = ? ) AND `u`.`id` NOT IN (SELECT `dd`.`event_id` FROM `dashboard_dismissed` `dd` WHERE `dd`.`agent` = ? AND `dd`.`event_mode` = ? GROUP BY `dd`.`event_id`)")
            ->andReturn($stmt);

            $registrationEventListener= new RegistrationEventListener($this->db, $this->auth, $this->container, $this->leadsAuth);
        $this->assertEquals(78, $registrationEventListener->getEventsCount());
    }
}
