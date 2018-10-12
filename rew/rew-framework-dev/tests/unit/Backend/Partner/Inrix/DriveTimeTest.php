<?php
namespace REW\Test\Backend\Partner\Inrix;

use Mockery as m;
use REW\Backend\Partner\Inrix\DriveTime;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;

class DriveTimeTest extends \Codeception\Test\Unit
{
    /**
     * @var m \MockInterface|DBInterface
     */
    protected $db;

    /**
     * @var m \MockInterface|SettingsInterface
     */
    protected $settings;

    protected function _before()
    {
        $this->db = m::mock(DBInterface::class);
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
     * @covers \REW\Backend\Partner\Inrix\DriveTime::__construct()
     */
    public function testConstruct()
    {
        $drivetime = new DriveTime($this->db, $this->settings);
        $this->assertInstanceOf('REW\Backend\Partner\Inrix\DriveTime', $drivetime);
    }

    /**
     * @covers \REW\Backend\Partner\Inrix\DriveTime::checkMapMarkerDuplicate()
     * Values Match
     */
    public function testCheckMapMarkerDuplicateMatch()
    {
        $dt_address = '123 Fake blvd';
        $mls_address = '123 Fake blvd';

        $drivetime = new DriveTime($this->db, $this->settings);
        $this->assertEquals(true, $this->invokeMethod($drivetime, 'checkMapMarkerDuplicate', [$dt_address, $mls_address]));
    }

    /**
     * @covers \REW\Backend\Partner\Inrix\DriveTime::checkMapMarkerDuplicate()
     * Values Don't Match
     */
    public function testCheckMapMarkerDuplicateNoMatch()
    {
        $dt_address = '1234 Fakke st';
        $mls_address = '123 Fake blvd';

        $drivetime = new DriveTime($this->db, $this->settings);
        $this->assertEquals(false, $this->invokeMethod($drivetime, 'checkMapMarkerDuplicate', [$dt_address, $mls_address]));
    }

    /**
     * @covers \REW\Backend\Partner\Inrix\DriveTime::getArrivalTimeOptions()
     */
    public function testGetArrivalTimeOptions()
    {
        $expected = [
            ['value' => '11:00', 'display' => '11:00 am'],
            ['value' => '11:15', 'display' => '11:15 am'],
            ['value' => '11:30', 'display' => '11:30 am'],
            ['value' => '11:45', 'display' => '11:45 am'],
            ['value' => '12:00', 'display' => '12:00 pm'],
            ['value' => '12:15', 'display' => '12:15 pm'],
            ['value' => '12:30', 'display' => '12:30 pm'],
            ['value' => '12:45', 'display' => '12:45 pm'],
            ['value' => '13:00', 'display' => '1:00 pm']
        ];

        $drivetime = new DriveTime($this->db, $this->settings);
        $this->assertEquals($expected, $this->invokeMethod($drivetime, 'getArrivalTimeOptions', [11, 13]));
    }

    /**
     * @covers \REW\Backend\Partner\Inrix\DriveTime::getTravelDurationOptions()
     */
    public function testGetTravelDurationOptions()
    {
        $expected = [
            ['value' => '15', 'display' => '15 min'],
            ['value' => '25', 'display' => '25 min'],
            ['value' => '35', 'display' => '35 min'],
            ['value' => '45', 'display' => '45 min']
        ];

        $drivetime = new DriveTime($this->db, $this->settings);
        $this->assertEquals($expected, $this->invokeMethod($drivetime, 'getTravelDurationOptions', [15, 45, 10]));
    }

    /**
     * @covers \REW\Backend\Partner\Inrix\DriveTime::generatePolygonString()
     */
    public function testGeneratePolygonString()
    {
        $polys = [
            '30.4999780654907 -97.9882621765137',
            '30.6273078918457 -97.9436731338501',
            '30.66734790802 -97.9101133346558'
        ];

        $expected = sprintf('["%s"]', implode(',', $polys));

        $drivetime = new DriveTime($this->db, $this->settings);
        $this->assertEquals($expected, $this->invokeMethod($drivetime, 'generatePolygonString', [[
            implode(' ', $polys)
        ]]));
    }

    /**
     * @covers \REW\Backend\Partner\Inrix\DriveTime::generatePolygonApiEndpoint()
     */
    public function testGeneratePolygonApiEndpoint()
    {
        $test_vals = [
            'test_server',
            'test_token',
            'test_lat',
            'test_lng',
            'test_dir',
            'test_dur',
            'test_time'
        ];

        $expected = sprintf(
            '%s?Action=GetDriveTimePolygons&Center=%s|%s&RangeType=%s&Duration=%s&DateTime=%s&Token=%s&vendorid=%s&consumerid=%s',
            $test_vals[1],
            $test_vals[2],
            $test_vals[3],
            $test_vals[4],
            $test_vals[5],
            $test_vals[6],
            $test_vals[0],
            DriveTime::API_VENDOR_KEY,
            DriveTime::API_CONSUMER_KEY
        );

        $drivetime = new DriveTime($this->db, $this->settings);
        $this->assertEquals($expected, $this->invokeMethod($drivetime, 'generatePolygonApiEndpoint', $test_vals));
    }

    /**
     * @covers \REW\Backend\Partner\Inrix\DriveTime::findOffsetFromUTC()
     */
    public function testFindOffsetFromUTC()
    {
        $timezone = 'America/Chicago';
        $expected = '05:00';

        $drivetime = new DriveTime($this->db, $this->settings);
        $this->assertEquals($expected, $this->invokeMethod($drivetime, 'findOffsetFromUTC', [$timezone]));
    }

}