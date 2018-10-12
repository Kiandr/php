<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel_Agent;
use Mockery;

class AgentPanelTest extends \Codeception\Test\Unit
{
    /**
     * @var string
     */
    protected $expectedTitle = 'Search by Agent';
    /**
     * @var string[]
     */
    protected $expectedInputs = ['search_agent'];
    /**
     * @var string
     */
    protected $expectedInputClass = 'x12 autocomplete';
    /**
     * @var string
     */
    protected $expectedField = 'ListingAgent';

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_Agent::__construct()
     */
    public function testDefaultProperties()
    {
        $panel = Mockery::mock(IDX_Panel_Agent::class)->makePartial();

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
