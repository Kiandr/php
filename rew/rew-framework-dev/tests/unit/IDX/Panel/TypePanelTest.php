<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel_Type;
use IDX;
use Locale;
use Cache;
use IDX_Panel;
use Mockery;
use Mockery\Exception\NoMatchingExpectationException;
use ReflectionProperty;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\DatabaseInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\Hook\CollectionInterface;
use REW\Core\Interfaces\HooksInterface;
use REW\Core\Interfaces\SettingsInterface;

class TypePanelTest extends \Codeception\Test\Unit
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
    protected $expectedTitle = 'Property Type';

    /**
     * @var string[]
     */
    protected $expectedInputs = ['search_type'];

    /**
     * @var string
     */
    protected $expectedField = 'ListingType';

    /**
     * @var string
     */
    protected $expectedFieldType = 'Checklist';

    /**
     * @var string
     */
    protected $expectedPlaceholder = 'All Properties';

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_Type::__construct()
     */
    public function testConstruct()
    {
        $options = ['placeholder' => 'Test Placeholder'];

        $this->mockCommonClassesAndMethods();
        // Make sure hooks run
        $this->hooks = Mockery::mock(HooksInterface::class)->makePartial();
        $this->hooks->shouldReceive('hook')->with(HooksInterface::HOOK_IDX_PANEL_SETTINGS)
            ->andReturn($this->collection);
        $this->collection->shouldReceive('run');
        $this->hooks->shouldReceive('hook')->with(HooksInterface::HOOK_IDX_PANEL_CONSTRUCT)
            ->andReturn($this->collection);
        $this->collection->shouldReceive('run')->with(IDX_Panel::class);
        $this->container->shouldReceive('get')->with(HooksInterface::class)->andReturn($this->hooks);

        // Mock Panel class
        $panel = new IDX_Panel_Type($options);
        $this->assertEquals(
            $options['placeholder'],
            $panel->getPlaceholder(),
            'Placeholder does not match expected placeholder set by constructor'
        );
    }

    /**
     * @covers IDX_Panel_Type::__construct()
     */
    public function testDefaultProperties()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Type::class)->makePartial();

        // Test that properties are set correctly
        $this->assertEquals(
            $this->expectedTitle,
            $panel->getTitle(),
            'Title does not match title set by class'
        );
        $this->assertEquals(
            $this->expectedInputs,
            $panel->getInputs(),
            'Input does not match inputs set by class'
        );
        $this->assertEquals(
            $this->expectedField,
            $panel->getField(),
            'Field does not match field set by class'
        );
        $this->assertEquals(
            $this->expectedFieldType,
            $panel->getFieldType(),
            'Field Type does not match fieldType set by class'
        );
        $this->assertEquals(
            $this->expectedPlaceholder,
            $panel->getPlaceholder(),
            'Placeholder does not match placeholder set by class'
        );
    }

    /**
     * @covers IDX_Panel_Type::getOptions()
     * @dataProvider optionsProvider
     * @param $optionsSet array
     * @param $optionsResult array
     * @param $placeholder string
     * @param $fieldType string
     * @param $expectedOptions array
     */
    public function testGetOptions($optionsSet, $optionsResult, $placeholder, $fieldType, $expectedOptions)
    {
        $panelProperties = [
            'field' => 'Field',
            'where' => 'Where',
            'order' => 'Order',
            'options' => $optionsSet,
            'placeholder' => $placeholder,
            'fieldType' => $fieldType,
        ];

        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Type::class)->makePartial();

        // Set panel properties used by getOption method
        foreach ($panelProperties as $property => $value) {
            $panelProperties[$property] = new ReflectionProperty($panel, $property);
            $panelProperties[$property]->setAccessible(true);
            $panelProperties[$property]->setValue($panel, $value);
        }

        $this->mockCommonClassesAndMethods();
        $this->db->shouldReceive('query')->andReturn(true);
        $this->idx->shouldReceive('field')->andReturn($panelProperties['field']);
        $this->db->shouldReceive('fetchArray')
            ->andReturnValues($optionsResult);

        $this->assertEquals($expectedOptions, $panel->getOptions());
    }

    /**
     * @covers IDX_Panel_Type::fetchOptions()
     */
    public function testFetchOptions()
    {
        $field = 'Field';
        $order = 'Order';
        $where = 'Where';
        $expectedOrder = "Order,COUNT(`" . $field . "`) DESC";
        $optionsResult = [['title' => 'value1'], false];
        $expectedOptionItems = [['value' => 'value1', 'title' => 'Value1']];

        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Type::class)->makePartial();
        $this->mockCommonClassesAndMethods();
        $this->idx->shouldReceive('field')->andReturn($field);
        $this->db->shouldReceive('fetchArray')
            ->andReturnValues($optionsResult);

        // Make sure args are used in query
        $this->db->shouldReceive('query')->with(Mockery::on(
            function ($arg) use ($field, $where, $expectedOrder) {
                if ((strpos($arg, $field) !== false)
                    && (strpos($arg, $where) !== false)
                    && (strpos($arg, $expectedOrder) !== false)) {
                    return true;
                }
                return false;
            }
        ))->andReturn(true);

        // Test that fetchOptions returns expected format
        try {
            $this->assertEquals($expectedOptionItems, $panel->fetchOptions($field, $where, $order));
        } catch (NoMatchingExpectationException $e) {
            // Catch and fail assertion if query doesn't use field, where, and order arguments
            $this->assertTrue(false, $e->getMessage());
        }
    }

    /**
     * Mock Classes and methods used by parent fetchOptions
     * @return void
     */
    public function mockCommonClassesAndMethods()
    {
        $this->cache = Mockery::mock('alias:Cache');
        $this->cache->shouldReceive('getCache')->andReturnNull();
        $this->cache->shouldReceive('setCache');
        $this->settings = Mockery::mock(SettingsInterface::class);
        $this->container = Mockery::mock('alias:Container');
        $this->collection = Mockery::mock(CollectionInterface::class);
        $this->idxfactory = Mockery::mock(IDXFactoryInterface::class);
        $this->db = Mockery::mock(DBInterface::class);
        $this->idx = Mockery::mock(IDX::class);
        $this->idxfactory->shouldReceive('getIdx')->andReturn($this->idx);
        $this->idxfactory->shouldReceive('getDatabase')->andReturn($this->db);
        $this->idx->shouldReceive('getTable')->andReturn('_rewidx_listings');
        $this->idx->shouldReceive('getName')->andReturn('MFR');
        $this->db->shouldReceive('db')->andReturn('rewidx_mfr');
        $this->idx->shouldReceive('executeSearchWhereCallback');
        $this->container->shouldReceive('getInstance')->andReturn($this->container);
        $this->settings->IDX_FEED = self::IDX_FEED;
        $this->container->shouldReceive('get')->with(SettingsInterface::class)->andReturn($this->settings);
        $this->container->shouldReceive('get')->with(IDXFactoryInterface::class)->andReturn($this->idxfactory);
    }

    /**
     * Provider for getOptions test
     * @return array
     */
    public function optionsProvider()
    {
        return [
            'Options set' => [
                [['value' => 'value1', 'title' => 'Value1']],
                [],
                'No placeholder',
                'Checklist',
                [['value' => 'value1', 'title' => 'Value1']],
            ],
            'No Options set' => [
                null,
                [['title' => 'value1'], false],
                'Placeholder',
                'Checklist',
                [
                    ['value' => '', 'title' => 'Placeholder'],
                    ['value' => 'value1', 'title' => 'Value1']
                ],
            ],
            'No Placeholder' => [
                null,
                [['title' => 'value1'], false],
                null,
                'Checklist',
                [
                    ['value' => 'value1', 'title' => 'Value1']
                ],
            ],
            'Select Field Type' => [
                null,
                [['title' => 'value1'], false],
                'Placeholder',
                'Select',
                [
                    ['value' => 'value1', 'title' => 'Value1']
                ],
            ],
        ];
    }
}
