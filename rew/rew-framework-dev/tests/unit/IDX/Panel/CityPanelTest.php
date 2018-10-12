<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel_City;
use IDX;
use Locale;
use IDX_Panel;
use Mockery;
use ReflectionProperty;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\DatabaseInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\Hook\CollectionInterface;
use REW\Core\Interfaces\HooksInterface;
use REW\Core\Interfaces\SettingsInterface;

class CityPanelTest extends \Codeception\Test\Unit
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
    protected $expectedTitle = 'City';

    /**
     * @var string[]
     */
    protected $expectedInputs = ['search_city'];

    /**
     * @var string
     */
    protected $expectedInputClass = 'location';

    /**
     * @var string
     */
    protected $expectedField = 'AddressCity';

    /**
     * @var string
     */
    protected $expectedFieldType = 'Checklist';

    /**
     * @var string
     */
    protected $expectedPanelClass = 'scrollable';

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_City::__construct()
     */
    public function testConstruct()
    {
        // City Panel properties
        $options = ['fieldType' => 'NotCheckList'];
        $expectedInputClasses = $this->expectedInputClass . ' x12';

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
        $panel = new IDX_Panel_City($options);

        // Test that properties are set correctly
        $this->assertEquals(
            $expectedInputClasses,
            $panel->getInputClass(),
            'Input Class does not match class set by constructor when not Checklist fieldType'
        );
    }

    /**
     * @covers IDX_Panel_City::__construct()
     */
    public function testDefaultProperties()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_City::class)->makePartial();

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
            $this->expectedInputClass,
            $panel->getInputClass(),
            'InputClass does not match inputClass set by Class'
        );
        $this->assertEquals(
            $this->expectedField,
            $panel->getField(),
            'Field does not match field set by Class'
        );
        $this->assertEquals(
            $this->expectedFieldType,
            $panel->getFieldType(),
            'Field Type does not match fieldType set by Class'
        );
        $this->assertEquals(
            $this->expectedPanelClass,
            $panel->getPanelClass(),
            'Panel Class does not match panelClass set by Class'
        );
    }

    /**
     * @covers IDX_Panel_City::getOptions()
     * @dataProvider getOptionsProvider
     * @param $cityList array
     * @param $setOptions array
     * @param $expectedOptions array
     * @param $fieldType string
     */
    public function testGetOptions($cityList, $setOptions, $expectedOptions, $fieldType)
    {
        global $_CLIENT;
        $_CLIENT['city_list'] = $cityList;

        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_City::class)->makePartial();

        $optionsProp = new ReflectionProperty($panel, 'options');
        $optionsProp->setAccessible(true);
        $optionsProp->setValue($panel, $setOptions);
        $fieldTypeProp = new ReflectionProperty($panel, 'fieldType');
        $fieldTypeProp->setAccessible(true);
        $fieldTypeProp->setValue($panel, $fieldType);

        $options = $panel->getOptions();
        $this->assertEquals(
            $expectedOptions,
            $options,
            'Expected Options do not match getOptions'
        );
    }

    /**
     * Provide getOptions configuration/sample values and expected results
     * @return array
     */
    public function getOptionsProvider()
    {
        return [
            'city_list and not select' => [
                [['value' => 'my city', 'title' => 'My City']],
                [],
                [['value' => 'my city', 'title' => 'My City']],
                'Checklist'
            ],
            'city_list and select' => [
                [['value' => 'my city', 'title' => 'My City']],
                [],
                [
                    ['value' => '', 'title' => 'Select a City'],
                    ['value' => 'my city', 'title' => 'My City']
                ],
                'Select'
            ],
            'no city_list and not select' => [
                [],
                [['value' => 'my city', 'title' => 'My City']],
                [['value' => 'my city', 'title' => 'My City']],
                'Checklist'
            ],
            'no city_list and select' => [
                [],
                [['value' => 'my city', 'title' => 'My City']],
                [
                    ['value' => '', 'title' => 'Select a City'],
                    ['value' => 'my city', 'title' => 'My City']
                ],
                'Select'
            ],
            'no options and select' => [
                [],
                [],
                [],
                'Select'
            ]
        ];
    }
}
