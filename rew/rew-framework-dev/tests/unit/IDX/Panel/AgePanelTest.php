<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel_Age;
use ReflectionProperty;
use Mockery;

class AgePanelTest extends \Codeception\Test\Unit
{
    /**
     * @var string
     */
    protected $expectedTitle = 'Days on Website';
    /**
     * @var string[]
     */
    protected $expectedInputs = ['search_new'];
    /**
     * @var string
     */
    protected $expectedField = 'timestamp_created';
    /**
     * @var string
     */
    protected $expectedFieldType = 'Radiolist';

    /**
     * @var array
     */
    protected $expectedOptions = [
        ['value' => 0, 'title' => 'All Listings'],
        ['value' => '-1 DAY', 'title' => 'New Listings (1 Day)'],
        ['value' => '-7 DAY', 'title' => 'This Week (7 Days)'],
        ['value' => '-31 DAY', 'title' => 'This Month (31 Days)'],
    ];

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_Age::__construct()
     */
    public function testDefaultProperties()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Age::class)->makePartial();

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
            $this->expectedField,
            $panel->getField(),
            'Field does not match expected field set by class'
        );
        $this->assertEquals(
            $this->expectedFieldType,
            $panel->getFieldType(),
            'FieldType does not match expected fieldType set by class'
        );
    }

    /**
     * @covers IDX_Panel_Age::getOptions()
     */
    public function testGetOptions()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Age::class)->makePartial();

        // Test GetOptions that uses GetLabelForValue
        $this->assertEquals(
            $this->expectedOptions,
            $panel->getOptions(),
            'Get Options does not return expected options set by class'
        );
    }

    /**
     * @covers IDX_Panel_Age::getTags()
     * @dataProvider tagsProvider
     * @param $value string[]
     * @param $expectedTitle string[]
     * @param $input string
     * @param $expectedField array
     */
    public function testGetTags($value, $expectedTitle, $input, $expectedField)
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Age::class)->makePartial();

        // Mock getValues and set inputName
        $panel->shouldReceive('getValues')->andReturnValues([$value, []]);
        $inputName = new ReflectionProperty($panel, 'inputName');
        $inputName->setAccessible(true);
        $inputName->setValue($panel, $input);

        // Test getTags with valid values returned
        $tags = $panel->getTags();
        $this->assertInternalType(
            'array',
            $tags,
            'getTags did not return an instance of IDX_Search_Tag'
        );
        foreach ($tags as $key => $tag) {
            $this->assertInstanceOf(
                'IDX_Search_Tag',
                $tag,
                'getTags did not return an instance of IDX_Search_Tag'
            );
            $this->assertEquals(
                $expectedTitle[$key],
                $tag->getTitle(),
                'Title from Value we set did not match the IDX_Search_tag returned'
            );
            $this->assertEquals(
                $expectedField[$key],
                $tag->getField(),
                'Value and inputName did not match the IDX_Search_Tag returned'
            );
        }
    }

    /**
     * @covers IDX_Panel_Age::getTags()
     */
    public function testGetTagsNoValues()
    {
        $value = [];

        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Age::class)->makePartial();

        // Mock getValues
        $panel->shouldReceive('getValue')->andReturn($value);

        // Test get Tags with no values returned
        $tags = $panel->getTags();
        $this->assertEquals(
            [],
            $tags,
            'No values returned from getValue did not return empty array'
        );
    }

    /**
     * Provider for different Age values returned
     * @return array
     */
    public function tagsProvider()
    {
        return [
            '1 Value' => [
                ['-1 DAY'],
                ['New Listings (1 Day)'],
                'mockInput',
                [['mockInput' => '-1 DAY']]
            ],
            '2 Value' => [
                ['-1 DAY', '-7 DAY'],
                ['New Listings (1 Day)', 'This Week (7 Days)'],
                'mockInput',
                [['mockInput' => '-1 DAY'], ['mockInput' => '-7 DAY']]
            ],
        ];
    }
}
