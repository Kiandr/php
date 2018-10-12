<?php

namespace REW\Test\Backend\Page;

use Mockery as m;
use REW\Backend\Interfaces\Page\TimelineInterface;
use REW\Backend\Page\Timeline;
use REW\Backend\Page\TimelineFactory;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;

class TimelineTest extends \Codeception\Test\Unit
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

    /**
     * @var m\MockInterface|TimelineFactory
     */
    private $factory;

    protected function _before()
    {
        $this->db = m::mock(DBInterface::class);
        $this->settings = m::mock(SettingsInterface::class);
        $this->factory = m::mock(TimelineFactory::class);
    }

    protected function _after()
    {
        m::close();
    }

    /**
     * @covers \REW\Backend\Page\Timeline::__construct()
     * @param string $expectedUrl
     * @param string $inputUrl
     * @param string $preInputUrl
     * @param array $expectedGet
     * @param array $inputGet
     * @dataProvider constructDataProvider
     */
    public function testConstruct($expectedUrl, $inputUrl, $preInputUrl, $expectedGet, $inputGet)
    {
        if (isset($preInputUrl)) {
            // Use reflection to inject a url (mimic PDO::fetchObject)
            $reflector = new \ReflectionClass(Timeline::class);
            $timeline = $reflector->newInstanceWithoutConstructor();
            $url = $reflector->getProperty('url');
            $url->setAccessible(true);
            $url->setValue($timeline, $preInputUrl);
            $reflector->getConstructor()->invoke(
                $timeline,
                $this->db,
                $this->settings,
                $this->factory,
                $inputUrl,
                $inputGet
            );
        } else {
            $timeline = new Timeline($this->db, $this->settings, $this->factory, $inputUrl, $inputGet);
        }

        $this->assertEquals($expectedUrl, $timeline->getUrl());
        $this->assertEquals($expectedGet, $timeline->getGet());
    }

    /**
     * @covers \REW\Backend\Page\Timeline::getLink()
     */
    public function testGetLink()
    {
        $timeline = new Timeline($this->db, $this->settings, $this->factory, null, ['id' => 123]);

        $this->assertEquals('?id=123&' . Timeline::MODE . '=foo', $timeline->getLink('foo'));
    }

    /**
     * @covers \REW\Backend\Page\Timeline::encode()
     * @covers \REW\Backend\Page\Timeline::setLast()
     */
    public function testEncodeWithLastPage()
    {
        $timeline = new Timeline($this->db, $this->settings, $this->factory, null, $get = ['id' => 123]);
        $timeline->setLast($last = m::mock(TimelineInterface::class));
        $last->shouldReceive('getGUID')->once()->andReturn('guid');

        $this->assertEquals(json_encode(['url' => null, 'get' => $get, 'lastPage' => 'guid']), $timeline->encode());
    }

    /**
     * @covers \REW\Backend\Page\Timeline::getLast()
     */
    public function testGetLast()
    {
        // Use reflection to inject a lastPageGuid (mimic PDO::fetchObject which is the only way to inject an id other
        // than saving a new record.
        $reflector = new \ReflectionClass(Timeline::class);
        $timeline = $reflector->newInstanceWithoutConstructor();
        $lastPageGuid = $reflector->getProperty('lastPageGuid');
        $lastPageGuid->setAccessible(true);
        $lastPageGuid->setValue($timeline, 'guid');
        $reflector->getConstructor()->invoke(
            $timeline,
            $this->db,
            $this->settings,
            $this->factory
        );
        $this->factory->shouldReceive('load')->once()->with('guid')
            ->andReturn($last = m::mock(TimelineInterface::class));

        // Test load
        $this->assertEquals($last, $timeline->getLast());

        // Second should not call any more mocks as it should be cached
        $this->assertEquals($last, $timeline->getLast());
    }

    /**
     * @covers \REW\Backend\Page\Timeline::clearPast()
     */
    public function testClearPastNoPast()
    {
        $timeline = new Timeline($this->db, $this->settings, $this->factory, null, $get = ['id' => 123]);

        $timeline->clearPast();
    }

    /**
     * Tests clearPast recursion - 3 levels to ensure all database queries are run in the correct order.
     * This uses separate databases so that it's easier to track what is going where.
     * @covers \REW\Backend\Page\Timeline::clearPast()
     * @covers \REW\Backend\Page\Timeline::setGUID()
     * @dataProvider clearPastDataProvider
     * @param bool $failure
     */
    public function testClearPast($failure)
    {
        $db2 = m::mock(DBInterface::class);
        $db3 = m::mock(DBInterface::class);
        $timeline = new Timeline($this->db, $this->settings, $this->factory, null, $get = ['id' => 123]);
        $timeline->setLast(
            $last = new Timeline($db2, $this->settings, $this->factory, null, $get = ['id' => 456])
        );
        $last->setLast(
            $lastChild = new Timeline($db3, $this->settings, $this->factory, null, $get = ['id' => 789])
        );
        $last->setGUID('last');
        $lastChild->setGUID('lastChild');

        $toBinary = sprintf(Timeline::CONVERT_GUID_TO_BINARY, ':guid');
        $del = "DELETE FROM `pgtbl` WHERE `guid` = " . $toBinary;

        // Order doesn't need to be tested
        $this->settings->shouldReceive('offsetGet')->atLeast(1)->with('TABLES')
            ->andReturn(['TIMELINE_PAGES' => 'pgtbl']);
        $this->db->shouldReceive('prepare')->once()
            ->with($del)
            ->andReturn($stmt1 = m::mock(\PDOStatement::class));
        $db2->shouldReceive('prepare')->once()
            ->with($del)
            ->andReturn($stmt2 = m::mock(\PDOStatement::class));
        $db2->shouldReceive('inTransaction')->once()->andReturn(true);

        // Order needs to be tested
        $this->db->shouldReceive('beginTransaction')->once()->globally()->ordered();
        $stmt2->shouldReceive('execute')->once()->with(['guid' => 'lastChild'])->globally()->ordered();
        $m = $stmt1->shouldReceive('execute')->once()->with(['guid' => 'last'])->globally()->ordered();

        if ($failure) {
            $this->db->shouldReceive('inTransaction')->twice()->andReturnValues([
                false,
                true
            ]);
            $m->andThrow(new \PDOException('exc'));
            $this->db->shouldReceive('rollBack')->once()->ordered();
            try {
                $timeline->clearPast();
            } catch (\Exception $e) {
                $this->assertInstanceOf(\PDOException::class, $e);
            }
        } else {
            $this->db->shouldReceive('inTransaction')->once()->andReturn(false);
            $this->db->shouldReceive('commit')->once()->ordered();
            $timeline->clearPast();
        }
    }

    /**
     * @covers \REW\Backend\Page\Timeline::encode()
     */
    public function testEncodeNoLastPage()
    {
        $timeline = new Timeline($this->db, $this->settings, $this->factory, null, $get = ['id' => 123]);

        $this->assertEquals(json_encode(['url' => null, 'get' => $get, 'lastPage' => null]), $timeline->encode());
    }

    /**
     * @param string $expected
     * @param string $inputUrl
     * @param string $compareUrl
     * @param array|null $inputGet
     * @param array|null $compareGet
     * @covers \REW\Backend\Page\Timeline::compare()
     * @dataProvider compareDataProvider
     */
    public function testCompare($expected, $inputUrl, $compareUrl, $inputGet, $compareGet)
    {
        $timeline = new Timeline($this->db, $this->settings, $this->factory, $inputUrl, $inputGet);
        $timelineCompare = m::mock(TimelineInterface::class);
        $timelineCompare->shouldReceive('getUrl')->andReturn($compareUrl);
        $timelineCompare->shouldReceive('getGet')->andReturn($compareGet);

        $this->assertEquals($expected, $timeline->compare($timelineCompare));
    }

    /**
     * @covers \REW\Backend\Page\Timeline::getGUID()
     * @covers \REW\Backend\Page\Timeline::setGUID()
     */
    public function testGetSetGUID()
    {
        $timeline = new Timeline($this->db, $this->settings, $this->factory);

        $timeline->setGUID('abc');
        $this->assertEquals('abc', $timeline->getGUID());
    }

    /**
     * @covers \REW\Backend\Page\Timeline::getUrl()
     */
    public function testGetUrl()
    {
        $timeline = new Timeline($this->db, $this->settings, $this->factory, 'foo');

        $this->assertEquals('foo', $timeline->getUrl());
    }

    /**
     * @covers \REW\Backend\Page\Timeline::getGet()
     */
    public function testGetGetNotSaved()
    {
        $timeline = new Timeline($this->db, $this->settings, $this->factory, 'foo');

        $this->assertEquals([], $timeline->getGet());
    }

    /**
     * @covers \REW\Backend\Page\Timeline::getGet()
     */
    public function testGetGetSaved()
    {
        $timeline = new Timeline($this->db, $this->settings, $this->factory, 'foo');

        $toBinary = sprintf(Timeline::CONVERT_GUID_TO_BINARY, ':guid');

        $this->settings->shouldReceive('offsetGet')->once()->with('TABLES')
            ->andReturn(['TIMELINE_PAGE_VARIABLES' => 'tblvar']);
        $this->db->shouldReceive('prepare')->once()
            ->with("SELECT `value` FROM `tblvar` WHERE `page_guid` = " . $toBinary . " AND `key` = :key")
            ->andReturn($stmt = m::mock(\PDOStatement::class));
        $stmt->shouldReceive('execute')->once()->with(['guid' => 'guid', 'key' => 'get']);
        $stmt->shouldReceive('fetchColumn')->once()->with(0)
            ->andReturn(json_encode($e = ['foo' => 'bar']));

        $timeline->setGUID('guid');
        $this->assertEquals($e, $timeline->getGet());
    }

    /**
     * @covers \REW\Backend\Page\Timeline::save()
     * @param bool $isNew
     * @param bool $hasVariables
     * @param mixed $lastPageGuid
     * @param bool $failPage
     * @param bool $failVariables
     * @dataProvider saveDataProvider
     */
    public function testSave($isNew, $hasVariables, $lastPageGuid, $failPage = false, $failVariables = false)
    {
        $timeline = m::mock(
            Timeline::class . '[generateRandomGUID]',
            [$this->db, $this->settings, $this->factory, 'foo', $hasVariables ? ['id' => 123] : null]
        );

        $toBinary = sprintf(Timeline::CONVERT_GUID_TO_BINARY, ':guid');
        $lpToBinary = sprintf(Timeline::CONVERT_GUID_TO_BINARY, ':last_page_guid');

        $this->settings->shouldReceive('offsetGet')->with('TABLES')->andReturn([
            'TIMELINE_PAGES' => 'tblpages',
            'TIMELINE_PAGE_VARIABLES' => 'tblvariables'
        ]);

        if ($isNew) {
            $timeline->shouldReceive('generateRandomGUID')->once()->andReturn('bar');
            $this->db->shouldReceive('prepare')->once()
                ->with(
                    "INSERT INTO `tblpages` SET `url` = :url, `guid` = " . $toBinary . ","
                        . " `last_page_guid` = " . $lpToBinary . ",  `timestamp_updated` = NOW()"
                )->andReturn($stmt = m::mock(\PDOStatement::class));
        } else {
            $timeline->setGUID('bar');
            $this->db->shouldReceive('prepare')->once()
                ->with(
                    "UPDATE `tblpages` SET `url` = :url, `last_page_guid` = " . $lpToBinary . ",  `timestamp_updated` = NOW()"
                        . " WHERE `guid` = " . $toBinary
                )->andReturn($stmt = m::mock(\PDOStatement::class));
        }

        if ($lastPageGuid) {
            $timeline->setLast($last = m::mock(TimelineInterface::class));
            $last->shouldReceive('getGUID')->andReturn($lastPageGuid);
        }

        $m = $stmt->shouldReceive('execute')->once()
            ->with(['url' => 'foo', 'guid' => 'bar', 'last_page_guid' => $lastPageGuid]);
        if ($failPage) {
            $m->andThrow(new \PDOException('error page'));
        }

        $this->db->shouldReceive('beginTransaction')->once();

        if ($failPage) {
            // don't expect the variable queries
        } elseif ($hasVariables) {
            $this->db->shouldReceive('prepare')->once()
                ->with(
                    "INSERT INTO `tblvariables` SET `page_guid` = " . $toBinary . ", `key` = :key,"
                        . " `value` = :value ON DUPLICATE KEY UPDATE `timestamp_updated` = NOW(),"
                        . " `value` = VALUES(`value`)"
                )->andReturn($stmt = m::mock(\PDOStatement::class));
            $m = $stmt->shouldReceive('execute')->once()
                ->with(['guid' => 'bar', 'key' => 'get', 'value' => json_encode(['id' => 123])]);
        } else {
            $this->db->shouldReceive('prepare')->once()
                ->with("DELETE FROM `tblvariables` WHERE `page_guid` = " . $toBinary . " AND `key` = 'get'")
                ->andReturn($stmt = m::mock(\PDOStatement::class));
            $m = $stmt->shouldReceive('execute')->once()->with(['guid' => 'bar']);
        }
        if ($failVariables) {
            $m->andThrow(new \PDOException('error variables'));
        }

        if ($failPage || $failVariables) {
            $this->db->shouldReceive('rollBack')->once();

            try {
                $timeline->save();
            } catch (\PDOException $e) {
                if ($failPage) {
                    $this->assertEquals('error page', $e->getMessage());
                } else {
                    $this->assertEquals('error variables', $e->getMessage());
                }
            }
        } else {
            $this->db->shouldReceive('commit')->once();

            $timeline->save();
        }
    }

    /**
     * @covers \REW\Backend\Page\Timeline::generateRandomGUID()
     */
    public function testGenerateRandomGUID()
    {
        $timeline = new Timeline($this->db, $this->settings, $this->factory, 'foo');

        $pool = [];

        // If we get 10 uniques, lets be happy with that.
        for ($i = 0; $i < 10; $i++) {
            $guid = $timeline->generateRandomGUID();

            $this->assertRegExp('/[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}/', $guid);
            $this->assertNotContains($guid, $pool);
            $pool[] = $guid;
        }
    }

    /**
     * data provider for constructor tests
     * @return array
     */
    public function constructDataProvider()
    {
        // $expectedUrl, $inputUrl, $preInputUrl, $expectedGet, $inputGet
        $savedVarsTests = [];
        // test all constants
        foreach (Timeline::SAVED_VARS as $savedVar) {
            $savedVarsTests[] =             [
                'url',
                'url',
                null,
                [$savedVar => 123],
                [$savedVar => 123, 'foo' => 'bar']
            ];
        }

        return array_merge($savedVarsTests, [
            [
                // no valid params
                'url',
                'url',
                null,
                [],
                ['foo' => 'bar']
            ],
            [
                // no params
                'url',
                'url',
                null,
                [],
                []
            ],
            [
                // null params
                'url',
                'url',
                null,
                [],
                null
            ],
            [
                // pre-set url and url passed
                'pre url',
                'url',
                'pre url',
                [],
                null
            ],
            [
                // empty pre-set url and url passed
                '',
                'url',
                '',
                [],
                null
            ],
            [
                // null pre-set url and passed url
                null,
                null,
                null,
                [],
                null
            ],
            [
                // pre-set url and no passed url
                'pre',
                null,
                'pre',
                [],
                null
            ],
        ]);
    }

    /**
     * data provider for clearPast
     * @return array
     */
    public function clearPastDataProvider()
    {
        return [
            [
                true
            ],
            [
                false
            ]
        ];
    }

    /**
     * data provider for compare
     * @return array
     */
    public function compareDataProvider()
    {
        //$expected, $inputUrl, $compareUrl, $inputGet, $compareGet
        return [
            [
                // matching url, matching get
                true,
                'url',
                'url',
                ['id' => 123],
                ['id' => 123],
            ],
            [
                // matching url, non-matching get
                false,
                'url',
                'url',
                ['id' => 123],
                ['id' => 456],
            ],
            [
                // non-matching url, matching get
                false,
                'url',
                'url2',
                ['id' => 123],
                ['id' => 123],
            ],
            [
                // non-matching url, non-matching get
                false,
                'url',
                'url2',
                ['id' => 123],
                ['id' => 456],
            ],
        ];
    }

    /**
     * data provider for save
     * @return array
     */
    public function saveDataProvider()
    {
        // $isNew, $hasVariables, $lastPageGuid, $failPage, $failVariables
        return [
            [
                false,
                false,
                null,
            ],
            [
                false,
                true,
                null,
            ],
            [
                true,
                false,
                null,
            ],
            [
                true,
                true,
                null,
            ],
            [
                false,
                false,
                123,
            ],
            [
                false,
                true,
                123,
            ],
            [
                true,
                false,
                123,
            ],
            [
                true,
                true,
                123,
            ],
            // Test exceptions
            [
                // fail page
                true,
                true,
                123,
                true
            ],
            [
                // fail variables
                true,
                true,
                123,
                false,
                true
            ],
        ];
    }
}
