<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel_Features;
use Mockery;

class FeaturesPanelTest extends \Codeception\Test\Unit
{
    /**
     * @var string
     */
    protected $expectedTitle = 'Property Features';

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_Features::__construct()
     */
    public function testDefaultProperties()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Features::class)->makePartial();

        // Test that properties are set correctly
        $this->assertEquals(
            $this->expectedTitle,
            $panel->getTitle(),
            'Title does not match expected title set by class'
        );
    }

    /**
     * @covers IDX_Panel_Features::getTags()
     * @dataProvider tagsProvider
     * @param $values array
     * @param $expectedTitles string[]
     * @param $expectedFields array
     */
    public function testGetTags($values, $expectedTitles, $expectedFields)
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Features::class)->makePartial();

        // Mock getValues
        $panel->shouldReceive('getValue')->andReturnValues($values);

        // Test getTags with valid values returned
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
                $expectedTitles[$key],
                $tag->getTitle(),
                'Values we set did not match the IDX_Search_tag returned'
            );
            $this->assertEquals(
                $expectedFields[$key],
                $tag->getField(),
                'Value and inputName did not match the IDX_Search_Tag returned'
            );
        }
    }

    /**
     * @covers IDX_Panel_Features::getTags()
     */
    public function testGetTagsNoValues()
    {
        $value = [];

        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Features::class)->makePartial();

        // Mock getValues
        $panel->shouldReceive('getValue')->andReturn($value);

        // Test getTags with no values returned
        $tag = $panel->getTags();
        $this->assertNull(
            $tag,
            'getTags did not return null when no values'
        );
    }

    /**
     * Provides values and expected title and field from tags returned
     * @return array
     */
    public function tagsProvider()
    {
        return [
            'All Y' => [
                [
                    ['search_pool' => 'Y'],
                    ['search_fireplace' => 'Y'],
                    ['search_waterfront' => 'Y']
                ],
                ['Has Pool', 'Has Fireplace'],
                [
                    ['search_pool' => 'Y'],
                    ['search_fireplace' => 'Y']
                ]
            ],
            'Pool Y' => [
                [
                    ['search_pool' => 'Y'],
                    ['search_fireplace' => 'N'],
                    ['search_waterfront' => 'Y']
                ],
                ['Has Pool', 'No Fireplace'],
                [
                    ['search_pool' => 'Y'],
                    ['search_fireplace' => 'N']
                ]
            ],
            'Fireplace Y' => [
                [
                    ['search_pool' => 'N'],
                    ['search_fireplace' => 'Y'],
                    ['search_waterfront' => 'Y']
                ],
                ['No Pool', 'Has Fireplace'],
                [
                    ['search_pool' => 'N'],
                    ['search_fireplace' => 'Y']
                ]
            ],
            'Pool & Fireplace N' => [
                [
                    ['search_pool' => 'N'],
                    ['search_fireplace' => 'N'],
                    ['search_waterfront' => 'Y']
                ],
                ['No Pool', 'No Fireplace'],
                [
                    ['search_pool' => 'N'],
                    ['search_fireplace' => 'N']
                ]
            ]
        ];
    }
}
