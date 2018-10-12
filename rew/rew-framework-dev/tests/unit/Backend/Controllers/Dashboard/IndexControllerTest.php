<?php
namespace REW\Test\Backend\Controllers\Dashboard;

use Mockery as m;
use REW\Backend\Exceptions\UnauthorizedPageException;
use REW\Backend\View\Interfaces\FactoryInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\LogInterface;
use REW\Backend\Auth\DashboardAuth;
use REW\Backend\Dashboard\EventListener\FormEvents\InquiryEventListener;
use REW\Backend\Dashboard\EventListener\FormEvents\SellingEventListener;
use REW\Backend\Dashboard\EventListener\FormEvents\ShowingEventListener;
use REW\Backend\Dashboard\EventListener\MessageEventListener;
use REW\Backend\Dashboard\EventListener\RegistrationEventListener;
use REW\Backend\Controller\Dashboard\IndexController;
use REW\Backend\Dashboard\EventIdCollection;
use REW\Backend\Dashboard\Interfaces\EventIdInterface;
use REW\Backend\Dashboard\EventFactory\FormEvents\InquiryEventFactory;
use REW\Backend\Dashboard\EventFactory\FormEvents\SellingEventFactory;
use REW\Backend\Dashboard\EventFactory\MessageEventFactory;

class IndexControllerTest extends \Codeception\Test\Unit
{

    protected function _before()
    {
        $this->view = m::mock(FactoryInterface::class);
        $this->auth = m::mock(AuthInterface::class);
        $this->db = m::mock(DBInterface::class);
        $this->settings = m::mock(SettingsInterface::class);
        $this->log = m::mock(LogInterface::class);
        $this->dashboardAuth = m::mock(DashboardAuth::class);
        $this->inquiryEventListener = m::mock(InquiryEventListener::class);
        $this->sellingEventListener= m::mock(SellingEventListener::class);
        $this->showingEventListener= m::mock(ShowingEventListener::class);
        $this->messageEventListener= m::mock(MessageEventListener::class);
        $this->registrationEventListener= m::mock(RegistrationEventListener::class);
    }

    protected function _after()
    {
        m::close();
    }

    /**
     * @covers \REW\Backend\Controller\Dashboard\IndexController::__construct()
     */
    public function testContruct()
    {
        $indexController = new IndexController($this->view, $this->auth, $this->db, $this->settings, $this->log, $this->dashboardAuth, $this->inquiryEventListener, $this->sellingEventListener, $this->showingEventListener, $this->messageEventListener, $this->registrationEventListener);
        $this->assertInstanceOf('REW\Backend\Controller\Dashboard\IndexController', $indexController);
    }

    /**
     * Authorized to View Dashboard
     * @covers \REW\Backend\Controller\Dashboard\IndexController::canViewDashboard()
     */
    public function testCanViewDashboard()
    {

        $this->dashboardAuth->shouldReceive('canViewDashboard')
            ->once()
            ->andReturn(true);

        // Check for no exception
        $indexController = new IndexController($this->view, $this->auth, $this->db, $this->settings, $this->log, $this->dashboardAuth, $this->inquiryEventListener, $this->sellingEventListener, $this->showingEventListener, $this->messageEventListener, $this->registrationEventListener);
        $indexController->canViewDashboard();
    }

    /**
     * Not Authorized to View Dashboard
     * @covers \REW\Backend\Controller\Dashboard\IndexController::canViewDashboard
     * @throws UnauthorizedPageException
     */
    public function testCanViewDashboardFailed()
    {

        $this->dashboardAuth->shouldReceive('canViewDashboard')
            ->once()
            ->andReturn(false);

        // Check for no exception
        $indexController = new IndexController($this->view, $this->auth, $this->db, $this->settings, $this->log, $this->dashboardAuth, $this->inquiryEventListener, $this->sellingEventListener, $this->showingEventListener, $this->messageEventListener, $this->registrationEventListener);
        $this->expectException(UnauthorizedPageException::class);
        $indexController->canViewDashboard();
    }

    /**
     * @covers \REW\Backend\Controller\Dashboard\IndexController::getEventListeners()
     */
    public function testGetEventListeners()
    {

        // Check for array of listeners
        $indexController = new IndexController($this->view, $this->auth, $this->db, $this->settings, $this->log, $this->dashboardAuth, $this->inquiryEventListener, $this->sellingEventListener, $this->showingEventListener, $this->messageEventListener, $this->registrationEventListener);
        $listeners = $indexController->getEventListeners();

        $this->assertInternalType('array', $listeners);
        $this->assertCount(5, $listeners);

        $this->assertInstanceOf(InquiryEventListener::class, $listeners[0]);
        $this->assertInstanceOf(SellingEventListener::class, $listeners[1]);
        $this->assertInstanceOf(ShowingEventListener::class, $listeners[2]);
        $this->assertInstanceOf(MessageEventListener::class, $listeners[3]);
        $this->assertInstanceOf(RegistrationEventListener::class, $listeners[4]);
    }

    /**
     * @covers \REW\Backend\Controller\Dashboard\IndexController::getEventCollections()
     */
    public function testGetEventCollections()
    {

        $this->inquiryEventListener->shouldReceive('getMode')
            ->once()
            ->andReturn('message');
        $this->inquiryEventListener->shouldReceive('getEventsIds')
            ->once()
            ->with(IndexController::LIMIT)
            ->andReturn([1,3,8]);
        $this->inquiryEventListener->shouldReceive('getEventsCount')
            ->once()
            ->andReturn(23);
        $inquiryFactory= m::mock(InquiryEventFactory::class);
        $this->inquiryEventListener->shouldReceive('getFactory')
            ->once()
            ->andReturn($inquiryFactory);

        $this->messageEventListener->shouldReceive('getMode')
            ->once()
            ->andReturn('message');
        $this->messageEventListener->shouldReceive('getEventsIds')
            ->once()
            ->with(IndexController::LIMIT)
            ->andReturn([2,4,6]);
        $this->messageEventListener->shouldReceive('getEventsCount')
            ->once()
            ->andReturn(23);
        $messageFactory= m::mock(MessageEventFactory::class);
        $this->messageEventListener->shouldReceive('getFactory')
            ->once()
            ->andReturn($messageFactory);

        $indexController = new IndexController($this->view, $this->auth, $this->db, $this->settings, $this->log, $this->dashboardAuth, $this->inquiryEventListener, $this->sellingEventListener, $this->showingEventListener, $this->messageEventListener, $this->registrationEventListener);
        $collections = $indexController->getEventCollections([$this->inquiryEventListener, $this->messageEventListener]);

        $this->assertInternalType('array', $collections);
        $this->assertCount(2, $collections);

        $this->assertInstanceOf(EventIdCollection::class, $collections[0]);
        $this->assertInstanceOf(EventIdCollection::class, $collections[1]);
    }

    /**
     * @covers \REW\Backend\Controller\Dashboard\IndexController::getNextEventCollection()
     */
    public function testGetNextEventCollection()
    {

        $inquiryCollection = m::mock(EventIdCollection::class);
        $inquiryCollection->shouldReceive('getNextTimestamp')
            ->once()
            ->andReturn(1);

        $messageCollection = m::mock(EventIdCollection::class);
        $messageCollection->shouldReceive('getNextTimestamp')
            ->once()
            ->andReturn(3);

        $sellingCollection = m::mock(EventIdCollection::class);
        $sellingCollection->shouldReceive('getNextTimestamp')
            ->once()
            ->andReturn(2);

        $indexController = new IndexController($this->view, $this->auth, $this->db, $this->settings, $this->log, $this->dashboardAuth, $this->inquiryEventListener, $this->sellingEventListener, $this->showingEventListener, $this->messageEventListener, $this->registrationEventListener);
        $nextCollection = $indexController->getNextEventCollection([$inquiryCollection, $messageCollection, $sellingCollection]);
        $this->assertSame($messageCollection, $nextCollection);
    }

    /**
     * @covers \REW\Backend\Controller\Dashboard\IndexController::getLoadedEventData()
     */
    public function testGetLoadedEventData()
    {

        // Inquiry Events to Create
        $inquiryOne = m::mock(EventIdInterface::class);
        $inquiryTwo = m::mock(EventIdInterface::class);
        $inquiryThree = m::mock(EventIdInterface::class);

        // Event Creation Calls
        $inquiryCollection = m::mock(EventIdCollection::class);
        $inquiryCollection->shouldReceive('getNextTimestamp')
            ->times(9)
            ->andReturn(7, 7, 4, 4, 4, 1, 1, 1, null);
        $inquiryCollection->shouldReceive('getNextEvent')
            ->times(3)
            ->andReturn($inquiryOne, $inquiryTwo, $inquiryThree);
        $inquiryFactory = m::mock(InquiryEventFactory::class);
        $inquiryFactory->shouldReceive('getEvent')
            ->times(3)
            ->andReturn('i-1', 'i-2', 'i-3');
        $inquiryCollection->shouldReceive('getFactory')
            ->times(3)
            ->andReturn($inquiryFactory);
        $inquiryCollection->shouldReceive('iterateCurrentEvent')
            ->times(3);

        // Message Events to Create
        $messageOne = m::mock(EventIdInterface::class);
        $messageTwo = m::mock(EventIdInterface::class);
        $messageThree = m::mock(EventIdInterface::class);

        // Message Event Creation Calls
        $messageCollection = m::mock(EventIdCollection::class);
        $messageCollection->shouldReceive('getNextTimestamp')
            ->times(9)
            ->andReturn(8, 6, 6, 3, 3, 3, null, null, null);
        $messageCollection->shouldReceive('getNextEvent')
            ->times(3)
            ->andReturn($messageOne, $messageTwo, $messageThree);
        $messageFactory= m::mock(MessageEventFactory::class);
        $messageFactory->shouldReceive('getEvent')
            ->times(3)
            ->andReturn('m-1', 'm-2', 'm-3');
        $messageCollection->shouldReceive('getFactory')
            ->times(3)
            ->andReturn($messageFactory);
        $messageCollection->shouldReceive('iterateCurrentEvent')
            ->times(3);

        // Message Events to Create
        $sellingOne = m::mock(EventIdInterface::class);
        $sellingTwo = m::mock(EventIdInterface::class);

        // Message Event Creation Calls
        $sellingCollection = m::mock(EventIdCollection::class);
        $sellingCollection->shouldReceive('getNextTimestamp')
            ->times(9)
            ->andReturn(5, 5, 5, 5, 2, 2, 2, null, null);
        $sellingCollection->shouldReceive('getNextEvent')
            ->times(2)
            ->andReturn($sellingOne, $sellingTwo);
        $sellingFactory= m::mock(SellingEventFactory::class);
        $sellingFactory->shouldReceive('getEvent')
            ->times(2)
            ->andReturn('s-1', 's-2');
        $sellingCollection->shouldReceive('getFactory')
            ->times(2)
            ->andReturn($sellingFactory);
        $sellingCollection->shouldReceive('iterateCurrentEvent')
            ->times(2);

        $indexController = new IndexController($this->view, $this->auth, $this->db, $this->settings, $this->log, $this->dashboardAuth, $this->inquiryEventListener, $this->sellingEventListener, $this->showingEventListener, $this->messageEventListener, $this->registrationEventListener);
        $events = $indexController->getLoadedEventData([$inquiryCollection, $messageCollection, $sellingCollection]);

        $this->assertInternalType('array', $events);
        $this->assertCount(8, $events);
        $this->assertSame('m-1', $events[0]);
        $this->assertSame('i-1', $events[1]);
        $this->assertSame('m-2', $events[2]);
        $this->assertSame('s-1', $events[3]);
        $this->assertSame('i-2', $events[4]);
        $this->assertSame('m-3', $events[5]);
        $this->assertSame('s-2', $events[6]);
        $this->assertSame('i-3', $events[7]);
    }

    /**
     * @covers \REW\Backend\Controller\Dashboard\IndexController::getLoadedEventData()
     */
    public function testGetLoadedEventDataLimit()
    {

        // Inquiry Event Mockery
        $inquiryCollection = m::mock(EventIdCollection::class);
        $inquiryCollection->shouldReceive('getNextTimestamp')
            ->times(30)
            ->andReturn(34);
        $inquiryEvent = m::mock(EventIdInterface::class);
        $inquiryCollection->shouldReceive('getNextEvent')
            ->times(30)
            ->andReturn($inquiryEvent);
        $inquiryFactory = m::mock(InquiryEventFactory::class);
        $inquiryFactory->shouldReceive('getEvent')
            ->times(30)
            ->andReturn('data');
        $inquiryCollection->shouldReceive('getFactory')
            ->times(30)
            ->andReturn($inquiryFactory);
        $inquiryCollection->shouldReceive('iterateCurrentEvent')
            ->times(30);

        $indexController = new IndexController($this->view, $this->auth, $this->db, $this->settings, $this->log, $this->dashboardAuth, $this->inquiryEventListener, $this->sellingEventListener, $this->showingEventListener, $this->messageEventListener, $this->registrationEventListener);
        $events = $indexController->getLoadedEventData([$inquiryCollection]);

        $this->assertInternalType('array', $events);
        $this->assertCount(30, $events);
    }

    /**
     * @covers \REW\Backend\Controller\Dashboard\IndexController::getUnloadedEventData()
     */
    public function testGetUnloadedEventData()
    {

        $inquiryCollection = m::mock(EventIdCollection::class);
        $inquiryCollection->shouldReceive('getType')
            ->once()
            ->andReturn('inquiry');
        $inquiryCollection->shouldReceive('getUnloadedEventCount')
            ->once()
            ->andReturn(37);
        $inquiryCollection->shouldReceive('getNextCursor')
            ->once()
            ->andReturn('2342567::31');

        $messageCollection= m::mock(EventIdCollection::class);
        $messageCollection->shouldReceive('getType')
            ->once()
            ->andReturn('message');
        $messageCollection->shouldReceive('getUnloadedEventCount')
            ->once()
            ->andReturn(201);
        $messageCollection->shouldReceive('getNextCursor')
            ->once()
            ->andReturn('97854651::97');

        $sellingCollection= m::mock(EventIdCollection::class);
        $sellingCollection->shouldReceive('getType')
            ->once()
            ->andReturn('selling');
        $sellingCollection->shouldReceive('getUnloadedEventCount')
            ->once()
            ->andReturn(0);
        $sellingCollection->shouldReceive('getNextCursor')
            ->once()
            ->andReturn(null);

        $indexController = new IndexController($this->view, $this->auth, $this->db, $this->settings, $this->log, $this->dashboardAuth, $this->inquiryEventListener, $this->sellingEventListener, $this->showingEventListener, $this->messageEventListener, $this->registrationEventListener);
        $unloadedEvents = $indexController->getUnloadedEventData([$inquiryCollection, $messageCollection, $sellingCollection]);

        $this->assertInternalType('array', $unloadedEvents);
        $this->assertArrayHasKey('nextUnloadedInquiry', $unloadedEvents);
        $this->assertArrayHasKey('unloadedInquiryCount', $unloadedEvents);
        $this->assertArrayHasKey('nextUnloadedMessage', $unloadedEvents);
        $this->assertArrayHasKey('unloadedMessageCount', $unloadedEvents);
        $this->assertArrayNotHasKey('nextUnloadedSelling', $unloadedEvents);
        $this->assertArrayHasKey('unloadedSellingCount', $unloadedEvents);

        $this->assertEquals('2342567::31', $unloadedEvents['nextUnloadedInquiry']);
        $this->assertEquals(37, $unloadedEvents['unloadedInquiryCount']);
        $this->assertEquals('97854651::97', $unloadedEvents['nextUnloadedMessage']);
        $this->assertEquals(201, $unloadedEvents['unloadedMessageCount']);
        $this->assertEquals(0, $unloadedEvents['unloadedSellingCount']);
    }

    /**
     * @covers \REW\Backend\Controller\Dashboard\IndexController::getApiKey()
     */
    public function testGetApiKey()
    {
        $this->settings->shouldReceive('get')
            ->with('google.maps.api_key')
            ->once()
            ->andReturn('23436445784');

        $indexController = new IndexController($this->view, $this->auth, $this->db, $this->settings, $this->log, $this->dashboardAuth, $this->inquiryEventListener, $this->sellingEventListener, $this->showingEventListener, $this->messageEventListener, $this->registrationEventListener);
        $api = $indexController->getApiKey();

        $this->assertEquals('23436445784', $api);
    }
}
