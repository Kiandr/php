<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel_SchoolDistrict;
use Mockery;

class SchoolDistrictPanelTest extends \Codeception\Test\Unit
{
    /**
     * @var string
     */
    protected $expectedTitle = 'School District';
    /**
     * @var string[]
     */
    protected $expectedInputs = ['school_district'];
    /**
     * @var string
     */
    protected $expectedInputClass = 'x12 autocomplete single location';
    /**
     * @var string
     */
    protected $expectedField = 'SchoolDistrict';

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_SchoolDistrict::__construct()
     */
    public function testDefaultProperties()
    {
        $panel = Mockery::mock(IDX_Panel_SchoolDistrict::class)->makePartial();

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
