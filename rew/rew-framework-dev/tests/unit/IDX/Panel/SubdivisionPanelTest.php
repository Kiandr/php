<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel_Subdivision;
use Mockery;

class SubdivisionPanelTest extends \Codeception\Test\Unit
{
    /**
     * @var string
     */
    protected $expectedTitle = 'Subdivision';

    /**
     * @var string[]
     */
    protected $expectedInputs = ['search_subdivision'];

    /**
     * @var string
     */
    protected $expectedInputClass = 'x12 autocomplete location';

    /**
     * @var string
     */
    protected $expectedField = 'AddressSubdivision';

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_Subdivision::__construct()
     */
    public function testDefaultProperties()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Subdivision::class)->makePartial();

        // Test that properties are set correctly
        $this->assertEquals(
            $this->expectedTitle,
            $panel->getTitle(),
            'Title does not match expected title set by class'
        );
        $this->assertEquals(
            $this->expectedInputs,
            $panel->getInputs(),
            'Input does not match input set by class'
        );
        $this->assertEquals(
            $this->expectedInputClass,
            $panel->getInputClass(),
            'Input Class does not match input class set by class'
        );
        $this->assertEquals(
            $this->expectedField,
            $panel->getField(),
            'Field does not match expected field set by class'
        );
    }

    /**
     * @covers IDX_Panel_Subdivision::getTags()
     * @dataProvider tagsProvider
     * @param $values string[]
     */
    public function testGetTags($values)
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Subdivision::class)->makePartial();

        // Mock getValues and getOptionTitle
        $panel->shouldReceive('getValues')->andReturn($values);

        // Test getTags with only max returned
        $tags = $panel->getTags();
        $this->assertInternalType(
            'array',
            $tags,
            'getTags did not return an array'
        );
        foreach ($tags as $key => $tag) {
            $this->assertInstanceOf(
                'IDX_Search_Tag',
                $tag,
                'getTags did not return an instance of IDX_Search_Tag'
            );
            $this->assertEquals(
                $values[$key],
                $tag->getTitle(),
                'Value we set did not match the IDX_Search_tag Title returned'
            );
            $this->assertEquals(
                [$this->expectedInputs[0] => $values[$key]],
                $tag->getField(),
                'Value and inputName we set did not match the IDX_Search_Tag Field returned'
            );
        }
    }

    /**
     * Provide values for getTags
     * @return array
     */
    public function tagsProvider()
    {
        return [
            '1 value' => [
                ['Subdivision1'],
            ],
            '2 values' => [
                ['Subdivision1', 'Subdivision2'],
            ],
            'No values' => [[]]
        ];
    }
}
