<?php
namespace REW\Test\Datastore\Office;

use REW\Core\Interfaces\Factories\DBFactoryInterface;
use REW\Core\Interfaces\FormatInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Datastore\Office\SearchDatastore;
use REW\Model\Office\Search\OfficeRequest;
use REW\Model\Office\Search\OfficeResult;
use REW\Factory\Office\OfficeFactory;
use Mockery as m;

class SearchDatastoreTest extends \Codeception\Test\Unit
{
    /** @var int */
    const LIMIT = 16;

    /**
     * @var SearchDatastore
     */
    protected $searchDatastore;

    /**
     * @var \REW\Core\Interfaces\Factories\DBFactoryInterface
     */
    protected $dbFactory;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var FormatInterface
     */
    protected $format;

    /**
     * @var OfficeFactory
     */
    protected $officeFactory;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->dbFactory = m::mock(DBFactoryInterface::class);
        $this->settings = m::mock(SettingsInterface::class);
        $this->format = m::mock(FormatInterface::class);
        $this->officeFactory = new OfficeFactory($this->format);
    }

    /**
     * @covers REW\Datastore\Office\SearchDatastore::getOffices
     * @return void
     */
    public function testGetOffices()
    {
        $mockedStmt = m::mock(\PDOStatement::class);
        $mockedStmt->shouldReceive('execute')
            ->with(['Toron'])
            ->andReturn(true);
        $mockedStmt->shouldReceive('fetchAll')
            ->andReturn($this->getDemoData());
        $mockedPdo = m::mock(\PDO::class);
        $mockedPdo->shouldReceive('prepare')
            ->with('SELECT `o`.`id`, `o`.`title`, `o`.`description`, `o`.`email`, `o`.`phone`, `o`.`fax`, `o`.`address`, `o`.`city`, `o`.`state`, `o`.`zip`, `o`.`display`, `o`.`image`, `o`.`sort` FROM `featured_offices` `o` WHERE `o`.`title` LIKE CONCAT("%", ?, "%") ORDER BY `o`.`title` ASC LIMIT 16;')
            ->andReturn($mockedStmt);
        $this->dbFactory->shouldReceive('get')
            ->andReturn($mockedPdo);

        $this->settings->shouldReceive('offsetGet')->with('TABLES')
            ->andReturn(['LM_OFFICES' => 'featured_offices']);

        $this->format->shouldReceive('slugify')->with('Toronto Branch')->andReturn('toronto-branch');
        $this->format->shouldReceive('slugify')->with('Toronto Backup Branch')->andReturn('toronto-backup-branch');

        // Build Request
        $officeRequest = (new OfficeRequest())->withName('Toron')->withOrder([['title', 'ASC']])->withLimit(self::LIMIT);

        // Conduct Search
        $datastore = new SearchDatastore($this->dbFactory, $this->settings, $this->officeFactory);
        $result = $datastore->getOffices($officeRequest);
        $this->assertInternalType('array', $result);

        // Check Results
        $this->assertEquals(2, count($result));
        $resultOne = $results = $result[0];
        $this->assertInstanceOf(OfficeResult::class, $resultOne);
        $this->assertEquals('Toronto Branch', $resultOne->getTitle());
        $this->assertEquals('/office/toronto-branch/', $resultOne->getLink());
        $this->assertNull($resultOne->getAddress());

        $resultTwo = $result[1];
        $this->assertInstanceOf(OfficeResult::class, $resultTwo);
        $this->assertEquals('Toronto Backup Branch', $resultTwo->getTitle());
        $this->assertEquals('/office/toronto-backup-branch/', $resultTwo->getLink());
        $this->assertNull($resultTwo->getAddress());
    }

    /**
     * @covers REW\Datastore\Office\SearchDatastore::getOfficeCount
     * @return void
     */
    public function testGetOfficeCount()
    {
        $mockedStmt = m::mock(\PDOStatement::class);
        $mockedStmt->shouldReceive('execute')
            ->with(['Toron'])
            ->andReturn(true);
        $mockedStmt->shouldReceive('fetchColumn')
            ->andReturn(2);
        $mockedPdo = m::mock(\PDO::class);
        $mockedPdo->shouldReceive('prepare')
            ->with('SELECT COUNT(`id`) FROM `offices` `o` WHERE `o`.`title` LIKE CONCAT("%", ?, "%");')
            ->andReturn($mockedStmt);
        $this->dbFactory->shouldReceive('get')
            ->andReturn($mockedPdo);

        $this->settings->shouldReceive('offsetGet')->with('TABLES')
            ->andReturn(['LM_OFFICES' => 'offices']);

        // Build Request
        $officeRequest = (new OfficeRequest())->withName('Toron');

        // Conduct Search
        $datastore = new SearchDatastore($this->dbFactory, $this->settings, $this->officeFactory);
        $result = $datastore->getOfficeCount($officeRequest);
        $this->assertInternalType('int', $result);
        $this->assertEquals(2, $result);
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        m::close();
    }

    protected function getDemoData()
    {
        return [[
            'id' => 4,
            'title' => 'Toronto Branch'
        ], [
            'id' => 7,
            'title' => 'Toronto Backup Branch'
        ]];
    }
}
