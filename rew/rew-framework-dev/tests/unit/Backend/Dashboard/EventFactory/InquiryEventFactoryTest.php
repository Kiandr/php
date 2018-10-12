<?php
namespace REW\Test\Backend\Dashboard\EventFactory;

use Mockery as m;
use REW\Backend\Dashboard\EventFactory\FormEvents\InquiryEventFactory;
use REW\Backend\Dashboard\Interfaces\EventIdInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\FormatInterface;
use REW\Core\Interfaces\CacheInterface;

class InquiryEventFactoryTest extends \Codeception\Test\Unit
{

    protected function _before()
    {
        if (!defined('LM_TABLE_LEADS')) {
            define('LM_TABLE_LEADS', 'users');
        }
        if (!defined('LM_TABLE_FORMS')) {
            define('LM_TABLE_FORMS', 'users_forms');
        }
        if (!defined('URL_BACKEND')) {
            define('URL_BACKEND', 'backend/');
        }

        $this->auth = m::mock(AuthInterface::class);
        $this->db = m::mock(DBInterface::class);
        $this->settings = m::mock(SettingsInterface::class);
        $this->format = m::mock(FormatInterface::class);
        $this->cache = m::mock(CacheInterface::class);
        $this->idxFactory = m::mock(IDXFactoryInterface::class);
    }

    protected function _after()
    {
        m::close();
    }

    /**
     * @covers \REW\Backend\Dashboard\EventFactory\FormEvents\InquiryEventFactory::__construct()
     * @covers \REW\Backend\Dashboard\AbstractEventFactory::__construct()
     */
    public function testContruct()
    {
        $inquiryEventFactory = new InquiryEventFactory($this->db, $this->auth, $this->settings, $this->format, $this->cache, $this->idxFactory);
        $this->assertInstanceOf('REW\Backend\Dashboard\EventFactory\FormEvents\InquiryEventFactory', $inquiryEventFactory);
    }

    /**
     * @covers \REW\Backend\Dashboard\EventFactory\FormEvents\InquiryEventFactory::getMode()
     * @covers \REW\Backend\Dashboard\AbstractEventFactory::getMode()
     */
    public function testGetMode()
    {
        $inquiryEventFactory = new InquiryEventFactory($this->db, $this->auth, $this->settings, $this->format, $this->cache, $this->idxFactory);
        $this->assertEquals('inquiry', $inquiryEventFactory->getMode());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventFactory\FormEvents\InquiryEventFactory::getEvent()
     * @covers \REW\Backend\Dashboard\AbstractEventFactory::getEvent()
     */
    public function testGetEvent()
    {

        // Prepare Event Id
        $eventId= m::mock(EventIdInterface::class);
        $eventId->shouldReceive('getHash')
            ->once()
            ->andReturn('123456789::8');
        $eventId->shouldReceive('getMode')
            ->once()
            ->andReturn('inquiry');
        $eventId->shouldReceive('getTimestamp')
            ->once()
            ->andReturn(123456789);
        $eventId->shouldReceive('getId')
            ->once()
            ->andReturn(8);

        // Prepare DB Query
        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')
            ->once()
            ->with(['id' => 8]);
        $stmt->shouldReceive('fetch')
            ->once()
            ->andReturn(['user_id' => 17, 'first_name' => 'Matthew', 'last_name' => 'Brown', 'email' => 'test@realestatewebmasters.com', 'phone_cell' => '250 111 1111', 'image' =>  null, 'agent' => 4, 'status' => 'pending', 'form_id' => 8, 'form' => 'IDX Inquiry', 'data' => 'a:5:{s:4:"form";s:11:"IDX Inquiry";s:10:"ListingMLS";s:8:"16148726";s:11:"ListingType";s:11:"Residential";s:11:"ListingFeed";s:6:"carets";s:8:"comments";s:69:"Please call me. I have a serious offer to put on 13483 HUBBARD ST 24.";}', 'inquiry' => 'This is a test inquiry.']);
        $this->db->shouldReceive('prepare')
            ->with('SELECT `u`.`id` AS \'user_id\', `u`.`first_name`, `u`.`last_name`, `u`.`status`, `u`.`email`, `u`.`phone_cell`, `u`.`image`, `u`.`agent`, `uf`.`timestamp`, `uf`.`id` AS \'form_id\', `uf`.`form`, `uf`.`data` FROM ' . LM_TABLE_FORMS . ' `uf` JOIN ' . LM_TABLE_LEADS . ' `u` ON `u`.`id` = `uf`.`user_id` WHERE `uf`.`id` = :id')
            ->once()
            ->andReturn($stmt);

        // Prepare Cache
        $this->cache->shouldReceive('getCache')
            ->with('REW\Backend\Dashboard\AbstractEventFactory:123456789::8')
            ->once()
            ->andReturn(null);
        $this->cache->shouldReceive('setCache')
            ->once();

        // Prepare Factory
        $this->idxFactory->shouldReceive('switchFeed')
            ->once();
        $idx = m::mock(IDXInterface::class);
        $idx->shouldReceive('selectColumns')
            ->once()
            ->andReturn("`id` AS 'id', `ListingMLS` AS 'ListingMLS', `ListingPrice` AS 'ListingPrice', `ListingType` AS 'ListingType', `ListingSubType` AS 'ListingSubType', `ListingStyle` AS 'ListingStyle', `ListingStatus` AS 'ListingStatus', `ListingRemarks` AS 'ListingRemarks', `ListingImage` AS 'ListingImage', `ListingDate` AS 'ListingDate', `ListingDOM` AS 'ListingDOM', `timestamp_created` AS 'timestamp_created', `ListingPriceOld` AS 'ListingPriceOld', `ListingPriceChanged` AS 'ListingPriceChanged', `Address` AS 'Address', `AddressUnit` AS 'AddressUnit', `AddressArea` AS 'AddressArea', `AddressSubdivision` AS 'AddressSubdivision', `AddressCity` AS 'AddressCity', `AddressCounty` AS 'AddressCounty', `AddressState` AS 'AddressState', `AddressZipCode` AS 'AddressZipCode', `SchoolDistrict` AS 'SchoolDistrict', `SchoolElementary` AS 'SchoolElementary', `SchoolMiddle` AS 'SchoolMiddle', `SchoolHigh` AS 'SchoolHigh', `NumberOfBedrooms` AS 'NumberOfBedrooms', `NumberOfBathrooms` AS 'NumberOfBathrooms', `NumberOfBathsFull` AS 'NumberOfBathsFull', `NumberOfBathsHalf` AS 'NumberOfBathsHalf', `NumberOfSqFt` AS 'NumberOfSqFt', `NumberOfAcres` AS 'NumberOfAcres', `NumberOfStories` AS 'NumberOfStories', `NumberOfGarages` AS 'NumberOfGarages', `NumberOfParkingSpaces` AS 'NumberOfParkingSpaces', `NumberOfFireplaces` AS 'NumberOfFireplaces', `YearBuilt` AS 'YearBuilt', `HasPool` AS 'HasPool', `HasFireplace` AS 'HasFireplace', `IsWaterfront` AS 'IsWaterfront', `IsForeclosure` AS 'IsForeclosure', `IsShortSale` AS 'IsShortSale', `IsBankOwned` AS 'IsBankOwned', `DescriptionLot` AS 'DescriptionLot', `DescriptionPool` AS 'DescriptionPool', `DescriptionView` AS 'DescriptionView', `DescriptionStories` AS 'DescriptionStories', `DescriptionFireplace` AS 'DescriptionFireplace', `DescriptionWaterfront` AS 'DescriptionWaterfront', `DescriptionGarages` AS 'DescriptionGarages', `DescriptionParking` AS 'DescriptionParking', `DescriptionAmenities` AS 'DescriptionAmenities', `DescriptionAppliances` AS 'DescriptionAppliances', `DescriptionUtilities` AS 'DescriptionUtilities', `DescriptionFeatures` AS 'DescriptionFeatures', `DescriptionExterior` AS 'DescriptionExterior', `DescriptionExteriorFeatures` AS 'DescriptionExteriorFeatures', `DescriptionInterior` AS 'DescriptionInterior', `DescriptionInteriorFeatures` AS 'DescriptionInteriorFeatures', `DescriptionHeating` AS 'DescriptionHeating', `DescriptionCooling` AS 'DescriptionCooling', `DescriptionZoning` AS 'DescriptionZoning', `DescriptionRoofing` AS 'DescriptionRoofing', `DescriptionWindows` AS 'DescriptionWindows', `DescriptionConstruction` AS 'DescriptionConstruction', `DescriptionFoundation` AS 'DescriptionFoundation', `ListingOffice` AS 'ListingOffice', `ListingOfficeID` AS 'ListingOfficeID', `ListingAgent` AS 'ListingAgent', `ListingAgentID` AS 'ListingAgentID', `Latitude` AS 'Latitude', `Longitude` AS 'Longitude', `VirtualTour` AS 'VirtualTour'");
        $idx->shouldReceive('getTable')
            ->once()
            ->andReturn('_rewidx_listings');
        $idx->shouldReceive('field')
            ->with('ListingMLS')
            ->once()
            ->andReturn('ListingMLS');
        $idx->shouldReceive('field')
            ->with('ListingType')
            ->once()
            ->andReturn('ListingType');
        $this->idxFactory->shouldReceive('getIdx')
            ->once()
            ->andReturn($idx);
        $idxDb = m::mock(Database_MySQLImproved::class);
        $idxDb->shouldReceive('cleanInput')
            ->with('16148726')
            ->once()
            ->andReturn('16148726');
        $idxDb->shouldReceive('cleanInput')
            ->with('Residential')
            ->once()
            ->andReturn('Residential');
        $idxDb->shouldReceive('fetchQuery')
            ->with("SELECT SQL_CACHE `id` AS 'id', `ListingMLS` AS 'ListingMLS', `ListingPrice` AS 'ListingPrice', `ListingType` AS 'ListingType', `ListingSubType` AS 'ListingSubType', `ListingStyle` AS 'ListingStyle', `ListingStatus` AS 'ListingStatus', `ListingRemarks` AS 'ListingRemarks', `ListingImage` AS 'ListingImage', `ListingDate` AS 'ListingDate', `ListingDOM` AS 'ListingDOM', `timestamp_created` AS 'timestamp_created', `ListingPriceOld` AS 'ListingPriceOld', `ListingPriceChanged` AS 'ListingPriceChanged', `Address` AS 'Address', `AddressUnit` AS 'AddressUnit', `AddressArea` AS 'AddressArea', `AddressSubdivision` AS 'AddressSubdivision', `AddressCity` AS 'AddressCity', `AddressCounty` AS 'AddressCounty', `AddressState` AS 'AddressState', `AddressZipCode` AS 'AddressZipCode', `SchoolDistrict` AS 'SchoolDistrict', `SchoolElementary` AS 'SchoolElementary', `SchoolMiddle` AS 'SchoolMiddle', `SchoolHigh` AS 'SchoolHigh', `NumberOfBedrooms` AS 'NumberOfBedrooms', `NumberOfBathrooms` AS 'NumberOfBathrooms', `NumberOfBathsFull` AS 'NumberOfBathsFull', `NumberOfBathsHalf` AS 'NumberOfBathsHalf', `NumberOfSqFt` AS 'NumberOfSqFt', `NumberOfAcres` AS 'NumberOfAcres', `NumberOfStories` AS 'NumberOfStories', `NumberOfGarages` AS 'NumberOfGarages', `NumberOfParkingSpaces` AS 'NumberOfParkingSpaces', `NumberOfFireplaces` AS 'NumberOfFireplaces', `YearBuilt` AS 'YearBuilt', `HasPool` AS 'HasPool', `HasFireplace` AS 'HasFireplace', `IsWaterfront` AS 'IsWaterfront', `IsForeclosure` AS 'IsForeclosure', `IsShortSale` AS 'IsShortSale', `IsBankOwned` AS 'IsBankOwned', `DescriptionLot` AS 'DescriptionLot', `DescriptionPool` AS 'DescriptionPool', `DescriptionView` AS 'DescriptionView', `DescriptionStories` AS 'DescriptionStories', `DescriptionFireplace` AS 'DescriptionFireplace', `DescriptionWaterfront` AS 'DescriptionWaterfront', `DescriptionGarages` AS 'DescriptionGarages', `DescriptionParking` AS 'DescriptionParking', `DescriptionAmenities` AS 'DescriptionAmenities', `DescriptionAppliances` AS 'DescriptionAppliances', `DescriptionUtilities` AS 'DescriptionUtilities', `DescriptionFeatures` AS 'DescriptionFeatures', `DescriptionExterior` AS 'DescriptionExterior', `DescriptionExteriorFeatures` AS 'DescriptionExteriorFeatures', `DescriptionInterior` AS 'DescriptionInterior', `DescriptionInteriorFeatures` AS 'DescriptionInteriorFeatures', `DescriptionHeating` AS 'DescriptionHeating', `DescriptionCooling` AS 'DescriptionCooling', `DescriptionZoning` AS 'DescriptionZoning', `DescriptionRoofing` AS 'DescriptionRoofing', `DescriptionWindows` AS 'DescriptionWindows', `DescriptionConstruction` AS 'DescriptionConstruction', `DescriptionFoundation` AS 'DescriptionFoundation', `ListingOffice` AS 'ListingOffice', `ListingOfficeID` AS 'ListingOfficeID', `ListingAgent` AS 'ListingAgent', `ListingAgentID` AS 'ListingAgentID', `Latitude` AS 'Latitude', `Longitude` AS 'Longitude', `VirtualTour` AS 'VirtualTour' FROM `_rewidx_listings` WHERE `ListingMLS` = '16148726' AND `ListingType` = 'Residential' LIMIT 1;")
            ->once()
            ->andReturn([]);
        $this->idxFactory->shouldReceive('getDatabase')
            ->once()
            ->andReturn($idxDb);

        $inquiryEventFactory = new InquiryEventFactory($this->db, $this->auth, $this->settings, $this->format, $this->cache, $this->idxFactory);
        $event = $inquiryEventFactory->getEvent($eventId);

        $this->assertInternalType('array', $event);
        $this->assertArrayHasKey('hash', $event);
        $this->assertEquals('123456789::8', $event['hash']);
        $this->assertArrayHasKey('mode', $event);
        $this->assertEquals('inquiry', $event['mode']);
        $this->assertArrayHasKey('timestamp', $event);
        $this->assertEquals('123456789', $event['timestamp']);
        $this->assertArrayHasKey('data', $event);
        $this->assertInternalType('array', $event['data']);

        $this->assertArrayHasKey('lead', $event['data']);
        $this->assertInternalType('array', $event['data']['lead']);
        $this->assertEquals('17', $event['data']['lead']['id']);
        $this->assertEquals('Matthew Brown', $event['data']['lead']['name']);
        $this->assertEquals('test@realestatewebmasters.com', $event['data']['lead']['email']);
        $this->assertEquals('backend/email/?id=17&type=leads&redirect=backend/', $event['data']['lead']['emailLink']);
        $this->assertEquals('250 111 1111', $event['data']['lead']['phone']);
        $this->assertEquals('tel:+250-111-1111', $event['data']['lead']['phoneLink']);
        $this->assertEquals('backend/leads/lead/summary/?id=17', $event['data']['lead']['link']);
        $this->assertEquals('4', $event['data']['lead']['agent']);
        $this->assertEquals(null, $event['data']['lead']['agentName']);
        $this->assertEquals('backend/agents/agent/summary/?id=4', $event['data']['lead']['agentLink']);
        $this->assertEquals('b', $event['data']['lead']['defaultClass']);
        $this->assertEquals('MB', $event['data']['lead']['defaultText']);
        $this->assertEquals(null, $event['data']['lead']['image']);
        $this->assertEquals('pending', $event['data']['lead']['status']);

        $this->assertArrayHasKey('form', $event['data']);
        $this->assertInternalType('array', $event['data']['form']);
        $this->assertEquals('8', $event['data']['form']['id']);
        $this->assertEquals('IDX Inquiry', $event['data']['form']['name']);
        $this->assertEquals('16148726', $event['data']['form']['mls_number']);
        $this->assertEquals('Residential', $event['data']['form']['type']);
        $this->assertEquals('carets', $event['data']['form']['feed']);
        $this->assertEquals('Please call me. I have a serious offer to put on 13483 HUBBARD ST 24.', $event['data']['form']['comments']);
    }
}
