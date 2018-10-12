<?php

namespace REW\Test\IDX\Panel\Type;

use ReflectionProperty;
use IDX_Panel_Type_Radiolist;
use Mockery;

class RadiolistPanelTest extends \Codeception\Test\Unit
{
    /**
     * @covers IDX_Panel_Type_Radiolist::getTags()
     */
    public function testGetTags()
    {
        $value = 'value';
        $mockInputName = 'mockInput';
        $expectedValue = $value;
        $expectedField = [$mockInputName => $expectedValue];

        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Type_Radiolist::class)->makePartial();

        // Mock getValues and set inputName
        $panel->shouldReceive('getValue')->andReturn($value);
        $inputName = new ReflectionProperty($panel, 'inputName');
        $inputName->setAccessible(true);
        $inputName->setValue($panel, $mockInputName);

        // Test getTags with valid values returned
        $tag = $panel->getTags();
        $this->assertInstanceOf(
            'IDX_Search_Tag',
            $tag,
            'getTags did not return an instance of IDX_Search_Tag'
        );
        $this->assertEquals(
            $expectedValue,
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
     * @covers IDX_Panel_Type_Radiolist::getTags()
     */
    public function testGetTagsNoValues()
    {
        $value = [];

        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Type_Radiolist::class)->makePartial();

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
