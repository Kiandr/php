<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel_Dow;
use Mockery;

class DowPanelTest extends \Codeception\Test\Unit
{
    /**
     * @var string
     */
    protected $expectedTitle = 'Days on Website';

    /**
     * @var string[]
     */
    protected $expectedInputs = ['maximum_dow'];

    /**
     * @var string
     */
    protected $expectedField = 'ListingDOW';

    /**
     * @var string
     */
    protected $expectedFieldType = 'Radiolist';

    /**
     * @var array
     */
    protected $expectedOptions = [
        ['value' => 0, 'title' => 'All Listings'],
        ['value' => 1, 'title' => 'New Listings (1 Day)'],
        ['value' => 7, 'title' => 'This Week (7 Days)'],
        ['value' => 31, 'title' => 'This Month (31 Days)']
    ];

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_Dow::__construct()
     */
    public function testDefaultProperties()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Dow::class)->makePartial();

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
            $this->expectedFieldType,
            $panel->getFieldType(),
            'Field does not match expected field set by class'
        );
        $this->assertEquals(
            $this->expectedOptions,
            $panel->getOptions(),
            'Options do not match options set by Class'
        );
    }
}
