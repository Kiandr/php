<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel_Acres;
use Mockery;

class AcresPanelTest extends \Codeception\Test\Unit
{
    /**
     * @var string
     */
    protected $expectedTitle = 'Lot Size';

    /**
     * @var string
     */
    protected $expectedField = 'NumberOfAcres';

    /**
     * @var string[]
     */
    protected $expectedInputs = ['minimum_acres', 'maximum_acres'];

    /**
     * @var string[]
     */
    protected $expectedOptions = [
        ['value' => 0.25, 'title' => '1/4 Acre'],
        ['value' => 0.50, 'title' => '1/2 Acre'],
        ['value' => 1.00, 'title' => '1 Acre'],
        ['value' => 1.50, 'title' => '1 1/2 Acre'],
        ['value' => 2.00, 'title' => '2 Acres'],
        ['value' => 2.50, 'title' => '2 1/2 Acres'],
        ['value' => 3.00, 'title' => '3 Acres'],
        ['value' => 5.00, 'title' => '5 Acres'],
        ['value' => 10.00, 'title' => '10 Acres'],
        ['value' => 15.00, 'title' => '15 Acres'],
        ['value' => 20.00, 'title' => '20 Acres'],
        ['value' => 25.00, 'title' => '25 Acres'],
        ['value' => 30.00, 'title' => '30 Acres'],
        ['value' => 40.00, 'title' => '40 Acres'],
        ['value' => 50.00, 'title' => '50 Acres'],
        ['value' => 100.00, 'title' => '100 Acres']
    ];

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_Acres::__construct()
     */
    public function testDefaultProperties()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Acres::class)->makePartial();

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
