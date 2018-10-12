<?php

namespace REW\Test\IDX\Panel\Type;

use IDX;
use Locale;
use ReflectionProperty;
use IDX_Panel;
use IDX_Panel_Type_Input;
use Mockery;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\DatabaseInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\Hook\CollectionInterface;
use REW\Core\Interfaces\HooksInterface;
use REW\Core\Interfaces\SettingsInterface;

class InputPanelTest extends \Codeception\Test\Unit
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
     * @var string
     */
    protected $expectedInputClass = 'x12';

    /**
     * @var string
     */
    protected $expectedPlaceholder = '...';

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_Type_Input::__construct()
     */
    public function testConstruct()
    {
        $options = ['placeholder' => 'my placeholder'];

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
        $panel = new IDX_Panel_Type_Input($options);

        $this->assertEquals(
            $options['placeholder'],
            $panel->getPlaceholder(),
            'Placeholder does not match Placeholder set by constructor'
        );
    }

    /**
     * @covers IDX_Panel_Type_Input::__construct()
     * @return Mockery\MockInterface|IDX_Panel_Type_Input
     */
    public function testDefaultProperties()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Type_Input::class)->makePartial();

        // Test that properties are set correctly
        $this->assertEquals(
            $this->expectedInputClass,
            $panel->getInputClass(),
            'InputClass does not match inputClass set by class'
        );
        $this->assertEquals(
            $this->expectedPlaceholder,
            $panel->getPlaceholder(),
            'Placeholder does not match Placeholder set by class'
        );

        return $panel;
    }

    /**
     * @covers IDX_Panel_Type_Input::getPlaceholder()
     * @covers IDX_Panel_Type_Input::setPlaceholder()
     * @depends testDefaultProperties
     * @param $panel Mockery\MockInterface|IDX_Panel_Type_Input
     */
    public function testGetSetPlaceholder($panel)
    {
        // Test get after set
        foreach (['placeholder1', 'placeholder2'] as $fieldOptions) {
            $panel->setPlaceholder($fieldOptions);
            $this->assertEquals(
                $fieldOptions,
                $panel->getPlaceholder(),
                'Get Placeholder does not match set Placeholder value'
            );
        }
    }

    /**
     * @covers IDX_Panel_Type_Input::getTags()
     * @dataProvider tagsProvider
     * @param $value string[]
     * @param $mockTitle string
     * @param $input string
     * @param $expectedTitle string
     * @param $expectedField array
     */
    public function testGetTags($value, $mockTitle, $input, $expectedTitle, $expectedField)
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Type_Input::class)->makePartial();

        // Mock getValues and set inputName and title
        $panel->shouldReceive('getValue')->andReturn($value);
        $inputName = new ReflectionProperty($panel, 'inputName');
        $inputName->setAccessible(true);
        $inputName->setValue($panel, $input);
        $title = new ReflectionProperty($panel, 'title');
        $title->setAccessible(true);
        $title->setValue($panel, $mockTitle);

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
            'Value we set did not match the IDX_Search_tag returned'
        );
        $this->assertEquals(
            $expectedField,
            $tag->getField(),
            'Value and inputName did not match the IDX_Search_Tag returned'
        );
    }

    /**
     * @covers IDX_Panel_Type_Input::getTags()
     */
    public function testGetTagsNoValues()
    {
        $value = [];

        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Type_Input::class)->makePartial();

        // Mock getValues
        $panel->shouldReceive('getValue')->andReturn($value);

        // Test getTags with no values returned
        $tag = $panel->getTags();
        $this->assertNull($tag, 'No values returned from getValue did not return null');
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
                'Mock Title',
                'mockInput',
                'Mock Title: value',
                ['mockInput' => 'value']
            ],
            '2 Values' => [
                ['value', 'value2'],
                'Mock Title 2',
                'mockInput',
                'Mock Title 2: value, value2',
                ['mockInput' => 'value, value2']
            ]
        ];
    }
}
