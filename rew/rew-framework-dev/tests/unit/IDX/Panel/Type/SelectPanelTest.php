<?php

namespace REW\Test\IDX\Panel\Type;

use IDX;
use Locale;
use ReflectionProperty;
use IDX_Panel;
use IDX_Panel_Type_Select;
use Mockery;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\DatabaseInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\Hook\CollectionInterface;
use REW\Core\Interfaces\HooksInterface;
use REW\Core\Interfaces\SettingsInterface;

class SelectPanelTest extends \Codeception\Test\Unit
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
    protected $expectedPlaceholder = 'No Preference';

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_Type_Select::__construct()
     */
    public function testConstruct()
    {
        $options = [
            'multiple' => true,
            'size' => 2,
            'placeholder' => 'my placeholder',
        ];

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
        $panel = new IDX_Panel_Type_Select($options);

        $this->assertEquals(
            $options['placeholder'],
            $panel->getPlaceholder(),
            'Placeholder does not match Placeholder set by constructor'
        );
        $this->assertEquals(
            $options['multiple'],
            $panel->isMultiple(),
            'Multiple does not match Multiple set by constructor'
        );
        $this->assertEquals(
            $options['size'],
            $panel->getSize(),
            'Size does not match Size set by constructor'
        );
    }

    /**
     * @covers IDX_Panel_Type_Select::__construct()
     * @return Mockery\MockInterface|IDX_Panel_Type_Select
     */
    public function testDefaultProperties()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Type_Select::class)->makePartial();

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
     * @covers IDX_Panel_Type_Select::getPlaceholder()
     * @covers IDX_Panel_Type_Select::setPlaceholder()
     * @depends testDefaultProperties
     * @param $panel Mockery\MockInterface|IDX_Panel_Type_Select
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
     * @covers IDX_Panel_Type_Select::getTags()
     * @dataProvider tagsProvider
     * @param $value array
     * @param $title string[]
     * @param $input string
     * @param $expectedField array
     */
    public function testGetTags($value, $title, $input, $expectedField)
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Type_Select::class)->makePartial()
            ->shouldAllowMockingProtectedMethods();

        // Mock getValues and getOptionTitle
        $panel->shouldReceive('getValue')->andReturn($value);
        $panel->shouldReceive('getOptionTitle')->andReturnValues($title);
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
                $title[$key],
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
     * @covers IDX_Panel_Type_Select::getOptions()
     * @dataProvider optionsProvider
     * @depends testDefaultProperties
     * @param $multiple bool
     * @param $placeholder string
     * @param $setOptions array
     * @param $expectedOptions array
     * @param $panel Mockery\MockInterface|IDX_Panel_Type_Select
     */
    public function testGetOptions($multiple, $placeholder, $setOptions, $expectedOptions, $panel)
    {
        // Set panel properties used by getOption method
        $optionsProp = new ReflectionProperty($panel, 'options');
        $optionsProp->setAccessible(true);
        $optionsProp->setValue($panel, $setOptions);
        $multipleProp = new ReflectionProperty($panel, 'multiple');
        $multipleProp->setAccessible(true);
        $multipleProp->setValue($panel, $multiple);
        $panel->setPlaceholder($placeholder);

        // Get options should return options we set plus empty value when multiple not set
        $this->assertEquals(
            $expectedOptions,
            $panel->getOptions(),
            'Expected Options do not match based on multiple set'
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
                ['Value'],
                'mockInput',
                [['mockInput' => 'value']]
            ],
            '2 Values' => [
                ['value', 'value2'],
                ['Value', 'Value2'],
                'mockInput',
                [['mockInput' => 'value'], ['mockInput' => 'value2']]
            ],
            'No Values' => [
                [],
                [],
                'mockInput',
                []
            ]
        ];
    }

    /**
     * Provider for getOptions
     * @return array
     */
    public function optionsProvider()
    {
        return [
            'false multiple' => [
                false,
                'No Preference',
                [
                    ['value' => 'value1', 'title' => 'value1'],
                    ['value' => 'value2', 'title' => 'value2'],
                ],
                [
                    ['value' => '', 'title' => 'No Preference'],
                    ['value' => 'value1', 'title' => 'value1'],
                    ['value' => 'value2', 'title' => 'value2'],
                ],
            ],
            'true multiple' => [
                true,
                'No Preference',
                [
                    ['value' => 'value1', 'title' => 'value1'],
                    ['value' => 'value2', 'title' => 'value2'],
                ],
                [
                    ['value' => 'value1', 'title' => 'value1'],
                    ['value' => 'value2', 'title' => 'value2'],
                ],
            ],
        ];
    }
}
