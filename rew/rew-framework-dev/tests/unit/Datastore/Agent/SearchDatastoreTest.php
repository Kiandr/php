<?php
namespace REW\Test\Datastore\Agent;

use REW\Core\Interfaces\Factories\DBFactoryInterface;
use REW\Core\Interfaces\FormatInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Datastore\Agent\SearchDatastore;
use REW\Model\Agent\Search\AgentRequest;
use REW\Model\Agent\Search\AgentResult;
use REW\Factory\Agent\AgentFactory;
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
     * @var AgentFactory
     */
    protected $agentFactory;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->dbFactory = m::mock(DBFactoryInterface::class);
        $this->settings = m::mock(SettingsInterface::class);
        $this->format = m::mock(FormatInterface::class);
        $this->agentFactory = new AgentFactory($this->format);
    }

    /**
     * @covers REW\Datastore\Agent\SearchDatastore::getAgents
     * @return void
     */
    public function testGetAgents()
    {

        $mockedStmt = m::mock(\PDOStatement::class);
        $mockedStmt->shouldReceive('execute')
            ->with(['ake'])
            ->andReturn(true);
        $mockedStmt->shouldReceive('fetchAll')
            ->andReturn($this->getDemoData());
        $mockedPdo = m::mock(\PDO::class);
        $mockedPdo->shouldReceive('prepare')
            ->with('SELECT `a`.`id`, `a`.`first_name`, `a`.`last_name`, `a`.`email`, `a`.`title`, `a`.`office`, `a`.`office_phone`, `a`.`home_phone`, `a`.`cell_phone`, `a`.`fax`, `a`.`remarks`, `a`.`display`, `a`.`display_feature`, `a`.`image`, `a`.`agent_id` FROM `agents` `a` WHERE CONCAT(`a`.`first_name`,\' \', `a`.`last_name`) LIKE CONCAT("%", ?, "%") ORDER BY `a`.`last_name` ASC LIMIT 10;')
            ->andReturn($mockedStmt);
        $this->dbFactory->shouldReceive('get')
            ->andReturn($mockedPdo);

        $this->settings->shouldReceive('offsetGet')->with('TABLES')
            ->andReturn(['LM_AGENTS' => 'agents']);

        $this->format->shouldReceive('slugify')->with('Jake Reynolds')->andReturn('jake-reynolds');
        $this->format->shouldReceive('slugify')->with('Nathan Drake')->andReturn('nathan-drake');

        // Build Request
        $agentRequest = (new AgentRequest())->withName('ake')->withOrder([['last_name', 'ASC']])->withLimit(10);

        // Conduct Search
        $datastore = new SearchDatastore($this->dbFactory, $this->settings, $this->agentFactory);
        $result = $datastore->getAgents($agentRequest);
        $this->assertInternalType('array', $result);

        // Check Results
        $this->assertEquals(2, count($result));
        $resultOne = $result[0];
        $this->assertInstanceOf(AgentResult::class, $resultOne);
        $this->assertEquals('Jake', $resultOne->getFirstName());
        $this->assertEquals('/agent/jake-reynolds/', $resultOne->getLink());
        $this->assertNull($resultOne->getCellPhone());
        $this->assertTrue($resultOne->getDisplay());
        $this->assertFalse($resultOne->getDisplayFeature());

        $resultTwo = $result[1];
        $this->assertInstanceOf(AgentResult::class, $resultTwo);
        $this->assertEquals('Nathan', $resultTwo->getFirstName());
        $this->assertEquals('/agent/nathan-drake/', $resultTwo->getLink());
        $this->assertNull($resultTwo->getCellPhone());
        $this->assertFalse($resultTwo->getDisplay());
        $this->assertTrue($resultTwo->getDisplayFeature());
    }

    /**
     * @covers REW\Datastore\Agent\SearchDatastore::getAgentCount
     * @return void
     */
    public function testGetAgentCount()
    {
        $mockedStmt = m::mock(\PDOStatement::class);
        $mockedStmt->shouldReceive('execute')
            ->with(['ake'])
            ->andReturn(true);
        $mockedStmt->shouldReceive('fetchColumn')
            ->andReturn(2);
        $mockedPdo = m::mock(\PDO::class);
        $mockedPdo->shouldReceive('prepare')
            ->with('SELECT COUNT(`a`.`id`) FROM `agents` `a` WHERE CONCAT(`a`.`first_name`,\' \', `a`.`last_name`) LIKE CONCAT("%", ?, "%");')
            ->andReturn($mockedStmt);
        $this->dbFactory->shouldReceive('get')
            ->andReturn($mockedPdo);

        $this->settings->shouldReceive('offsetGet')->with('TABLES')
            ->andReturn(['LM_AGENTS' => 'agents']);

        // Build Request
        $agentRequest = (new AgentRequest())->withName('ake');

        // Conduct Search
        $datastore = new SearchDatastore($this->dbFactory, $this->settings, $this->agentFactory);
        $result = $datastore->getAgentCount($agentRequest);
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
            'first_name' => 'Jake',
            'last_name' => 'Reynolds',
            'email' => 'jake@test.com',
            'display' => 'Y',
            'display_feature' => 'N'
        ], [
            'id' => 7,
            'first_name' => 'Nathan',
            'last_name' => 'Drake',
            'email' => 'nathan@test.com',
            'display' => 'N',
            'display_feature' => 'Y'
        ]];
    }
}
