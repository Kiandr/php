<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel_Foreclosure;
use Mockery;

class ForeclosurePanelTest extends \Codeception\Test\Unit
{
    /**
     * @var string
     */
    protected $expectedTitle = 'Foreclosures';

    /**
     * @var string[]
     */
    protected $expectedInputs = ['search_foreclosure'];

    /**
     * @var string
     */
    protected $expectedField = 'IsForeclosure';

    /**
     * @var string[]
     */
    protected $expectedOptions = [
        ['value' => '', 'title' => 'No Preference'],
        ['value' => 'Y', 'title' => 'Search Foreclosures'],
        ['value' => 'N', 'title' => 'Exclude Foreclosures']
    ];

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_Foreclosure::__construct()
     */
    public function testDefaultProperties()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Foreclosure::class)->makePartial();

        // Test that properties are set correctly
        $this->assertEquals(
            $this->expectedTitle,
            $panel->getTitle(),
            'Title does not match expected title set by class'
        );
        $this->assertEquals(
            $this->expectedInputs,
            $panel->getInputs(),
            'Min and Max Input do not match inputs set by class'
        );
        $this->assertEquals(
            $this->expectedField,
            $panel->getField(),
            'Field does not match expected field set by class'
        );
        $this->assertEquals(
            $this->expectedOptions,
            $panel->getOptions(),
            'Options do not match options set by Class'
        );
    }
}
