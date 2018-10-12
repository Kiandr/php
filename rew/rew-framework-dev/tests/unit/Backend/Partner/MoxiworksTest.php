<?php
namespace REW\Test\Backend\Partner;

use Mockery as m;
use REW\Backend\Partner\Moxiworks;
use REW\Core\Interfaces\SettingsInterface;
use Backend_Lead;
use DB;
use Http_Host;
use UnexpectedValueException;

class MoxiworksTest extends \Codeception\Test\Unit
{
    /**
     * @var m \MockInterface|DBInterface
     */
    protected $db;

    /**
     * @var Http_Host
     */
    protected $http_host;

    /**
     * @var m \MockInterface|SettingsInterface
     */
    protected $settings;

    protected function _before()
    {
        $_SERVER['HTTP_HOST'] = 'fake.rewdev.com';
        $this->db = m::mock(DB::class);
        $this->http_host = new Http_Host();
        $this->settings = m::mock(SettingsInterface::class);
    }

    protected function _after()
    {
        m::close();
    }

    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * @covers \REW\Backend\Partner\Moxiworks::__construct()
     */
    public function testConstruct()
    {
        $moxiworks = new Moxiworks($this->http_host, $this->settings);
        $this->assertInstanceOf('REW\Backend\Partner\Moxiworks', $moxiworks);
    }

    /**
     * @covers \REW\Backend\Partner\Moxiworks::decodeJsonResponse()
     * API Success Response
     */
    public function testDecodeJsonResponseSuccess()
    {
        $response = ['body' => 'some body'];
        $json_response = json_encode($response);

        $moxiworks = new Moxiworks($this->http_host, $this->settings);
        $this->assertEquals($response, $this->invokeMethod($moxiworks, 'decodeJsonResponse', [$json_response]));
    }

    /**
     * @covers \REW\Backend\Partner\Moxiworks::decodeJsonResponse()
     * API Error Response
     */
    public function testDecodeJsonResponseError()
    {
        $response = ['status' => 'error', 'body' => 'some body'];
        $json_response = json_encode($response);

        $moxiworks = new Moxiworks($this->http_host, $this->settings);
        $this->expectException(UnexpectedValueException::class);
        $this->invokeMethod($moxiworks, 'decodeJsonResponse', [$json_response]);
    }

    /**
     * @covers \REW\Backend\Partner\Moxiworks::generateContactId()
     */
    public function testGenerateContactId()
    {
        $test_id = 123;
        $expected_id = sprintf(
            'REW-%s-%s',
            preg_replace('/[^a-zA-Z0-9]/', '-', $this->http_host->getHost()),
            $test_id
        );

        $moxiworks = new Moxiworks($this->http_host, $this->settings);
        $this->assertEquals($expected_id, $this->invokeMethod($moxiworks, 'generateContactId', [$test_id]));
    }

    /**
     * @covers \REW\Backend\Partner\Moxiworks::getPushRequestParameters()
     */
    public function testGetPushRequestParameters()
    {
        $test_id = 123;

        $lead_data = [
            'id' => $test_id,
            'first_name' => 'Fake',
            'last_name' => 'McContact',
            'email' => 'fake@test.ca',
            'phone' => '5555555555',
            'address1' => '123 Fake St',
            'city' => 'Fakeville',
            'state' => 'AA',
            'zip' => '12345'
        ];

        $expected_response = [
            'agent_uuid' => null,
            'partner_contact_id' => sprintf(
                'REW-fake-rewdev-com-%s',
                $lead_data['id']
            ),
            'contact_name' => sprintf(
                '%s %s',
                $lead_data['first_name'],
                $lead_data['last_name']
            ),
            'primary_email_address' => $lead_data['email'],
            'primary_phone_number' => $lead_data['phone'],
            'home_street_address' => $lead_data['address1'],
            'home_city' => $lead_data['city'],
            'home_state' => $lead_data['state'],
            'home_zip' => $lead_data['zip']
        ];

        // Build Mock Lead Object with Static Data
        $lead = m::mock(Backend_Lead::class, [$lead_data, &$this->db])->makePartial();

        $moxiworks = new Moxiworks($this->http_host, $this->settings);
        $this->assertEquals($expected_response, $this->invokeMethod($moxiworks, 'getPushRequestParameters', [$lead]));
    }

    /**
     * @covers \REW\Backend\Partner\Moxiworks::getIndexRequestParameters()
     */
    public function testGetIndexRequestParameters()
    {
        $email_address = 'test@fake.ca';

        $expected_response = [
            'agent_uuid' => null,
            'email_address' => $email_address,
        ];

        $moxiworks = new Moxiworks($this->http_host, $this->settings);
        $this->assertEquals($expected_response, $this->invokeMethod($moxiworks, 'getIndexRequestParameters', [$email_address]));
    }

}