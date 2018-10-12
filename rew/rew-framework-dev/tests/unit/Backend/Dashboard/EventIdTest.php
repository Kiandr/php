<?php
namespace REW\Test\Backend\Dashboard;

use Mockery as m;
use REW\Backend\Dashboard\EventId;

class EventIdTest extends \Codeception\Test\Unit
{

    protected function _after()
    {
        m::close();
    }


    /**
     * @covers \REW\Backend\Dashboard\EventId::__construct()
     */
    public function testContruct()
    {
        $eventId = new EventId(12, 'selling', 'pending', 24131515);
        $this->assertInstanceOf('REW\Backend\Dashboard\EventId', $eventId);
    }

    /**
     * @covers \REW\Backend\Dashboard\EventId::__construct()
     * @covers \REW\Backend\Dashboard\EventId::getId()
     */
    public function testGetId()
    {
        $eventId = new EventId(12, 'selling', 'pending', 24131515);
        $this->assertEquals(12, $eventId->getId());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventId::__construct()
     * @covers \REW\Backend\Dashboard\EventId::getMode()
     */
    public function testGetMode()
    {
        $eventId = new EventId(12, 'selling', 'pending', 24131515);
        $this->assertEquals('selling', $eventId->getMode());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventId::__construct()
     * @covers \REW\Backend\Dashboard\EventId::getStatus()
     */
    public function testGetStatus()
    {
        $eventId = new EventId(12, 'selling', 'pending', 24131515);
        $this->assertEquals('pending', $eventId->getStatus());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventId::__construct()
     * @covers \REW\Backend\Dashboard\EventId::getTimestamp()
     */
    public function testGetTimestamp()
    {
        $eventId = new EventId(12, 'selling', 'pending', 24131515);
        $this->assertEquals(24131515, $eventId->getTimestamp());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventId::__construct()
     * @covers \REW\Backend\Dashboard\EventId::getHash()
     */
    public function testGetHash()
    {
        $eventId = new EventId(12, 'selling', 'pending', 24131515);
        $this->assertEquals(md5(serialize(['selling', 'pending', 12])), $eventId->getHash());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventId::__construct()
     * @covers \REW\Backend\Dashboard\EventId::getCursor()
     */
    public function testGetCursor()
    {
        $eventId = new EventId(12, 'selling', 'pending', 24131515);
        $this->assertEquals('24131515::12', $eventId->getCursor());
    }
}
