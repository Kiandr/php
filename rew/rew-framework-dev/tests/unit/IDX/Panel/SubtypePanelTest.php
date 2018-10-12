<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel_Subtype;
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

class SubtypePanelTest extends \Codeception\Test\Unit
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
    protected $expectedTitle = 'Property Sub-Type';

    /**
     * @var string[]
     */
    protected $expectedInputs = ['search_subtype'];

    /**
     * @var string
     */
    protected $expectedField = 'ListingSubType';

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
     * @covers IDX_Panel_Subtype::__construct()
     */
    public function testConstruct()
    {
        $options = ['delimiter' => ', '];

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
        $panel = new IDX_Panel_Subtype($options);
        $this->assertEquals(
            $options['delimiter'],
            $panel->getDelimiter(),
            'Delimiter does not match expected delimiter set by constructor'
        );
    }

    /**
     * @covers IDX_Panel_Subtype::__construct()
     */
    public function testDefaultProperties()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Subtype::class)->makePartial();

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
            $this->expectedPlaceholder,
            $panel->getPlaceholder(),
            'Placeholder does not match placeholder set by class'
        );
    }

    /**
     * @covers IDX_Panel_Subtype::getOptions()
     * @dataProvider optionsProvider
     * @param $optionsSet array
     * @param $optionsResult array
     * @param $placeholder string
     * @param $types string|string[]
     * @param $delimiter string
     * @param $expectedOptions array
     */
    public function testGetOptions($optionsSet, $optionsResult, $placeholder, $types, $delimiter, $expectedOptions)
    {
        global $_REQUEST;
        $_REQUEST = [];
        $_REQUEST['search_type'] = $types;

        $panelProperties = [
            'field' => 'Field',
            'where' => 'Where',
            'order' => 'Order',
            'options' => $optionsSet,
            'placeholder' => $placeholder,
            'delimiter' => $delimiter,
        ];

        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Subtype::class)->makePartial();

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
     * @covers IDX_Panel_Subtype::fetchOptions()
     * @dataProvider fetchOptionsProvider
     * @param $types string|string[]
     * @param $expectedWhere string
     */
    public function testFetchOptions($types, $expectedWhere)
    {
        global $_REQUEST;
        $_REQUEST = [];
        $_REQUEST['search_type'] = $types;
        $types = is_array($types) ? $types : (explode(', ', $types));

        $field = 'Field';
        $order = 'Order';
        $where = 'Where';
        $expectedOrder = "Order,COUNT(`" . $field . "`) DESC";
        $optionsResult = [['title' => 'value1'], false];
        $expectedOptionItems = [['value' => 'value1', 'title' => 'Value1']];

        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Subtype::class)->makePartial();
        $this->mockCommonClassesAndMethods();
        $this->idx->shouldReceive('field')->with($field)->andReturn($field);
        $this->idx->shouldReceive('field')->with('ListingType')->andReturn('ListingType');
        foreach ($types as $type) {
            $this->db->shouldReceive('cleanInput')->with($type)->andReturn($type);
        }
        $this->db->shouldReceive('fetchArray')
            ->andReturnValues($optionsResult);

        // Make sure args are used in query
        $this->db->shouldReceive('query')->with(Mockery::on(
            function ($arg) use ($field, $expectedWhere, $expectedOrder) {
                if ((strpos($arg, $field) !== false)
                    && (strpos($arg, $expectedWhere) !== false)
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
     * @covers IDX_Panel_Subtype::getAllTypes()
     */
    public function testGetAllTypes()
    {
        $fieldType = 'ListingType';
        $fieldSubType = 'ListingSubType';
        $optionsResult = [
            ['type' => 'Type1', 'sub_type' => 'SubType1'],
            ['type' => 'Type1', 'sub_type' => 'SubType2'],
            ['type' => 'Type2', 'sub_type' => ''],
            ['type' => 'Type3', 'sub_type' => 'SubType3'],
            ['type' => 'Type4', 'sub_type' => 'SubType1'],
            ['type' => 'Type4', 'sub_type' => ''],
            false
        ];
        $expectedOptions = [
            'Type1' => [
                ['value' => 'SubType1', 'title' => 'Subtype1'],
                ['value' => 'SubType2', 'title' => 'Subtype2'],
            ],
            'Type2' => [
                ['value' => '', 'title' => ''],
            ],
            'Type3' => [
                ['value' => 'SubType3', 'title' => 'Subtype3'],
            ],
            'Type4' => [
                ['value' => 'SubType1', 'title' => 'Subtype1'],
                ['value' => '', 'title' => ''],
            ],
        ];

        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Subtype::class)->makePartial();
        $this->mockCommonClassesAndMethods();
        $this->idx->shouldReceive('field')->with($fieldSubType)->andReturn($fieldSubType);
        $this->idx->shouldReceive('field')->with($fieldType)->andReturn($fieldType);
        $this->db->shouldReceive('fetchArray')
            ->andReturnValues($optionsResult);

        // Make sure args are used in query
        $this->db->shouldReceive('query')->with(Mockery::on(
            function ($arg) use ($fieldType, $fieldSubType) {
                if ((strpos($arg, $fieldType) !== false)
                    && (strpos($arg, $fieldSubType) !== false)) {
                    return true;
                }
                return false;
            }
        ))->andReturn(true);

        $this->assertEquals($expectedOptions, $panel->getAllTypes());
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
            'Options set with delim' => [
                [['value' => 'value1, value2', 'title' => 'value1, value2']],
                [false],
                null,
                '',
                ', ',
                [['value' => 'value2', 'title' => 'value2'], ['value' => 'value1', 'title' => 'value1']],
            ],
            'No Options set and no delim' => [
                null,
                [['title' => 'value1, value2'], false],
                'Placeholder',
                null,
                null,
                [
                    ['value' => '', 'title' => 'Placeholder'],
                    ['value' => 'value1, value2', 'title' => 'Value1, Value2']
                ],
            ],
            'No Options set with delim' => [
                null,
                [['title' => 'value1, value2'], false],
                'Placeholder',
                null,
                ', ',
                [
                    ['value' => '', 'title' => 'Placeholder'],
                    ['value' => 'Value2', 'title' => 'Value2'],
                    ['value' => 'Value1', 'title' => 'Value1'],
                ],
            ],
            'No placeholder and type string' => [
                [['value' => 'value1, value2', 'title' => 'value1, value2']],
                [false],
                null,
                'Type1',
                null,
                [
                    ['value' => '', 'title' => 'All Type1 Listings'],
                    ['value' => 'value1, value2', 'title' => 'value1, value2'],
                ],
            ],
            'No placeholder and 1 type array' => [
                [['value' => 'value1, value2', 'title' => 'value1, value2']],
                [false],
                null,
                ['Type1'],
                null,
                [
                    ['value' => '', 'title' => 'All Type1 Listings'],
                    ['value' => 'value1, value2', 'title' => 'value1, value2'],
                ],
            ],
            'No placeholder and multi type array' => [
                [['value' => 'value1, value2', 'title' => 'value1, value2']],
                [false],
                null,
                ['Type1', 'Type2'],
                null,
                [
                    ['value' => '', 'title' => 'All Properties'],
                    ['value' => 'value1, value2', 'title' => 'value1, value2'],
                ],
            ],
        ];
    }

    /**
     * Provide types and expected where result for fetchOptions
     * @return array
     */
    function fetchOptionsProvider()
    {
        return [
            '1 Type' => [
                'Type1',
                "Where AND (`ListingType` = 'Type1') AND `ListingType` != ''"
            ],
            'Types array' => [
                ['Type1', 'Type2'],
                "Where AND (`ListingType` = 'Type1' OR `ListingType` = 'Type2') AND `ListingType` != ''"
            ],
            'Types string' => [
                'Type1, Type2',
                "Where AND (`ListingType` = 'Type1' OR `ListingType` = 'Type2') AND `ListingType` != ''"
            ],
        ];
    }
}
