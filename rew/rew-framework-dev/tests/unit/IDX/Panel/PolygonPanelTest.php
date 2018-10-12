<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel_Polygon;
use IDX;
use Locale;
use IDX_Panel;
use Mockery;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\DatabaseInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\Hook\CollectionInterface;
use REW\Core\Interfaces\HooksInterface;
use REW\Core\Interfaces\SettingsInterface;

class PolygonPanelTest extends \Codeception\Test\Unit
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
    protected $expectedTitle = 'Polygon Search';

    /**
     * @var string
     */
    protected $expectedTooltip = 'Click on the map to draw your polygon search.';

    /**
     * @var string
     */
    protected $expectedControlId = 'GPolygonControl';

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_Polygon::__construct()
     */
    public function testConstruct()
    {
        $options = [
            'tooltip' => 'Tooltip',
            'control_id' => 'Control ID'
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
        $panel = new IDX_Panel_Polygon($options);
        $this->assertEquals(
            $options['tooltip'],
            $panel->getTooltip(),
            'Tooltip does not match expected tooltip set by constructor'
        );
        $this->assertEquals(
            $options['control_id'],
            $panel->getControlId(),
            'Tooltip does not match expected tooltip set by constructor'
        );
    }

    /**
     * @covers IDX_Panel_Polygon::__construct()
     */
    public function testDefaultProperties()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Polygon::class)->makePartial();

        // Test that properties are set correctly
        $this->assertEquals(
            $this->expectedTitle,
            $panel->getTitle(),
            'Title does not match expected title set by class'
        );
        $this->assertEquals(
            $this->expectedTooltip,
            $panel->getTooltip(),
            'Tooltip does not match expected tooltip set by class'
        );
        $this->assertEquals(
            $this->expectedControlId,
            $panel->getControlId(),
            'Control ID does not match expected control id set by class'
        );
    }

    /**
     * @covers IDX_Panel_Polygon::getTags()
     */
    public function testGetTags()
    {
        $value = 1;
        $expectedTagTitle = 'In Polygon';
        $expectedTagField = ['polygon' => 1];

        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Polygon::class)->makePartial();

        // Mock getValues
        $panel->shouldReceive('getValue')->andReturn($value);

        // Test getTags with both min and max returned
        $tag = $panel->getTags();
        $this->assertInstanceOf(
            'IDX_Search_Tag',
            $tag,
            'getTags did not return an instance of IDX_Search_Tag'
        );
        $this->assertEquals(
            $expectedTagTitle,
            $tag->getTitle(),
            'Class expected Title did not match the IDX_Search_tag returned'
        );
        $this->assertEquals(
            $expectedTagField,
            $tag->getField(),
            'Class expected field did not match the IDX_Search_Tag returned'
        );
    }

    /**
     * @covers IDX_Panel_Polygon::getTags()
     */
    public function testGetTagsNoValues()
    {
        $value = '';

        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Polygon::class)->makePartial();

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
