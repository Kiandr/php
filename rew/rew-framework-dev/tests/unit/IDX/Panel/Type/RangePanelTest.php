<?php

namespace REW\Test\IDX\Panel\Type;

use IDX;
use Locale;
use ReflectionProperty;
use IDX_Panel;
use IDX_Panel_Type_Range;
use Mockery;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\DatabaseInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\Hook\CollectionInterface;
use REW\Core\Interfaces\HooksInterface;
use REW\Core\Interfaces\SettingsInterface;

class RangePanelTest extends \Codeception\Test\Unit
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
     * @var bool
     */
    protected $expectedShowMin = true;

    /**
     * @var bool
     */
    protected $expectedShowMax = true;

    /**
     * @var string
     */
    protected $expectedMinOption = 'Min';

    /**
     * @var string
     */
    protected $expectedMaxOption = 'Max';

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_Type_Range::__construct()
     */
    public function testConstruct()
    {
        $options = [
            'minInput' => 'min input',
            'maxInput' => 'max input',
            'minClass' => 'min class',
            'maxClass' => 'max class',
            'showMin' => false,
            'showMax' => false,
            'minOption' => 'min option',
            'maxOption' => 'max option',
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

        // Panel Range class constructor with options
        $panel = new IDX_Panel_Type_Range($options);

        // Test properties set by constructor with arguments
        $this->assertEquals(
            [$options['minInput'], $options['maxInput']],
            $panel->getInputs(),
            'Inputs do not match inputs set in constructor'
        );
        $this->assertEquals(
            $options['minClass'],
            $panel->getMinClass(),
            'Min Class does not match minClass set in constructor'
        );
        $this->assertEquals(
            $options['maxClass'],
            $panel->getMaxClass(),
            'Max Class does not match maxClass set in constructor'
        );
        $this->assertEquals(
            $options['showMin'],
            $panel->getShowMin(),
            'Show Min does not match showMin set in constructor'
        );
        $this->assertEquals(
            $options['showMax'],
            $panel->getShowMax(),
            'Show Max does not match showMax set in constructor'
        );
        $this->assertEquals(
            $options['minOption'],
            $panel->getMinOption(),
            'Min Option does not match minOption set in constructor'
        );
        $this->assertEquals(
            $options['maxOption'],
            $panel->getMaxOption(),
            'Max Option does not match maxOption set in constructor'
        );
    }

    /**
     * @covers IDX_Panel_Type_Range::__construct()
     */
    public function testDefaultProperties()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Type_Range::class)->makePartial();

        // Test properties set by class with no arguments
        $this->assertEquals(
            $this->expectedShowMin,
            $panel->getShowMin(),
            'Show Min does not match showMin set by class'
        );
        $this->assertEquals(
            $this->expectedShowMax,
            $panel->getShowMax(),
            'Show Max does not match showMax set by class'
        );
        $this->assertEquals(
            $this->expectedMinOption,
            $panel->getMinOption(),
            'Min Option does not match minOption set by class'
        );
        $this->assertEquals(
            $this->expectedMaxOption,
            $panel->getMaxOption(),
            'Max Option does not match maxOption set by class'
        );
    }

    /**
     * @covers IDX_Panel_Type_Range::getTags()
     * @dataProvider tagsProvider
     * @param $value array
     * @param $expectedTitle string
     * @param $expectedField array
     * @param $mockTitleMin string
     * @param $mockTitleMax string
     */
    public function testGetTags($value, $expectedTitle, $expectedField, $mockTitleMin, $mockTitleMax)
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Type_Range::class)->makePartial()
            ->shouldAllowMockingProtectedMethods();

        // Set Input Names for Class based on values from data provider
        $minInputName = new ReflectionProperty($panel, 'minInput');
        $minInputName->setAccessible(true);
        $minInputName->setValue($panel, array_keys($value)[0]);
        $maxInputName = new ReflectionProperty($panel, 'maxInput');
        $maxInputName->setAccessible(true);
        $maxInputName->setValue($panel, array_keys($value)[1]);

        // Mock getValues and getOptionTitle
        $panel->shouldReceive('getValue')->andReturn($value);
        $panel->shouldReceive('getOptionTitle')->andReturnValues([$mockTitleMin, $mockTitleMax]);

        // Test getTags with only max returned
        $tags = $panel->getTags();
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
                'Max Value we set did not match the IDX_Search_tag Title returned'
            );
            $this->assertEquals(
                $expectedField,
                $tag->getField(),
                'Max Value and inputName did not match the IDX_Search_Tag Field returned'
            );
        }
    }

    /**
     * @covers IDX_Panel_Type_Range::getTags()
     */
    public function testGetTagsNoValues()
    {
        $minInput = 'Min';
        $maxInput = 'Max';
        $value = [];

        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Type_Range::class)->makePartial();

        // Set Input Names for Class
        $minInputName = new ReflectionProperty($panel, 'minInput');
        $minInputName->setAccessible(true);
        $minInputName->setValue($panel, $minInput);
        $maxInputName = new ReflectionProperty($panel, 'maxInput');
        $maxInputName->setAccessible(true);
        $maxInputName->setValue($panel, $maxInput);

        // Mock getValues and getOptionTitle
        $panel->shouldReceive('getValue')->andReturn($value);

        // Test getTags with no values returned
        $tag = $panel->getTags();
        $this->assertNull(
            $tag,
            'getTags did not return null when no values'
        );
    }

    /**
     * @covers IDX_Panel_Type_Range::getMinOptions()
     */
    public function testGetMinOptions()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Type_Range::class)->makePartial();

        // Mock getOptions
        $panel->shouldReceive('getOptions')
            ->andReturnValues([[], null]);

        $minOption = $panel->getMinOption();
        $expectedOption = [['value' => '', 'title' => $minOption]];

        $this->assertEquals(
            $expectedOption,
            $panel->getMinOptions(),
            'MinOptions does not have expected empty value'
        );
    }

    /**
     * @covers IDX_Panel_Type_Range::getMaxOptions()
     */
    public function testGetMaxOptions()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Type_Range::class)->makePartial();

        // Mock getOptions
        $panel->shouldReceive('getOptions')
            ->andReturnValues([[], null]);

        $maxOption = $panel->getMaxOption();
        $expectedOption = [['value' => '', 'title' => $maxOption]];

        $this->assertEquals(
            $expectedOption,
            $panel->getMaxOptions(),
            'MaxOptions does not have expected empty value'
        );
    }

    /**
     * Provider for different Range values returned
     * @return array
     */
    public function tagsProvider()
    {
        return [
            'Min and Max' => [
                ['Min' => 'value', 'Max' => 'value2'],
                'Mock Title Min - Mock Title Max',
                ['Min' => 'value', 'Max' => 'value2'],
                'Mock Title Min',
                'Mock Title Max'
            ],
            'Min Only' => [
                ['Min' => 'value', 'Max' => ''],
                'More than Mock Title Min',
                ['Min' => 'value'],
                'Mock Title Min',
                ''
            ],
            'Max Only' => [
                ['Min' => '', 'Max' => 'value2'],
                'Less than Mock Title Max',
                ['Max' => 'value2'],
                '',
                'Mock Title Max'
            ]
        ];
    }
}
