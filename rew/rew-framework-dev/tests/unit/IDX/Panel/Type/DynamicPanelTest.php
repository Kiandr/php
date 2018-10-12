<?php

namespace REW\Test\IDX\Panel\Type;

use IDX;
use Locale;
use ReflectionProperty;
use IDX_Panel;
use IDX_Panel_Type_Dynamic;
use Mockery;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\DatabaseInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\Hook\CollectionInterface;
use REW\Core\Interfaces\HooksInterface;
use REW\Core\Interfaces\SettingsInterface;

class DynamicPanelTest extends \Codeception\Test\Unit
{
    /**
     * @var string
     */
    const IDX_FEED = 'mfr';

    /**
     * @var Mockery\MockInterface|SettingsInterface
     */
    protected $settings;

    /**
     * @var Mockery\MockInterface|ContainerInterface
     */
    protected $container;

    /**
     * @var Mockery\MockInterface|HooksInterface
     */
    protected $hooks;

    /**
     * @var Mockery\MockInterface|CollectionInterface
     */
    protected $collection;

    /**
     * @var Mockery\MockInterface|IDXFactoryInterface
     */
    protected $idxfactory;

    /**
     * @var Mockery\MockInterface|IDX
     */
    protected $idx;

    /**
     * @var Mockery\MockInterface|DatabaseInterface
     */
    protected $db;

    /**
     * @var Mockery\MockInterface|Locale
     */
    protected $locale;

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_Type_Dynamic::__construct()
     * @return \PHPUnit_Framework_MockObject_MockObject|IDX_Panel_Type_Dynamic
     */
    public function testConstruct()
    {
        $options = ['fieldType' => 'Select'];

        // Mockery for parent constructor
        $this->settings = Mockery::mock(SettingsInterface::class);
        $this->container = Mockery::mock('alias:Container');
        $this->collection = Mockery::mock(CollectionInterface::class);
        $this->idxfactory = Mockery::mock(IDXFactoryInterface::class);
        $this->db = Mockery::mock(DBInterface::class);
        $this->idx = Mockery::mock(IDX::class);
        // Mock Collection and Hooks objects
        $this->collection = Mockery::mock(CollectionInterface::class);
        $this->hooks = Mockery::mock(HooksInterface::class)->makePartial();
        $this->locale = Mockery::mock('alias:Locale')
            ->shouldReceive('spell')->andReturn('string')->getMock();

        // Make sure hooks run
        $this->hooks->shouldReceive('hook')->with(HooksInterface::HOOK_IDX_PANEL_SETTINGS)
            ->andReturn($this->collection);
        $this->collection->shouldReceive('run');
        $this->hooks->shouldReceive('hook')->with(HooksInterface::HOOK_IDX_PANEL_CONSTRUCT)
            ->andReturn($this->collection);
        $this->collection->shouldReceive('run')->with(IDX_Panel::class);
        $this->container->shouldReceive('get')->with(HooksInterface::class)->andReturn($this->hooks);
        // Mock methods
        $this->container->shouldReceive('getInstance')->andReturn($this->container);
        $this->settings->IDX_FEED = self::IDX_FEED;
        $this->container->shouldReceive('get')->with(SettingsInterface::class)->andReturn($this->settings);
        $this->container->shouldReceive('get')->with(IDXFactoryInterface::class)->andReturn($this->idxfactory);

        // Mock Panel class
        $panel = $this->getMockBuilder('IDX_Panel_Type_Dynamic')
            ->setConstructorArgs([$options])
            ->getMockForAbstractClass();

        $this->assertEquals(
            $options['fieldType'],
            $panel->getFieldType(),
            'FieldType does not match fieldType set by constructor'
        );

        return $panel;
    }

    /**
     * @covers IDX_Panel_Type_Dynamic::setFieldType()
     * @covers IDX_Panel_Type_Dynamic::getFieldType()
     * @depends testConstruct
     * @param $panel \PHPUnit_Framework_MockObject_MockObject|IDX_Panel_Type_Dynamic
     */
    public function testGetSetFieldType($panel)
    {
        // Test get after set
        foreach (['Checklist', 'Select'] as $fieldType) {
            $panel->setFieldType($fieldType);
            $this->assertEquals(
                $fieldType,
                $panel->getFieldType(),
                'Get FieldType does not match set FieldType value'
            );
        }
    }
    /**
     * @covers IDX_Panel_Type_Dynamic::getFieldOptions()
     * @covers IDX_Panel_Type_Dynamic::setFieldOptions()
     * @depends testConstruct
     * @param $panel \PHPUnit_Framework_MockObject_MockObject|IDX_Panel_Type_Dynamic
     */
    public function testGetSetFieldOptions($panel)
    {
        // Test get after set
        foreach (['Checklist', 'Select'] as $fieldOptions) {
            $panel->setFieldOptions($fieldOptions);
            $this->assertEquals(
                $fieldOptions,
                $panel->getFieldOptions(),
                'Get FieldOptions does not match set FieldOptions value'
            );
        }
    }

    /**
     * @covers IDX_Panel_Type_Dynamic::getTags()
     * @dataProvider tagsProvider
     * @param $value string[]
     * @param $input string
     * @param $expectedField array
     */
    public function testGetTags($value, $input, $expectedField)
    {
        // Mock Panel class
        $panel = $this->getMockBuilder('IDX_Panel_Type_Dynamic')
            ->setMethods(['getValues'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        // Mock getValues and set inputName
        $panel->expects($this->once())->method('getValues')
            ->will($this->onConsecutiveCalls($value));
        $inputName = new ReflectionProperty($panel, 'inputName');
        $inputName->setAccessible(true);
        $inputName->setValue($panel, $input);

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
                'Title of Value we set did not match the IDX_Search_tag Title returned'
            );
            $this->assertEquals(
                $expectedField[$key],
                $tag->getField(),
                ' did not match the IDX_Search_Tag Field returned'
            );
        }
    }

    /**
     * @covers IDX_Panel_Type_Dynamic::getTags()
     */
    public function testGetTagsNoValues()
    {
        $value = [];

        // Mock Panel class
        $panel = $this->getMockBuilder('IDX_Panel_Type_Dynamic')
            ->setMethods(['getValues'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        // Mock getValues and set inputName
        $panel->expects($this->once())->method('getValues')
            ->will($this->onConsecutiveCalls($value));

        // Test getTags with no values returned
        $tag = $panel->getTags();
        $this->assertEmpty(
            $tag,
            'getTags did not return an empty array when no values'
        );
    }

    /**
     * Provider for different values returned
     * @return array
     */
    public function tagsProvider()
    {
        return [
            '1 Value' => [
                ['value'],
                'mockInput',
                [['mockInput' => 'value']]
            ],
            '2 Values' => [
                ['value', 'value2'],
                'mockInput',
                [['mockInput' => 'value'], ['mockInput' => 'value2']]
            ]
        ];
    }
}
