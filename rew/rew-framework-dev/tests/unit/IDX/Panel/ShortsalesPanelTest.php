<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel_Shortsales;
use Mockery;

class ShortsalesPanelTest extends \Codeception\Test\Unit
{
    /**
     * @var string
     */
    protected $expectedTitle = 'Short Sales';

    /**
     * @var string
     */
    protected $expectedField = 'IsShortSale';

    /**
     * @var string[]
     */
    protected $expectedInputs = ['search_shortsale'];

    /**
     * @var array
     */
    protected $expectedOptions = [
        ['value' => '', 'title' => 'No Preference'],
        ['value' => 'Y', 'title' => 'Search Short Sales'],
        ['value' => 'N', 'title' => 'Exclude Short Sales']
    ];

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_Shortsales::__construct()
     */
    public function testDefaultProperties()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Shortsales::class)->makePartial();

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
            'Min and Max Input do not match inputs set by class'
        );
        $this->assertEquals(
            $this->expectedOptions,
            $panel->getOptions(),
            'Options do not match options set by Class'
        );
    }
}
