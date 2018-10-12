<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel_DriveTime;
use Mockery;

class DriveTimePanelTest extends \Codeception\Test\Unit
{
    /**
     * @var string
     */
    protected $expectedTitle = 'Drive Time';

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_DriveTime::__construct()
     */
    public function testDefaultProperties()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_DriveTime::class)->makePartial();

        // Test that properties are set correctly
        $this->assertEquals(
            $this->expectedTitle,
            $panel->getTitle(),
            'Title does not match expected title set by class'
        );
    }

    /**
     * @covers IDX_Panel_DriveTime::setPanelClass
     */
    public function testSetPanelClass()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_DriveTime::class)->makePartial();

        $panel->setPanelClass('test tester');

        // Test that properties are set correctly
        $this->assertEquals(
            'test tester -width-1/1',
            $panel->getPanelClass(),
            'Title does not match expected title set by class'
        );
    }

    /**
     * @covers IDX_Panel_DriveTime::getMarkup
     */
    public function testGetMarkup()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_DriveTime::class)->makePartial();

        // Test that properties are set correctly
        $this->assertNotEmpty($panel->getMarkup());
    }
}
