<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel_HasOpenHouse;
use Mockery;

class HasOpenHousePanelTest extends \Codeception\Test\Unit
{
    /**
     * @var string
     */
    protected $expectedTitle = 'Has Open House';

    /**
     * @var string[]
     */
    protected $expectedInputs = ['search_has_openhouse'];

    /**
     * @var string
     */
    protected $expectedField = 'HasOpenHouse';

    /**
     * @var string[]
     */
    protected $expectedOptions = [
        ['value' => '', 'title' => 'No Preference'],
        ['value' => 'Y', 'title' => 'Search Open Houses'],
        ['value' => 'N', 'title' => 'Exclude Open Houses']
    ];

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_HasOpenHouse::__construct()
     */
    public function testDefaultProperties()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_HasOpenHouse::class)->makePartial();

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
