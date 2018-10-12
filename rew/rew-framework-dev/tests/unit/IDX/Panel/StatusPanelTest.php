<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel;
use IDX_Panel_Status;
use Mockery;
use IDX;
use Locale;
use Cache;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\DatabaseInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\Hook\CollectionInterface;
use REW\Core\Interfaces\HooksInterface;
use REW\Core\Interfaces\SettingsInterface;

class StatusPanelTest extends \Codeception\Test\Unit
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
     * @var Mockery\MockInterface|Cache
     */
    protected $cache;

    /**
     * @var string
     */
    protected $expectedTitle = 'Listing Status';

    /**
     * @var string[]
     */
    protected $expectedInputs = ['search_status'];

    /**
     * @var string
     */
    protected $expectedField = 'ListingStatus';

    /**
     * @var string
     */
    protected $expectedFieldType = 'Checklist';

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_Status::__construct()
     */
    public function testDefaultProperties()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Status::class)->makePartial();

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
    }

    /**
     * @covers IDX_Panel_Status::fetchOptions()
     * @dataProvider typesProvider
     */
    public function testFetchOptions($types)
    {
        global $_REQUEST;
        $_REQUEST['search_type'] = $types;
        $field = 'Field';
        $where = 'Where';
        $order = 'Order';
        if (!is_array($types)) {
            $types = [$types];
        }

        // Positive and negative result for fetchOptions
        $option_results = [
            ['title' => 'value1'],
            ['title' => 'value2'],
            ['title' => 'value1, value2'],
            false
        ];
        $expected_option_items = [
            ['value' => 'value1', 'title' => 'Value1'],
            ['value' => 'value2', 'title' => 'Value2'],
            ['value' => 'value1, value2', 'title' => 'Value1, Value2'],
        ];

        $this->mockAllTheThings();
        $this->idx->shouldReceive('field')->andReturn($field);
        $this->db->shouldReceive('fetchArray')
            ->andReturnValues($option_results);
        $this->db->shouldReceive('cleanInput')
            ->andReturnValues($types);
        // Make sure args are used in query
        $this->db->shouldReceive('query')->with(Mockery::on(function ($arg) use ($field, $where, $order, $types) {
            if ((strpos($arg, $field) !== false)
                    && (strpos($arg, $where) !== false)
                    && (strpos($arg, $order) !== false)) {
                foreach ($types as $type) {
                    if (strpos($arg, $type) === false) {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }))->andReturn(true);

        // Mock Panel class
        $panel = new IDX_Panel_Status();

        // Test that fetchOptions returns expected format
        $this->assertEquals(
            $expected_option_items,
            $panel->fetchOptions($field, $where, $order),
            'Types and fetchOptions parameters not used for fetchOptions'
        );
    }

    /**
     * Mock All the things required for constructor and parent fetchOptions
     * @return void
     */
    public function mockAllTheThings()
    {
        // Mock Classes
        $this->settings = Mockery::mock(SettingsInterface::class);
        $this->container = Mockery::mock('alias:Container');
        $this->collection = Mockery::mock(CollectionInterface::class);
        $this->idxfactory = Mockery::mock(IDXFactoryInterface::class);
        $this->db = Mockery::mock(DBInterface::class);
        $this->idx = Mockery::mock(IDX::class);
        $this->cache = Mockery::mock('alias:Cache');
        $this->cache->shouldReceive('getCache')->andReturnNull();
        $this->cache->shouldReceive('setCache');

        // Mock IDX methods
        $this->idxfactory->shouldReceive('getIdx')->andReturn($this->idx);
        $this->idxfactory->shouldReceive('getDatabase')->andReturn($this->db);
        $this->idx->shouldReceive('getTable')->andReturn('_rewidx_listings');
        $this->idx->shouldReceive('getName')->andReturn('MFR');
        $this->db->shouldReceive('db')->andReturn('rewidx_mfr');
        $this->idx->shouldReceive('executeSearchWhereCallback');

        // Mock methods
        $this->container->shouldReceive('getInstance')->andReturn($this->container);
        $this->settings->IDX_FEED = self::IDX_FEED;
        $this->container->shouldReceive('get')->with(SettingsInterface::class)->andReturn($this->settings);
        $this->container->shouldReceive('get')->with(IDXFactoryInterface::class)->andReturn($this->idxfactory);
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
    }

    /**
     * Provides Types for REQUEST
     * @return array
     */
    public function typesProvider()
    {
        return [
            '1 Type' => ['Type1'],
            '2 Type' => [['Type1', 'Type2']],
        ];
    }
}
