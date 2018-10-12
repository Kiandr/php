<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel_AgentId;
use Mockery;

class AgentIdPanelTest extends \Codeception\Test\Unit
{
    /**
     * @var string
     */
    protected $expectedTitle = 'Search by Agent ID';

    /**
     * @var string[]
     */
    protected $expectedInputs = ['agent_id'];

    /**
     * @var string
     */
    protected $expectedInputClass = 'x12 autocomplete';

    /**
     * @var string
     */
    protected $expectedField = 'ListingAgentID';

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }
    /**
     * @covers IDX_Panel_AgentId::__construct()
     */
    public function testDefaultProperties()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_AgentId::class)->makePartial();

        // Test that properties are set correctly
        $this->assertEquals(
            $this->expectedTitle,
            $panel->getTitle(),
            'Title does not match expected title set by class'
        );
        $this->assertEquals(
            $this->expectedInputs,
            $panel->getInputs(),
            'InputName does not match input name set by class'
        );
        $this->assertEquals(
            $this->expectedInputClass,
            $panel->getInputClass(),
            'InputClass does not match expected inputClass set by class'
        );
        $this->assertEquals(
            $this->expectedField,
            $panel->getField(),
            'Field does not match expected field set by class'
        );
    }
}
