<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel_Bankowned;
use Mockery;

class BankownedPanelTest extends \Codeception\Test\Unit
{
    /**
     * @var string
     */
    protected $expectedTitle = 'Bank Owned';

    /**
     * @var string
     */
    protected $expectedField = 'IsBankOwned';

    /**
     * @var string[]
     */
    protected $expectedInputs = ['search_bankowned'];

    /**
     * @var array
     */
    protected $expectedOptions = [
        ['value' => '', 'title' => 'No Preference'],
        ['value' => 'Y', 'title' => 'Search Bank Owned'],
        ['value' => 'N', 'title' => 'Exclude Bank Owned']
    ];

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_Bankowned::__construct()
     */
    public function testDefaultProperties()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Bankowned::class)->makePartial();

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
