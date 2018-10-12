<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel_ReducedPrice;
use IDX;
use Locale;
use Mockery;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\DatabaseInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\Hook\CollectionInterface;
use REW\Core\Interfaces\HooksInterface;
use REW\Core\Interfaces\SettingsInterface;

class ReducedPricePanelTest extends \Codeception\Test\Unit
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
    protected $expectedAllTimeValue = '-1000 YEAR';

    /**
     * @var string
     */
    protected $expectedTitle = 'Reduced Price';

    /**
     * @var string
     */
    protected $expectedInputs = ['search_reduced_price'];

    /**
     * @var string
     */
    protected $expectedField = 'ListingPriceOld';

    /**
     * @var string
     */
    protected $expectedFieldType = 'Radiolist';

    /**
     * @var string
     */
    protected $expectedPlaceholder = 'All Properties';

    /**
     * @var array
     */
    protected $expectedOptions = [
        ['value' => '', 'title' => 'All Properties'],
        ['value' => '-1000 YEAR', 'title' => 'Reduced Price'],
        ['value' => '-1 DAY', 'title' => 'Recently Reduced (within 1 Day)'],
        ['value' => '-7 DAY', 'title' => 'Reduced This Week'],
        ['value' => '-31 DAY', 'title' => 'Reduced This Month']
    ];

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_ReducedPrice::__construct()
     */
    public function testConstruct()
    {
        $options = ['placeholder' => 'Placeholder'];

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
        $panel = new IDX_Panel_ReducedPrice($options);
        $this->assertEquals(
            $options['placeholder'],
            $panel->getPlaceholder(),
            'Placeholder does not match expected placeholder set by constructor'
        );
    }

    /**
     * @covers IDX_Panel_ReducedPrice::__construct()
     */
    public function testDefaultProperties()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_ReducedPrice::class)->makePartial();

        // Test that properties are set correctly
        $this->assertEquals(
            $this->expectedTitle,
            $panel->getTitle(),
            'Title does not match expected title set by class'
        );
        $this->assertEquals(
            $this->expectedInputs,
            $panel->getInputs(),
            'Min and Max Input do not match inputs set by class'
        );
        $this->assertEquals(
            $this->expectedField,
            $panel->getField(),
            'Field does not match expected field set by class'
        );
        $this->assertEquals(
            $this->expectedFieldType,
            $panel->getFieldType(),
            'FieldType does not match expected fieldType set by class'
        );
        $this->assertEquals(
            $this->expectedPlaceholder,
            $panel->getPlaceholder(),
            'Field does not match expected field set by class'
        );
        $this->assertEquals(
            $this->expectedOptions,
            $panel->getOptions(),
            'Options do not match options set by Class'
        );
        $this->assertEquals(
            $this->expectedAllTimeValue,
            $panel::ALL_TIME_VALUE,
            'All Time Value does not match constant set by Class'
        );
    }
    /**
     * @covers IDX_Panel_ReducedPrice::getTags()
     * @dataProvider tagsProvider
     * @param $values string[]
     * @param $expectedTitles string[]
     * @param $expectedFields array
     */
    public function testGetTags($values, $expectedTitles, $expectedFields)
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_ReducedPrice::class)->makePartial();

        // Mock getValues
        $panel->shouldReceive('getValues')->andReturn($values);

        // Test getTags with valid values returned
        $tags = $panel->getTags();
        foreach ($tags as $key => $tag) {
            $this->assertInstanceOf(
                'IDX_Search_Tag',
                $tag,
                'getTags did not return an instance of IDX_Search_Tag'
            );
            $this->assertEquals(
                $expectedTitles[$key],
                $tag->getTitle(),
                'Value we set did not match the IDX_Search_tag returned'
            );
            $this->assertEquals(
                $expectedFields[$key],
                $tag->getField(),
                'Value and inputName did not match the IDX_Search_Tag returned'
            );
        }
    }

    /**
     * Provide values and expected tag title and field
     * @return array
     */
    public function tagsProvider()
    {
        return [
            'No Value' => [
                [''],
                [$this->expectedPlaceholder],
                [[$this->expectedInputs[0] => '']]
            ],
            'This Week' => [
                ['7 day'],
                ['Reduced This Week'],
                [[$this->expectedInputs[0] => '7 day']]
            ]
        ];
    }
}
