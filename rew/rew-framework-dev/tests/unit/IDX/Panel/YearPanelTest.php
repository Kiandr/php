<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel_Year;
use Mockery;

class YearPanelTest extends \Codeception\Test\Unit
{
    /**
     * @var string
     */
    protected $expectedTitle = 'Year Built';

    /**
     * @var string
     */
    protected $expectedField = 'YearBuilt';

    /**
     * @var string[]
     */
    protected $expectedInputs = ['minimum_year', 'maximum_year'];

    /**
     * @var IDX_Panel_Year
     */
    protected $panel;

    /**
     * @var array
     */
    protected $expectedOptions;

    /**
     * @var string
     */
    protected $currentYear;

    /**
     * @return void
     */
    public function _before()
    {
        // Setup expected Options based on Current year
        $this->expectedOptions = $this->getExpectedOptions('Year');
        $this->currentYear = '2018';

        // Setup Mock Panel class
        $this->panel = $this->getMockBuilder('IDX_Panel_Year')
            ->setMethods(['getCurrentYear', 'getValue', 'getOptionTitle'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_Year::__construct()
     */
    public function testDefaultProperties()
    {
        // Test that properties are set correctly
        $this->assertEquals(
            $this->expectedTitle,
            $this->panel->getTitle(),
            'Title does not match expected title set by class'
        );
        $this->assertEquals(
            $this->expectedField,
            $this->panel->getField(),
            'Field does not match expected field set by class'
        );
        $this->assertEquals(
            $this->expectedInputs,
            $this->panel->getInputs(),
            'Min and Max Input do not match inputs set by class'
        );
    }

    /**
     * @covers IDX_Panel_Year::getOptions()
     */
    public function testGetOptions()
    {
        $this->panel->expects($this->any())
            ->method('getCurrentYear')
            ->willReturn($this->currentYear);
        $this->assertEquals($this->expectedOptions, $this->panel->getOptions());
    }

    /**
     * @covers IDX_Panel_Year::getTags()
     * @dataProvider tagsProvider
     * @param $values array
     * @param $expectedTitle string
     * @param $expectedField array
     */
    public function testGetTags($values, $expectedTitle, $expectedField)
    {
        $this->panel->expects($this->any())
            ->method('getValue')
            ->willReturn($values);
        $this->panel->expects($this->any())
            ->method('getOptionTitle')
            ->will($this->onConsecutiveCalls(array_shift($values), array_shift($values)));


        // Test getTags with only max returned
        $tags = $this->panel->getTags();
        $this->assertInternalType(
            'array',
            $tags,
            'getTags did not return an array'
        );
        foreach ($tags as $tag) {
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
    }

    /**
     * @covers IDX_Panel_Year::getTags()
     */
    public function testGetTagsNoValues()
    {
        $this->panel->expects($this->once())
            ->method('getValue')
            ->willReturn([]);

        $this->assertNull($this->panel->getTags());
    }

    /**
     * Provider for different Range values returned
     * @return array
     */
    public function tagsProvider()
    {
        return [
            'Min and Max' => [
                ['minimum_year' => '1940', 'maximum_year' => '2000'],
                'Built between 1940 - 2000',
                ['minimum_year' => '1940', 'maximum_year' => '2000'],
            ],
            'Min Only' => [
                ['minimum_year' => '1950', 'maximum_year' => ''],
                'Built after 1950',
                ['minimum_year' => '1950'],
            ],
            'Max Only' => [
                ['minimum_year' => '', 'maximum_year' => '1980'],
                'Built before 1980',
                ['maximum_year' => '1980'],
            ]
        ];
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
