<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel_Dom;
use Mockery;

class DomPanelTest extends \Codeception\Test\Unit
{
    /**
     * @var string
     */
    protected $expectedTitle = 'Days on Market';

    /**
     * @var string[]
     */
    protected $expectedInputs = ['maximum_dom'];

    /**
     * @var string
     */
    protected $expectedField = 'ListingDOM';

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
     * @covers IDX_Panel_Dom::__construct()
     */
    public function testDefaultProperties()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Dom::class)->makePartial();

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
            'FieldType does not match expected fieldType set by class'
        );
        $this->assertEquals(
            $this->expectedOptions,
            $panel->getOptions(),
            'Options do not match options set by Class'
        );
    }
    /**
     * @covers IDX_Panel_Dom::getTags()
     */
    public function testGetTags()
    {
        $value = 1;
        $expectedTitle = "Less than " . $value . " " . strtolower($this->expectedTitle);
        $expectedField = [$this->expectedInputs[0] => $value];

        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Dom::class)->makePartial();

        // Mock getValues
        $panel->shouldReceive('getValue')->andReturn($value);

        // Test getTags with valid values returned
        $tag = $panel->getTags();
        $this->assertInstanceOf(
            'IDX_Search_Tag',
            $tag,
            'getTags did not return an instance of IDX_Search_Tag'
        );
        $this->assertEquals(
            $expectedTitle,
            $tag->getTitle(),
            'Values we set did not match the IDX_Search_tag returned'
        );
        $this->assertEquals(
            $expectedField,
            $tag->getField(),
            'Value and inputName did not match the IDX_Search_Tag returned'
        );
    }

    /**
     * @covers IDX_Panel_Dom::getTags()
     */
    public function testGetTagsNoValues()
    {
        $value = [];

        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Dom::class)->makePartial();

        // Mock getValues
        $panel->shouldReceive('getValue')->andReturn($value);

        // Test getTags with no values returned
        $tag = $panel->getTags();
        $this->assertNull(
            $tag,
            'getTags did not return null when no values'
        );
    }
}
