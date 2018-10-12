<?php

namespace REW\Test\IDX\Panel\Type;

use ReflectionProperty;
use IDX_Panel_Type_Checklist;

class ChecklistPanelTest extends \Codeception\Test\Unit
{
    /**
     * @covers IDX_Panel_Type_Checklist::getTags()
     */
    public function testGetTags()
    {
        $value = ['value', 'value2'];

        // Mock Panel class
        $panel = $this->getMockBuilder('IDX_Panel_Type_Checklist')
            ->setMethods(['getValues'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        // Mock getValues and set inputName
        $panel->expects($this->atMost(2))->method('getValues')
            ->will($this->onConsecutiveCalls($value, []));
        $inputName = new ReflectionProperty($panel, 'inputName');
        $inputName->setAccessible(true);
        $inputName->setValue($panel, 'mockInput');

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
                'getTags did not return an array of IDX_Search_Tag'
            );
            $this->assertEquals(
                $value[$key],
                $tag->getTitle(),
                'Values we set did not match the IDX_Search_tag returned'
            );
            $this->assertEquals(
                [$panel->getInputs()[0] => $value[$key]],
                $tag->getField(),
                'Value and inputName did not match the IDX_Search_Tag returned'
            );
        }

        // Test get Tags with no values returned
        $tags = $panel->getTags();
        $this->assertEquals(
            [],
            $tags,
            'No values returned from getValues did not return an empty array'
        );
    }
}
