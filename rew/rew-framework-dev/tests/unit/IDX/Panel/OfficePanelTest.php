<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel_Office;
use Mockery;

class OfficePanelTest extends \Codeception\Test\Unit
{
    /**
     * @var string
     */
    protected $expectedTitle = 'Search by Office';
    /**
     * @var string[]
     */
    protected $expectedInputs = ['search_office'];
    /**
     * @var string
     */
    protected $expectedInputClass = 'x12 autocomplete';
    /**
     * @var string
     */
    protected $expectedField = 'ListingOffice';

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_Office::__construct()
     */
    public function testDefaultProperties()
    {
        $panel = Mockery::mock(IDX_Panel_Office::class)->makePartial();

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
