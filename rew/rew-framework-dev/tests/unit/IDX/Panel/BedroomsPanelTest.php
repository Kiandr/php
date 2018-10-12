<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel_Bedrooms;
use Mockery;

class BedroomsPanelTest extends \Codeception\Test\Unit
{
    /**
     * @var string
     */
    protected $expectedTitle = 'Bedrooms';

    /**
     * @var string
     */
    protected $expectedField = 'NumberOfBedrooms';

    /**
     * @var string[]
     */
    protected $expectedInputs = ['minimum_bedrooms', 'maximum_bedrooms'];

    /**
     * @var array
     */
    protected $expectedOptions = [
        ['value' => 1, 'title' => '1'],
        ['value' => 2, 'title' => '2'],
        ['value' => 3, 'title' => '3'],
        ['value' => 4, 'title' => '4'],
        ['value' => 5, 'title' => '5'],
        ['value' => 6, 'title' => '6'],
        ['value' => 7, 'title' => '7'],
        ['value' => 8, 'title' => '8']
    ];

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_Bedrooms::__construct()
     */
    public function testDefaultProperties()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Bedrooms::class)->makePartial();

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

    /**
     * @covers IDX_Panel_Bedrooms::getTags()
     * @dataProvider tagsProvider
     */
    public function testGetTags($value, $expectedTitle, $expectedField)
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Bedrooms::class)->makePartial();

        // Mock getValues and getOptionTitle
        $panel->shouldReceive('getValue')->andReturn($value);

        // Test getTags with only max returned
        $tag = $panel->getTags();
        $this->assertInternalType(
            'array',
            $tag,
            'getTags did not return an array'
        );
        $tag = $tag[0];
        $this->assertInstanceOf(
            'IDX_Search_Tag',
            $tag,
            'getTags did not return an instance of IDX_Search_Tag'
        );
        $this->assertEquals(
            $expectedTitle,
            $tag->getTitle(),
            'Value we set did not match the IDX_Search_tag Title returned'
        );
        $this->assertEquals(
            $expectedField,
            $tag->getField(),
            'Value and inputName we set did not match the IDX_Search_Tag Field returned'
        );
    }

    /**
     * @covers IDX_Panel_Bedrooms::getTags()
     */
    public function testGetTagsNoValues()
    {
        $value = [];

        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Bedrooms::class)->makePartial();

        // Mock getValues and getOptionTitle
        $panel->shouldReceive('getValue')->andReturn($value);

        // Test getTags with no values returned
        $tag = $panel->getTags();
        $this->assertNull(
            $tag,
            'getTags did not return null when no values'
        );
    }

    /**
     * Provider for different Bedrooms values returned
     * @return array
     */
    public function tagsProvider()
    {
        return [
            'Min and Max' => [
                ['minimum_bedrooms' => 1, 'maximum_bedrooms' => 4],
                '1 - 4 Beds',
                ['minimum_bedrooms' => 1, 'maximum_bedrooms' => 4]
            ],
            'Min Only' => [
                ['minimum_bedrooms' => 1, 'maximum_bedrooms' => null],
                '1+ Beds',
                ['minimum_bedrooms' => '1']
            ],
            'Max Only' => [
                ['minimum_bedrooms' => null, 'maximum_bedrooms' => 4],
                '4 or Less Beds',
                ['maximum_bedrooms' => '4']
            ]
        ];
    }
}
