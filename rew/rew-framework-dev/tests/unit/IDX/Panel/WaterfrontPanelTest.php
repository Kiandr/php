<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel_Waterfront;
use Mockery;

class WaterfrontPanelTest extends \Codeception\Test\Unit
{
    /**
     * @var string
     */
    protected $expectedTitle = 'Waterfront';

    /**
     * @var string
     */
    protected $expectedField = 'IsWaterfront';

    /**
     * @var string[]
     */
    protected $expectedInputs = ['search_waterfront'];

    /**
     * @var array
     */
    protected $expectedOptions = [
        ['value' => '', 'title' => 'No Preference'],
        ['value' => 'Y', 'title' => 'Search Waterfront'],
        ['value' => 'N', 'title' => 'Exclude Waterfront']
    ];

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_Waterfront::__construct()
     */
    public function testDefaultProperties()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Waterfront::class)->makePartial();

        // Test that properties are set correctly
        $this->assertEquals(
            $this->expectedTitle,
            $panel->getTitle(),
            'Title does not match expected title set by class'
        );
        $this->assertEquals(
            $this->expectedField,
            $panel->getField(),
            'Field does not match expected field set by class'
        );
        $this->assertEquals(
            $this->expectedInputs,
            $panel->getInputs(),
            'Input does not match input set by class'
        );
        $this->assertEquals(
            $this->expectedOptions,
            $panel->getOptions(),
            'Options do not match options set by Class'
        );
    }
}
