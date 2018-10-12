<?php

namespace REW\Test\IDX\Panel\Type;

use IDX;
use Locale;
use IDX_Panel;
use IDX_Panel_Price;
use Mockery;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\DatabaseInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\Hook\CollectionInterface;
use REW\Core\Interfaces\HooksInterface;
use REW\Core\Interfaces\SettingsInterface;

class PricePanelTest extends \Codeception\Test\Unit
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
     * Types to consider rentals
     * @var array
     */
    protected $expectedRentalTypes = ['Rental', 'Rentals', 'Lease', 'Residential Lease', 'Commercial Lease', 'Residential Rental'];

    /**
     * Panel Title
     * @var string
     */
    protected $expectedTitle = 'Price Range';

    /**
     * Input Names
     * @var string
     */
    protected $expectedInputs = ['minimum_price', 'maximum_price', 'minimum_rent', 'maximum_rent'];

    /**
     * Placeholder Text for Inputs
     * @var string
     */
    protected $expectedPlaceholders = ['Min', 'Max', 'Min', 'Max'];

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_Price::__construct()
     */
    public function testConstruct()
    {
        $options = [
            'inputMinPrice' => 'inputMinPrice',
            'inputMaxPrice' => 'inputMaxPrice',
            'inputMinRent' => 'inputMinRent',
            'inputMaxRent' => 'inputMaxRent',
            'placeholderMinPrice' => 'placeholderMinPrice',
            'placeholderMaxPrice' => 'placeholderMaxPrice',
            'placeholderMinRent' => 'placeholderMinRent',
            'placeholderMaxRent' => 'placeholderMaxRent'
        ];
        $failMessage = 'Option does not match option set by constructor';

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
        $panel = new IDX_Panel_Price($options);

        $this->assertEquals(
            [
                $options['inputMinPrice'],
                $options['inputMaxPrice'],
                $options['inputMinRent'],
                $options['inputMaxRent']
            ],
            $panel->getInputs(),
            $failMessage
        );
        $this->assertEquals(
            [
                $options['placeholderMinPrice'],
                $options['placeholderMaxPrice'],
                $options['placeholderMinRent'],
                $options['placeholderMaxRent']
            ],
            $panel->getPlaceholders(),
            $failMessage
        );
    }

    /**
     * @covers IDX_Panel_Price::__construct()
     * @return Mockery\MockInterface|IDX_Panel_Price
     */
    public function testDefaultProperties()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Price::class)->makePartial();
        $failMessage = 'Property does not match class property';

        $this->assertEquals($this->expectedRentalTypes, $panel->getRentalTypes(), $failMessage);
        $this->assertEquals($this->expectedTitle, $panel->getTitle(), $failMessage);
        $this->assertEquals($this->expectedInputs, $panel->getInputs(), $failMessage);
        $this->assertEquals($this->expectedPlaceholders, $panel->getPlaceholders(), $failMessage);

        return $panel;
    }

    /**
     * @covers IDX_Panel_Price::getTags()
     * @dataProvider tagsProvider
     * @depends testDefaultProperties
     * @param $value array
     * @param $title string[]
     * @param $expectedField array
     * @param $panel Mockery\MockInterface|IDX_Panel_Price
     */
    public function testGetTags($value, $title, $expectedField, $panel)
    {
        global $_REQUEST;
        foreach ($value as $reqInput => $reqValue) {
            $_REQUEST[$reqInput] = $reqValue;
        }

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
     * @covers IDX_Panel_Price::getTags()
     * @depends testDefaultProperties
     * @param $panel Mockery\MockInterface|IDX_Panel_Price
     */
    public function testGetTagsNoValues($panel)
    {
        $value = [
            'minimum_price' => '',
            'maximum_price' => '',
            'minimum_rent' => '',
            'maximum_rent' => ''
        ];
        global $_REQUEST;
        foreach ($value as $reqInput => $reqValue) {
            $_REQUEST[$reqInput] = $reqValue;
        }
        // Test getTags with no values returned
        $tag = $panel->getTags();

        $this->assertEmpty(
            $tag,
            'getTags did not return an empty array when no values'
        );
    }

    /**
     * @covers IDX_Panel_Price::getValue()
     * @dataProvider valueProvider
     * @depends testDefaultProperties
     * @param $value array
     * @param $panel Mockery\MockInterface|IDX_Panel_Price
     */
    public function testGetValue($value, $panel)
    {
        global $_REQUEST;
        $expectedValue = [];
        foreach ($value as $reqInput => $reqValue) {
            $_REQUEST[$reqInput] = $reqValue;
            if (!empty($reqValue)) {
                $expectedValue[$reqInput] = $reqValue;
            }
        }
        $actualValue = $panel->getValue();

        $this->assertEquals(
            $expectedValue,
            $actualValue,
            "Value in REQUEST does not match value from get Value"
        );
    }

    /**
     * @covers IDX_Panel_Price::getPriceOptions()
     * @depends testDefaultProperties
     * @param $panel Mockery\MockInterface|IDX_Panel_Price
     */
    public function testGetPriceOptions($panel)
    {
        $expectedPriceOptions = $this->getExpectedOptions('Price');
        $actualValue = $panel->getPriceOptions();

        $this->assertEquals($expectedPriceOptions, $actualValue);
    }

    /**
     * @covers IDX_Panel_Price::getRentOptions()
     * @depends testDefaultProperties
     * @param $panel Mockery\MockInterface|IDX_Panel_Price
     */
    public function testGetRentOptions($panel)
    {
        $expectedRentOptions = $this->getExpectedOptions('Rent');
        $actualValue = $panel->getRentOptions();

        $this->assertEquals($expectedRentOptions, $actualValue);
    }

    /**
     * @covers IDX_Panel_Price::getActiveRange()
     * @dataProvider rentalTypeProvider
     * @depends testDefaultProperties
     * @param $types string|string[]
     * @param $expectedRange string
     * @param $panel Mockery\MockInterface|IDX_Panel_Price
     */
    public function testGetActiveRange($types, $expectedRange, $panel)
    {
        global $_REQUEST;
        $_REQUEST['search_type'] = $types;
        $actualRange = $panel->getActiveRange();

        $this->assertEquals($expectedRange, $actualRange);
    }

    /**
     * @covers IDX_Panel_Price::getMinOptions
     * @dataProvider optionsProvider
     * @param $type string Price|Rent
     * @param $expectedOptions array
     */
    public function testGetMinOptions($type, $expectedOptions)
    {
        $expectedMinOptions = array_merge([['value' => '', 'title' => 'Min']], $expectedOptions);

        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Price::class)->makePartial();
        $panel->shouldReceive('getActiveRange')->andReturn($type);
        $panel->shouldReceive('get' . $type . 'Options')->andReturn($expectedOptions);

        $actualOptions = $panel->getMinOptions();
        $this->assertEquals($expectedMinOptions, $actualOptions);
    }

    /**
     * @covers IDX_Panel_Price::getMaxOptions
     * @dataProvider optionsProvider
     * @param $type string Price|Rent
     * @param $expectedOptions array
     */
    public function testGetMaxOptions($type, $expectedOptions)
    {
        $expectedMaxOptions = array_merge([['value' => '', 'title' => 'Max']], $expectedOptions);

        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Price::class)->makePartial();
        $panel->shouldReceive('getActiveRange')->andReturn($type);
        $panel->shouldReceive('get' . $type . 'Options')->andReturn($expectedOptions);

        $actualOptions = $panel->getMaxOptions();
        $this->assertEquals($expectedMaxOptions, $actualOptions);
    }

    /**
     * Provider for different values returned
     * @return array
     */
    public function tagsProvider()
    {
        return [
            'Price & Rent - Min & Max' => [
                [
                    'minimum_price' => '1',
                    'maximum_price' => '2',
                    'minimum_rent' => '3',
                    'maximum_rent' => '4'
                ],
                ['$1 - $2', '$3 - $4'],
                [
                    ['minimum_price' => '1', 'maximum_price' => '2'],
                    ['minimum_rent' => '3', 'maximum_rent' => '4'],
                ]
            ],
            'Price - Min & Max' => [
                [
                    'minimum_price' => '1',
                    'maximum_price' => '2',
                    'minimum_rent' => '',
                    'maximum_rent' => ''
                ],
                ['$1 - $2'],
                [
                    ['minimum_price' => '1', 'maximum_price' => '2'],
                ]
            ],
            'Price - Min' => [
                [
                    'minimum_price' => '1',
                    'maximum_price' => '',
                    'minimum_rent' => '',
                    'maximum_rent' => ''
                ],
                ['Over $1'],
                [
                    ['minimum_price' => '1'],
                ]
            ],
            'Rent - Max' => [
                [
                    'minimum_price' => '',
                    'maximum_price' => '',
                    'minimum_rent' => '',
                    'maximum_rent' => '4',
                ],
                ['Under $4'],
                [
                    ['maximum_rent' => '4'],
                ]
            ],
            'Price - Max & Rent - Min' => [
                [
                    'minimum_price' => '',
                    'maximum_price' => '2',
                    'minimum_rent' => '3',
                    'maximum_rent' => '',
                ],
                ['Under $2', 'Over $3'],
                [
                    ['maximum_price' => '2'],
                    ['minimum_rent' => '3'],
                ]
            ],
        ];
    }

    /**
     * Provide values for $_REQUEST
     * @return array
     */
    public function valueProvider()
    {
        return [
            'All Values' => [
                [
                    'minimum_price' => '1',
                    'maximum_price' => '2',
                    'minimum_rent' => '3',
                    'maximum_rent' => '4'
                ],
            ],
            'Price Only' => [
                [
                    'minimum_price' => '1',
                    'maximum_price' => '2',
                    'minimum_rent' => '',
                    'maximum_rent' => ''
                ],
            ],
            'No Values' => [
                [
                    'minimum_price' => '',
                    'maximum_price' => '',
                    'minimum_rent' => '',
                    'maximum_rent' => ''
                ],
            ],
        ];
    }

    /**
     * Provide different type variations
     * @return array
     */
    public function rentalTypeProvider()
    {
        return [
            'Rental' => ['Rental', 'Rent'],
            'Rental Array' => [['Rental', 'Commercial Lease'], 'Rent'],
            'Non Rental' => ['Residential', 'Price'],
            'Non Rental & Rental array' => [['Residential', 'Rental'], 'Price'],
            'No Type' => ['', 'Price'],
        ];
    }

    /**
     * Provide expected options for Rent and Price
     * @return array
     */
    public function optionsProvider()
    {
        return [
            'Price' => ['Price', $this->getExpectedOptions('Price')],
            'Rent' => ['Rent', $this->getExpectedOptions('Rent')]
        ];
    }

    /**
     * Return Options File based on Type
     * @return array
     */
    public function getExpectedOptions($type)
    {
        $file = __DIR__ . '/Fixtures/' . $type . 'Options.json';
        $content = file_get_contents($file);
        return json_decode($content, true);
    }
}
