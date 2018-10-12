<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel_Address;
use Mockery;

class AddressPanelTest extends \Codeception\Test\Unit
{
    /**
     * @var string
     */
    protected $expectedTitle = 'Address';

    /**
     * @var string[]
     */
    protected $expectedInputs = ['search_address'];

    /**
     * @var string
     */
    protected $expectedInputClass = 'x12 location';

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_Address::__construct()
     */
    public function testDefaultProperties()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Address::class)->makePartial();

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
            'Options do not match options set by Class'
        );
    }
}
