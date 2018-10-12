<?php
namespace REW\Test\Seed\Dynamic;

use Codeception\Test\Unit;
use Mockery as m;
use REW\Seed\Dynamic\UserFormsDataSeeder;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\SettingsInterface;

class UserFormsDataSeederTest extends Unit
{
    /**
     * @var m \MockInterface|DBInterface
     */
    protected $db;

    /**
     * @var m \MockInterface|IDXFactoryInterface
     */
    protected $idxFactory;

    /**
     * @var m \MockInterface|SettingsInterface
     */
    protected $settings;

    protected function _before()
    {
        $this->db = m::mock(DBInterface::class);
        $this->settings = m::mock(SettingsInterface::class);
        $this->idxFactory = m::mock(IDXFactoryInterface::class);
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
     * @covers \REW\Seed\Dynamic\UserFormsDataSeeder::buildUserFormsRow()
     */
    public function testBuildUserFormsRow()
    {
        $args = ['inquiry', '12345', 'residential', 'abor', 'This is a comment!'];
        $expected = ' (:lead_id, \'inquiry\', \'a:5:{s:4:"form";s:7:"inquiry";s:10:"ListingMLS";s:5:"12345";s:11:"ListingType";s:11:"residential";s:11:"ListingFeed";s:4:"abor";s:8:"comments";s:18:"This is a comment!";}\', NULL, NOW()), ';

        $userFormsSeeder = new UserFormsDataSeeder($this->db, $this->idxFactory, $this->settings);
        $this->assertEquals($expected, $this->invokeMethod($userFormsSeeder, 'buildUserFormsRow', $args));
    }

    /**
     * Success
     * @covers \REW\Seed\Dynamic\UserFormsDataSeeder::buildUserFormsQuery()
     */
    public function testBuildUserFormsQuerySuccess()
    {
        $listings = [];
        for ($i = 0; $i < 8; $i++) {
            $listings[$i]['idx'] = 'fakefeed';
            foreach (UserFormsDataSeeder::MLS_LISTING_FETCH_FIELDS as $reqField) {
                $listings[$i][$reqField] = 'something';
            }
        }

        $userFormsSeeder = new UserFormsDataSeeder($this->db, $this->idxFactory, $this->settings);
        $this->assertNotEmpty($this->invokeMethod($userFormsSeeder, 'buildUserFormsQuery', [$listings]));
    }

    /**
     * Failure
     * @covers \REW\Seed\Dynamic\UserFormsDataSeeder::buildUserFormsQuery()
     */
    public function testBuildUserFormsQueryFail()
    {
        $listings = [];

        try {
            $userFormsSeeder = new UserFormsDataSeeder($this->db, $this->idxFactory, $this->settings);
            $this->invokeMethod($userFormsSeeder, 'buildUserFormsQuery', [$listings]);
        } catch (\Exception $e) {
            return true;
        }
        $this->fail('Failed to catch expected Exception');
    }

}