<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel_Sqft;
use Mockery;

class SqftPanelTest extends \Codeception\Test\Unit
{
    /**
     * @var string
     */
    protected $expectedTitle = 'Property Size';

    /**
     * @var string
     */
    protected $expectedField = 'NumberOfSqFt';

    /**
     * @var string[]
     */
    protected $expectedInputs = ['minimum_sqft', 'maximum_sqft'];

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_Sqft::__construct()
     */
    public function testDefaultProperties()
    {
        $expectedOptions = $this->getExpectedOptions('Sqft');

        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Sqft::class)->makePartial();

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
            $expectedOptions,
            $panel->getOptions(),
            'Options do not match options set by Class'
        );
    }

    /**
     * Return Options File based on Type
     * @return array
     */
    public function getExpectedOptions($type)
    {
        $file = __DIR__ . '/Fixtures/' . $type . 'Options.json';
        $content = file_get_contents($file);
        return json_decode($content, true);
    }
}
