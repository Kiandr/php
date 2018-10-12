<?php
namespace REW\Test\Backend\Dashboard\EventFactory;

use Mockery as m;
use REW\Backend\Dashboard\EventFactory\RegistrationEventFactory;
use REW\Backend\Dashboard\Interfaces\EventIdInterface;

use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\FormatInterface;
use REW\Core\Interfaces\CacheInterface;

class RegistrationEventFactoryTest extends \Codeception\Test\Unit
{

    protected function _before()
    {
        if (!defined('LM_TABLE_LEADS')) {
            define('LM_TABLE_LEADS', 'users');
        }
        if (!defined('LM_TABLE_AGENTS')) {
            define('LM_TABLE_AGENTS', 'agents');
        }
        if (!defined('URL_BACKEND')) {
            define('URL_BACKEND', 'backend/');
        }

        $this->auth = m::mock(AuthInterface::class);
        $this->db = m::mock(DBInterface::class);
        $this->settings = m::mock(SettingsInterface::class);
        $this->format = m::mock(FormatInterface::class);
        $this->cache = m::mock(CacheInterface::class);
    }

    protected function _after()
    {
        m::close();
    }

    /**
     * @covers \REW\Backend\Dashboard\EventFactory\RegistrationEventFactory::__construct()
     * @covers \REW\Backend\Dashboard\AbstractEventFactory::__construct()
     */
    public function testContruct()
    {
        $registrationEventFactory = new RegistrationEventFactory($this->db, $this->auth, $this->settings, $this->format, $this->cache);
        $this->assertInstanceOf('REW\Backend\Dashboard\EventFactory\RegistrationEventFactory', $registrationEventFactory);
    }

    /**
     * @covers \REW\Backend\Dashboard\EventFactory\RegistrationEventFactory::getMode()
     * @covers \REW\Backend\Dashboard\AbstractEventFactory::getMode()
     */
    public function testGetMode()
    {
        $registrationEventFactory = new RegistrationEventFactory($this->db, $this->auth, $this->settings, $this->format, $this->cache);
        $this->assertEquals('register', $registrationEventFactory->getMode());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventFactory\RegistrationEventFactory::getEvent()
     * @covers \REW\Backend\Dashboard\AbstractEventFactory::getEvent()
     */
    public function testGetEvent()
    {

        // Prepare Event Id
        $eventId= m::mock(EventIdInterface::class);
        $eventId->shouldReceive('getHash')
            ->once()
            ->andReturn('13154156::12');
        $eventId->shouldReceive('getMode')
            ->once()
            ->andReturn('register');
        $eventId->shouldReceive('getTimestamp')
            ->once()
            ->andReturn(13154156);
        $eventId->shouldReceive('getId')
            ->once()
            ->andReturn(12);

        // Prepare DB Query
        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')
            ->once()
            ->with(['id' => 12]);
        $stmt->shouldReceive('fetch')
            ->once()
            ->andReturn(
                ['user_id' => 12, 'first_name' => 'Matthew', 'last_name' => 'Brown', 'email' => 'test@realestatewebmasters.com', 'phone_cell' => '250 111 1111', 'image' =>  null, 'agent' => 4, 'status' => 'pending', 'agent_name' => 'Test Agent']
            );
        $this->db->shouldReceive('prepare')
            ->with('SELECT `l`.`id` AS \'user_id\', `l`.`first_name`, `l`.`last_name`, `l`.`status`, `l`.`email`, `l`.`phone_cell`, `l`.`image`, `l`.`agent`, CONCAT(`a`.`first_name`,\' \',`a`.`last_name`) AS \'agent_name\'  FROM ' . LM_TABLE_LEADS . ' l LEFT JOIN ' . LM_TABLE_AGENTS . ' a ON `l`.`agent` = `a`.`id` WHERE `l`.`id` = :id')
            ->once()
            ->andReturn($stmt);

        // Prepare Cache
        $this->cache->shouldReceive('getCache')
            ->with('REW\Backend\Dashboard\AbstractEventFactory:13154156::12')
            ->once()
            ->andReturn(null);
        $this->cache->shouldReceive('setCache')
            ->once();

        $registrationEventFactory = new RegistrationEventFactory($this->db, $this->auth, $this->settings, $this->format, $this->cache);
        $event = $registrationEventFactory->getEvent($eventId);

        $this->assertInternalType('array', $event);
        $this->assertArrayHasKey('hash', $event);
        $this->assertEquals('13154156::12', $event['hash']);
        $this->assertArrayHasKey('mode', $event);
        $this->assertEquals('register', $event['mode']);
        $this->assertArrayHasKey('timestamp', $event);
        $this->assertEquals('13154156', $event['timestamp']);
        $this->assertArrayHasKey('data', $event);
        $this->assertInternalType('array', $event['data']);
        $this->assertArrayHasKey('lead', $event['data']);
        $this->assertInternalType('array', $event['data']['lead']);

        $this->assertEquals('12', $event['data']['lead']['id']);
        $this->assertEquals('Matthew Brown', $event['data']['lead']['name']);
        $this->assertEquals('test@realestatewebmasters.com', $event['data']['lead']['email']);
        $this->assertEquals('backend/email/?id=12&type=leads&redirect=backend/', $event['data']['lead']['emailLink']);
        $this->assertEquals('250 111 1111', $event['data']['lead']['phone']);
        $this->assertEquals('tel:+250-111-1111', $event['data']['lead']['phoneLink']);
        $this->assertEquals('backend/leads/lead/summary/?id=12', $event['data']['lead']['link']);
        $this->assertEquals('4', $event['data']['lead']['agent']);
        $this->assertEquals('Test Agent', $event['data']['lead']['agentName']);
        $this->assertEquals('backend/agents/agent/summary/?id=4', $event['data']['lead']['agentLink']);
        $this->assertEquals('b', $event['data']['lead']['defaultClass']);
        $this->assertEquals('MB', $event['data']['lead']['defaultText']);
        $this->assertEquals(null, $event['data']['lead']['image']);
        $this->assertEquals('pending', $event['data']['lead']['status']);
    }

    /**
     * @covers \REW\Backend\Dashboard\EventFactory\RegistrationEventFactory::getEvent()
     * @covers \REW\Backend\Dashboard\AbstractEventFactory::getEvent()
     */
    public function testGetEventFromHash()
    {

        // Event cached
        $storedEvent = [
            "hash" => "13154156::12",
            "mode"=> "register",
            "timestamp" => 13154156,
            "data" => [
                "lead" => [
                    "id" => 12,
                    "name" => "Matthew Brown",
                    "email" => "test@realestatewebmasters.com",
                    "emailLink" => "backend/email/?id=12&type=agents&redirect=backend/",
                    "phone" => "250 111 1111",
                    "phoneLink" => "tel:+250-111-1111",
                    "link" => "backend/leads/lead/summary/?id=12",
                    "agent" => 4,
                    "agentName" => "Test Agent",
                    "agentLink" => "backend/agents/agent/summary/?id=4",
                    "defaultClass" => "b",
                    "defaultText" => "MB",
                    "image" => null
                ]
            ]
        ];

        // Prepare Event Id
        $eventId= m::mock(EventIdInterface::class);
        $eventId->shouldReceive('getHash')
            ->once()
            ->andReturn('13154156::12');

        // Prepare Cache
        $this->cache->shouldReceive('getCache')
            ->with('REW\Backend\Dashboard\AbstractEventFactory:13154156::12')
            ->once()
            ->andReturn($storedEvent);

        $registrationEventFactory = new RegistrationEventFactory($this->db, $this->auth, $this->settings, $this->format, $this->cache);
        $event = $registrationEventFactory->getEvent($eventId);

        $this->assertInternalType('array', $event);
        $this->assertArrayHasKey('hash', $event);
        $this->assertEquals('13154156::12', $event['hash']);
        $this->assertArrayHasKey('mode', $event);
        $this->assertEquals('register', $event['mode']);
        $this->assertArrayHasKey('timestamp', $event);
        $this->assertEquals('13154156', $event['timestamp']);
        $this->assertArrayHasKey('data', $event);
        $this->assertInternalType('array', $event['data']);
        $this->assertArrayHasKey('lead', $event['data']);
        $this->assertInternalType('array', $event['data']['lead']);

        $this->assertEquals('12', $event['data']['lead']['id']);
        $this->assertEquals('Matthew Brown', $event['data']['lead']['name']);
        $this->assertEquals('test@realestatewebmasters.com', $event['data']['lead']['email']);
        $this->assertEquals('backend/email/?id=12&type=agents&redirect=backend/', $event['data']['lead']['emailLink']);
        $this->assertEquals('250 111 1111', $event['data']['lead']['phone']);
        $this->assertEquals('tel:+250-111-1111', $event['data']['lead']['phoneLink']);
        $this->assertEquals('backend/leads/lead/summary/?id=12', $event['data']['lead']['link']);
        $this->assertEquals('4', $event['data']['lead']['agent']);
        $this->assertEquals('Test Agent', $event['data']['lead']['agentName']);
        $this->assertEquals('backend/agents/agent/summary/?id=4', $event['data']['lead']['agentLink']);
        $this->assertEquals('b', $event['data']['lead']['defaultClass']);
        $this->assertEquals('MB', $event['data']['lead']['defaultText']);
        $this->assertEquals(null, $event['data']['lead']['image']);
    }
}
