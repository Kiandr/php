<?php
namespace REW\Test\Backend\Dashboard\EventFactory;

use Mockery as m;
use REW\Backend\Dashboard\EventFactory\MessageEventFactory;
use REW\Backend\Dashboard\Interfaces\EventIdInterface;

use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\FormatInterface;
use REW\Core\Interfaces\CacheInterface;

class MessageEventFactoryTest extends \Codeception\Test\Unit
{

    protected function _before()
    {
        if (!defined('LM_TABLE_LEADS')) {
            define('LM_TABLE_LEADS', 'users');
        }
        if (!defined('LM_TABLE_AGENTS')) {
            define('LM_TABLE_AGENTS', 'agents');
        }
        if (!defined('LM_TABLE_MESSAGES')) {
            define('LM_TABLE_MESSAGES', 'users_messages');
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
     * @covers \REW\Backend\Dashboard\EventFactory\MessageEventFactory::__construct()
     * @covers \REW\Backend\Dashboard\AbstractEventFactory::__construct()
     */
    public function testContruct()
    {
        $messageEventFactory = new MessageEventFactory($this->db, $this->auth, $this->settings, $this->format, $this->cache);
        $this->assertInstanceOf('REW\Backend\Dashboard\EventFactory\MessageEventFactory', $messageEventFactory);
    }

    /**
     * @covers \REW\Backend\Dashboard\EventFactory\MessageEventFactory::getMode()
     * @covers \REW\Backend\Dashboard\AbstractEventFactory::getMode()
     */
    public function testGetMode()
    {
        $messageEventFactory = new MessageEventFactory($this->db, $this->auth, $this->settings, $this->format, $this->cache);
        $this->assertEquals('message', $messageEventFactory->getMode());
    }

    /**
     * @covers \REW\Backend\Dashboard\EventFactory\MessageEventFactory::getEvent()
     * @covers \REW\Backend\Dashboard\AbstractEventFactory::getEvent()
     */
    public function testGetEvent()
    {

        // Prepare Event Id
        $eventId= m::mock(EventIdInterface::class);
        $eventId->shouldReceive('getHash')
            ->once()
            ->andReturn('173481421::87');
        $eventId->shouldReceive('getMode')
            ->once()
            ->andReturn('message');
        $eventId->shouldReceive('getTimestamp')
            ->once()
            ->andReturn(173481421);
        $eventId->shouldReceive('getId')
            ->once()
            ->andReturn(87);

        // Prepare DB Query
        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')
            ->once()
            ->with(['id' => 87]);
        $stmt->shouldReceive('fetch')
            ->once()
            ->andReturn(['user_id' => 31, 'first_name' => 'Matthew', 'last_name' => 'Brown', 'email' => 'test@realestatewebmasters.com', 'phone_cell' => '250 111 1111', 'image' =>  null, 'agent' => 4, 'status' => 'pending', 'agent_first_name' => 'Test', 'agent_last_name' => 'Agent', 'message_id' => 87, 'subject' => 'Test', 'message' => 'This is a test message.']);
        $this->db->shouldReceive('prepare')
            ->with('SELECT `u`.`id` AS \'user_id\', `u`.`first_name`, `u`.`last_name`, `u`.`status`, `u`.`email`, `u`.`phone_cell`, `um`.`agent_id` AS \'agent\', `u`.`image`, `a`.`first_name` AS \'agent_first_name\', `a`.`last_name` AS \'agent_last_name\', `um`.`id` AS \'message_id\', `um`.`subject`, `um`.`message` FROM ' . LM_TABLE_MESSAGES . ' `um` JOIN ' . LM_TABLE_LEADS . ' `u` ON `u`.`id` = `um`.`user_id` LEFT JOIN ' . LM_TABLE_AGENTS . ' `a` ON `a`.`id` = `um`.`agent_id` WHERE `um`.`id` = :id')
            ->once()
            ->andReturn($stmt);

        // Prepare Cache
        $this->cache->shouldReceive('getCache')
            ->with('REW\Backend\Dashboard\AbstractEventFactory:173481421::87')
            ->once()
            ->andReturn(null);
        $this->cache->shouldReceive('setCache')
            ->once();

        $messageEventFactory = new MessageEventFactory($this->db, $this->auth, $this->settings, $this->format, $this->cache);
        $event = $messageEventFactory->getEvent($eventId);

        $this->assertInternalType('array', $event);
        $this->assertArrayHasKey('hash', $event);
        $this->assertEquals('173481421::87', $event['hash']);
        $this->assertArrayHasKey('mode', $event);
        $this->assertEquals('message', $event['mode']);
        $this->assertArrayHasKey('timestamp', $event);
        $this->assertEquals('173481421', $event['timestamp']);
        $this->assertArrayHasKey('data', $event);
        $this->assertInternalType('array', $event['data']);

        $this->assertArrayHasKey('lead', $event['data']);
        $this->assertInternalType('array', $event['data']['lead']);
        $this->assertEquals('31', $event['data']['lead']['id']);
        $this->assertEquals('Matthew Brown', $event['data']['lead']['name']);
        $this->assertEquals('test@realestatewebmasters.com', $event['data']['lead']['email']);
        $this->assertEquals('backend/email/?id=31&type=leads&redirect=backend/', $event['data']['lead']['emailLink']);
        $this->assertEquals('250 111 1111', $event['data']['lead']['phone']);
        $this->assertEquals('tel:+250-111-1111', $event['data']['lead']['phoneLink']);
        $this->assertEquals('backend/leads/lead/summary/?id=31', $event['data']['lead']['link']);
        $this->assertEquals('4', $event['data']['lead']['agent']);
        $this->assertEquals('Test Agent', $event['data']['lead']['agentName']);
        $this->assertEquals('backend/agents/agent/summary/?id=4', $event['data']['lead']['agentLink']);
        $this->assertEquals('b', $event['data']['lead']['defaultClass']);
        $this->assertEquals('MB', $event['data']['lead']['defaultText']);
        $this->assertEquals(null, $event['data']['lead']['image']);
        $this->assertEquals('pending', $event['data']['lead']['status']);

        $this->assertArrayHasKey('message', $event['data']);
        $this->assertInternalType('array', $event['data']['message']);
        $this->assertEquals('87', $event['data']['message']['id']);
        $this->assertEquals('Test', $event['data']['message']['subject']);
        $this->assertEquals('This is a test message.', $event['data']['message']['body']);
    }
}
