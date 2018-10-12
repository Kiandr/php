<?php

namespace REW\Test\Backend\Page;

use Mockery as m;
use REW\Backend\Interfaces\Page\TimelineInterface;
use REW\Backend\Page\Timeline;
use REW\Backend\Page\TimelineFactory;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;

class TimelineFactoryTest extends \Codeception\Test\Unit
{
    /**
     * @var \REW\Test\UnitTester
     */
    protected $tester;

    /**
     * @var m\MockInterface|DBInterface
     */
    private $db;

    /**
     * @var m\MockInterface|SettingsInterface
     */
    private $settings;

    protected function _before()
    {
        $this->db = m::mock(DBInterface::class);
        $this->settings = m::mock(SettingsInterface::class);
    }

    protected function _after()
    {
        m::close();
    }

    /**
     * @covers \REW\Backend\Page\TimelineFactory
     */
    public function testTimelineIntegration()
    {
        // We need to validate that a timeline can be created with the expected arguments since our PDO::fetchObject
        // will use them directly instead of auto-wiring. This is just so that if they change the test will break rather
        // than an unexpected bug being introduced. We're also initializing directly in build since why not? (we already
        // need to know the arguments for PDO.) This ensures both work as expected.
        $factory = m::mock(TimelineFactory::class);
        $this->assertInstanceOf(
            TimelineInterface::class,
            $timeline = new Timeline($this->db, $this->settings, $factory, 'url', $get = ['id' => 123])
        );
        $this->assertEquals('url', $timeline->getUrl());
        $this->assertEquals($get, $timeline->getGet());
    }

    /**
     * @covers \REW\Backend\Page\TimelineFactory::__construct()
     * @covers \REW\Backend\Page\TimelineFactory::load()
     */
    public function testLoad()
    {
        $factory = new TimelineFactory($this->db, $this->settings);

        $this->settings->shouldReceive('offsetGet')->once()->with('TABLES')
            ->andReturn(['TIMELINE_PAGES' => 'tbltimeline']);

        $this->assertTrue(defined(Timeline::class . '::CONVERT_BINARY_TO_GUID'));
        preg_match('/%s/', Timeline::CONVERT_BINARY_TO_GUID, $matches);
        $this->assertCount(1, $matches, 'Constant must contain "%s"');
        $toGuid = sprintf(Timeline::CONVERT_BINARY_TO_GUID, "`last_page_guid`");

        $this->assertTrue(defined(Timeline::class . '::CONVERT_GUID_TO_BINARY'));
        preg_match('/%s/', Timeline::CONVERT_GUID_TO_BINARY, $matches);
        $this->assertCount(1, $matches, 'Constant must contain "%s"');
        $toBinary = sprintf(Timeline::CONVERT_GUID_TO_BINARY, ':guid');

        $this->db->shouldReceive('prepare')->once()
            ->with(
                "SELECT :guid AS `guid`, `url`, $toGuid AS `lastPageGuid` FROM `tbltimeline` WHERE `guid` = $toBinary"
            )->andReturn($stmt = m::mock(\PDOStatement::class));
        $stmt->shouldReceive('execute')->once();

        $stmt->shouldReceive('fetchObject')->once()
            ->with(Timeline::class, [$this->db, $this->settings, $factory])
            ->andReturn($timeline = m::mock(Timeline::class));

        $this->assertEquals($timeline, $factory->load('1234'));
    }

    /**
     * @covers \REW\Backend\Page\TimelineFactory::__construct()
     * @covers \REW\Backend\Page\TimelineFactory::build()
     */
    public function testBuild()
    {
        $factory = new TimelineFactory($this->db, $this->settings);

        $timeline = $factory->build('url', $get = ['id' => 1234]);

        $this->assertEquals('url', $timeline->getUrl());
        $this->assertEquals($get, $timeline->getGet());
    }
}
