<?php
namespace REW\Test\Backend\Dashboard\EventFactory;

use Mockery as m;
use REW\Backend\Dashboard\EventFactory\FormEvents\SellingEventFactory;
use REW\Backend\Dashboard\Interfaces\EventIdInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\FormatInterface;
use REW\Core\Interfaces\CacheInterface;
use \Util_Curl;

class SellingEventFactoryTest extends \Codeception\Test\Unit
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
     * @covers \REW\Backend\Dashboard\EventFactory\FormEvents\SellingEventFactory::__construct()
     * @covers \REW\Backend\Dashboard\AbstractEventFactory::__construct()
     */
    public function testContruct()
    {
        $sellingEventFactory = new SellingEventFactory($this->db, $this->auth, $this->settings, $this->format, $this->cache, $this->idxFactory);
        $this->assertInstanceOf('REW\Backend\Dashboard\EventFactory\FormEvents\SellingEventFactory', $sellingEventFactory);
    }

    /**
     * @covers \REW\Backend\Dashboard\EventFactory\FormEvents\SellingEventFactory::getMode()
     * @covers \REW\Backend\Dashboard\AbstractEventFactory::getMode()
     */
    public function testGetMode()
    {
        $sellingEventFactory = new SellingEventFactory($this->db, $this->auth, $this->settings, $this->format, $this->cache, $this->idxFactory);
        $this->assertEquals('selling', $sellingEventFactory->getMode());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventFactory\FormEvents\SellingEventFactory::getEvent()
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
            ->andReturn('selling');
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
            ->andReturn(['user_id' => 17, 'first_name' => 'Matthew', 'last_name' => 'Brown', 'email' => 'test@realestatewebmasters.com', 'phone_cell' => '250 111 1111', 'image' =>  null, 'agent' => 4, 'status' => 'pending', 'form_id' => 8, 'form' => 'IDX Inquiry', 'data' => 'a:6:{s:4:"form";s:11:"Seller Form";s:8:"comments";s:74:"I want to sell my home: 35200 CATHEDRAL CANYON DR V173, Cathedral City, CA";s:7:"fm-addr";s:30:"35200 CATHEDRAL CANYON DR V173";s:7:"fm-town";s:14:"Cathedral City";s:8:"fm-state";s:2:"CA";s:11:"fm-postcode";s:5:"92234";}', 'selling' => 'This is a test selling.']);
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

        $utilCurl = \Mockery::mock('alias:' . Util_Curl::class);
        $utilCurl->shouldReceive('executeRequest')
            ->with('https://maps.googleapis.com/maps/api/geocode/json?sensor=false&key=AIzaSyCWcSS7z2onkSWnkegCusqA1qTRcr-Z5UI&address=35200%20CATHEDRAL%20CANYON%20DR%20V173%20Cathedral%20City%20CA')
            ->once()
            ->andReturn('{
               "results" : [
                  {
                     "address_components" : [
                        {
                           "long_name" : "35200",
                           "short_name" : "35200",
                           "types" : [ "street_number" ]
                        },
                        {
                           "long_name" : "Cathedral Canyon Drive",
                           "short_name" : "Cathedral Canyon Dr",
                           "types" : [ "route" ]
                        },
                        {
                           "long_name" : "Cathedral City",
                           "short_name" : "Cathedral City",
                           "types" : [ "locality", "political" ]
                        },
                        {
                           "long_name" : "Riverside County",
                           "short_name" : "Riverside County",
                           "types" : [ "administrative_area_level_2", "political" ]
                        },
                        {
                           "long_name" : "California",
                           "short_name" : "CA",
                           "types" : [ "administrative_area_level_1", "political" ]
                        },
                        {
                           "long_name" : "United States",
                           "short_name" : "US",
                           "types" : [ "country", "political" ]
                        },
                        {
                           "long_name" : "92234",
                           "short_name" : "92234",
                           "types" : [ "postal_code" ]
                        }
                     ],
                     "formatted_address" : "35200 Cathedral Canyon Dr, Cathedral City, CA 92234, USA",
                     "geometry" : {
                        "location" : {
                           "lat" : 33.7916421,
                           "lng" : -116.4645267
                        },
                        "location_type" : "ROOFTOP",
                        "viewport" : {
                           "northeast" : {
                              "lat" : 33.7929910802915,
                              "lng" : -116.4631777197085
                           },
                           "southwest" : {
                              "lat" : 33.7902931197085,
                              "lng" : -116.4658756802915
                           }
                        }
                     },
                     "partial_match" : true,
                     "place_id" : "ChIJJT0V5dQC24ARw8I-F64U4oE",
                     "types" : [ "street_address" ]
                  }
               ],
               "status" : "OK"
            }');
        $utilCurl->shouldReceive('info')
            ->once()
            ->andReturn(['http_code' => 200]);

        $this->settings->MODULES = ['REW_IDX_STREETVIEW' => true];
        $this->settings->shouldReceive('get')
            ->with('google.maps.api_key')
            ->once()
            ->andReturn('AIzaSyCWcSS7z2onkSWnkegCusqA1qTRcr-Z5UI');

        $sellingEventFactory = new SellingEventFactory($this->db, $this->auth, $this->settings, $this->format, $this->cache, $this->idxFactory);
        $event = $sellingEventFactory->getEvent($eventId);

        $this->assertInternalType('array', $event);
        $this->assertArrayHasKey('hash', $event);
        $this->assertEquals('123456789::8', $event['hash']);
        $this->assertArrayHasKey('mode', $event);
        $this->assertEquals('selling', $event['mode']);
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

        $this->assertArrayHasKey('map', $event['data']);
        $this->assertInternalType('array', $event['data']['map']);
        $this->assertEquals(true, $event['data']['map']['show']);
        $this->assertEquals('-116.4645267', $event['data']['map']['lng']);
        $this->assertEquals('33.791642099999997', $event['data']['map']['lat']);
    }
}
