<?php
namespace REW\Test\Backend\Dashboard;

use Mockery as m;
use REW\Backend\Dashboard\EventIdCollection;
use REW\Backend\Dashboard\EventId;
use REW\Backend\Dashboard\Interfaces\EventFactoryInterface;

class EventIdCollectionTest extends \Codeception\Test\Unit
{

    protected function _before()
    {
        $this->eventFactory= m::mock(EventFactoryInterface::class);
    }

    protected function _after()
    {
        m::close();
    }

    /**
     * @covers \REW\Backend\Dashboard\EventIdCollection::__construct()
     */
    public function testContruct()
    {
        $eventOne = m::mock(EventId::class);
        $eventTwo = m::mock(EventId::class);
        $eventThree = m::mock(EventId::class);

        $eventContainer = new EventIdCollection('inquiry', [$eventOne, $eventTwo, $eventThree], 3, $this->eventFactory);
        $this->assertInstanceOf('REW\Backend\Dashboard\EventIdCollection', $eventContainer);
    }

    /**
     * @covers \REW\Backend\Dashboard\EventIdCollection::__construct()
     * @covers \REW\Backend\Dashboard\EventIdCollection::getType()
     */
    public function testGetType()
    {
        $eventOne = m::mock(EventId::class);
        $eventTwo = m::mock(EventId::class);
        $eventThree = m::mock(EventId::class);

        $eventContainer = new EventIdCollection('inquiry', [$eventOne, $eventTwo, $eventThree], 3, $this->eventFactory);
        $this->assertEquals('inquiry', $eventContainer->getType());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventIdCollection::__construct()
     * @covers \REW\Backend\Dashboard\EventIdCollection::getFactory()
     */
    public function testGetFactory()
    {
        $eventOne = m::mock(EventId::class);
        $eventTwo = m::mock(EventId::class);
        $eventThree = m::mock(EventId::class);

        $eventContainer = new EventIdCollection('inquiry', [$eventOne, $eventTwo, $eventThree], 3, $this->eventFactory);
        $this->assertEquals($this->eventFactory, $eventContainer->getFactory());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventIdCollection::__construct()
     * @covers \REW\Backend\Dashboard\EventIdCollection::getNextEvent()
     */
    public function testGetNextEvent()
    {
        $eventOne = m::mock(EventId::class);
        $eventTwo = m::mock(EventId::class);
        $eventThree = m::mock(EventId::class);

        $eventContainer = new EventIdCollection('inquiry', [$eventOne, $eventTwo, $eventThree], 3, $this->eventFactory);
        $this->assertSame($eventOne, $eventContainer->getNextEvent());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventIdCollection::__construct()
     * @covers \REW\Backend\Dashboard\EventIdCollection::getNextEvent()
     */
    public function testGetNextEventEmpty()
    {
        $eventOne = m::mock(EventId::class);
        $eventTwo = m::mock(EventId::class);
        $eventThree = m::mock(EventId::class);

        $eventContainer = new EventIdCollection('inquiry', [], 0, $this->eventFactory);
        $this->assertNull($eventContainer->getNextEvent());

        $eventContainer = new EventIdCollection('inquiry', [$eventOne, $eventTwo, $eventThree], 3, $this->eventFactory);
        $eventContainer->setCurrentEvent(4);
        $this->assertNull($eventContainer->getNextEvent());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventIdCollection::__construct()
     * @covers \REW\Backend\Dashboard\EventIdCollection::getNextCursor()
     */
    public function testGetNextCursor()
    {
        $eventOne = m::mock(EventId::class);
        $eventOne->shouldReceive('getCursor')
            ->once()
            ->andReturn('97854651::97');
        $eventTwo = m::mock(EventId::class);
        $eventThree = m::mock(EventId::class);

        $eventContainer = new EventIdCollection('inquiry', [$eventOne, $eventTwo, $eventThree], 3, $this->eventFactory);
        $this->assertEquals('97854651::97', $eventContainer->getNextCursor());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventIdCollection::__construct()
     * @covers \REW\Backend\Dashboard\EventIdCollection::getNextCursor()
     */
    public function testGetNextCursorEmpty()
    {
        $eventOne = m::mock(EventId::class);
        $eventTwo = m::mock(EventId::class);
        $eventThree = m::mock(EventId::class);

        $eventContainer = new EventIdCollection('inquiry', [], 0, $this->eventFactory);
        $this->assertNull($eventContainer->getNextCursor());

        $eventContainer = new EventIdCollection('inquiry', [$eventOne, $eventTwo, $eventThree], 3, $this->eventFactory);
        $eventContainer->setCurrentEvent(4);
        $this->assertNull($eventContainer->getNextCursor());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventIdCollection::__construct()
     * @covers \REW\Backend\Dashboard\EventIdCollection::getNextTimestamp()
     */
    public function testGetNextTimestamp()
    {
        $eventOne = m::mock(EventId::class);
        $eventOne->shouldReceive('getTimestamp')
            ->once()
            ->andReturn('13672458979');
        $eventTwo = m::mock(EventId::class);
        $eventThree = m::mock(EventId::class);

        $eventContainer = new EventIdCollection('inquiry', [$eventOne, $eventTwo, $eventThree], 3, $this->eventFactory);
        $this->assertEquals('13672458979', $eventContainer->getNextTimestamp());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventIdCollection::__construct()
     * @covers \REW\Backend\Dashboard\EventIdCollection::getNextTimestamp()
     */
    public function testGetNextTimestampEmpty()
    {
        $eventOne = m::mock(EventId::class);
        $eventTwo = m::mock(EventId::class);
        $eventThree = m::mock(EventId::class);

        $eventContainer = new EventIdCollection('inquiry', [], 0, $this->eventFactory);
        $this->assertNull($eventContainer->getNextTimestamp());

        $eventContainer = new EventIdCollection('inquiry', [$eventOne, $eventTwo, $eventThree], 3, $this->eventFactory);
        $eventContainer->setCurrentEvent(4);
        $this->assertNull($eventContainer->getNextTimestamp());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventIdCollection::__construct()
     * @covers \REW\Backend\Dashboard\EventIdCollection::iterateCurrentEvent()
     * @covers \REW\Backend\Dashboard\EventIdCollection::resetCurrentEvent()
     * @covers \REW\Backend\Dashboard\EventIdCollection::setCurrentEvent()
     * @covers \REW\Backend\Dashboard\EventIdCollection::getCurrentEvent()
     */
    public function testInterateCurrentEvent()
    {
        $eventOne = m::mock(EventId::class);
        $eventTwo = m::mock(EventId::class);
        $eventThree = m::mock(EventId::class);

        $eventContainer = new EventIdCollection('inquiry', [$eventOne, $eventTwo, $eventThree], 3, $this->eventFactory);
        $this->assertEquals(0, $eventContainer->getCurrentEvent());

        $this->assertSame($eventOne, $eventContainer->getNextEvent());

        $eventContainer->iterateCurrentEvent();
        $this->assertEquals(1, $eventContainer->getCurrentEvent());

        $this->assertSame($eventTwo, $eventContainer->getNextEvent());

        $eventContainer->iterateCurrentEvent();
        $this->assertEquals(2, $eventContainer->getCurrentEvent());

        $this->assertSame($eventThree, $eventContainer->getNextEvent());

        $eventContainer->resetCurrentEvent();
        $this->assertEquals(0, $eventContainer->getCurrentEvent());

        $this->assertSame($eventOne, $eventContainer->getNextEvent());

        $eventContainer->setCurrentEvent(1);
        $this->assertEquals(1, $eventContainer->getCurrentEvent());

        $this->assertSame($eventTwo, $eventContainer->getNextEvent());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventIdCollection::__construct()
     * @covers \REW\Backend\Dashboard\EventIdCollection::getUnloadedEventCount()
     */
    public function testGetUnloadedEventCount()
    {
        $eventOne = m::mock(EventId::class);
        $eventTwo = m::mock(EventId::class);
        $eventThree = m::mock(EventId::class);

        $eventContainer = new EventIdCollection('inquiry', [$eventOne, $eventTwo, $eventThree], 3, $this->eventFactory);
        $this->assertEquals(3, $eventContainer->getUnloadedEventCount());
        $eventContainer->iterateCurrentEvent();
        $this->assertEquals(2, $eventContainer->getUnloadedEventCount());
    }
}
