<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel_Rooms;
use Mockery;
use IDX;
use Locale;
use IDX_Panel;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\DatabaseInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\Hook\CollectionInterface;
use REW\Core\Interfaces\HooksInterface;
use REW\Core\Interfaces\SettingsInterface;

class RoomsPanelTest extends \Codeception\Test\Unit
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
    protected $expectedTitle = 'Rooms';

    /**
     * @var string
     */
    protected $expectedShowTitle = false;

    /**
     * @var string[]
     */
    protected $expectedInputs = ['minimum_bedrooms', 'minimum_bathrooms'];

    /**
     * @var string[]
     */
    protected $expectedPlaceholders = ['-', '-'];

    /**
     * @var array
     */
    protected $expectedOptions = [
        ['value' => 1, 'title' => '1'],
        ['value' => 2, 'title' => '2'],
        ['value' => 3, 'title' => '3'],
        ['value' => 4, 'title' => '4'],
        ['value' => 5, 'title' => '5'],
        ['value' => 6, 'title' => '6'],
        ['value' => 7, 'title' => '7'],
        ['value' => 8, 'title' => '8']
    ];

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_Rooms::__construct()
     */
    public function testConstruct()
    {
        $options = [
            'inputNameBeds' => 'input_beds',
            'inputNameBaths' => 'input_baths',
            'placeholderBeds' => 'placeholder beds',
            'placeholderBaths' => 'placeholder baths',
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
        $panel = new IDX_Panel_Rooms($options);

        $expectedFormatOptions = [$panel, '_formatOption'];

        $this->assertEquals(
            [$options['inputNameBeds'], $options['inputNameBaths']],
            $panel->getInputs(),
            'Inputs do not match Inputs set by constructor'
        );
        $this->assertEquals(
            [$options['placeholderBeds'], $options['placeholderBaths']],
            $panel->getPlaceholders(),
            'Placeholders do not match Placeholders set by constructor'
        );
        $this->assertEquals(
            $expectedFormatOptions,
            $panel->getFormatOptions(),
            'Format Options callable does not match callable set by constructor'
        );
    }

    /**
     * @covers IDX_Panel_Rooms::__construct()
     * @return Mockery\MockInterface|IDX_Panel_Rooms
     */
    public function testDefaultProperties()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Rooms::class)->makePartial();
        $failMessage = 'Property does not match expected property set by class';

        // Test that properties are set correctly
        $this->assertEquals($this->expectedTitle, $panel->getTitle(), $failMessage);
        $this->assertEquals($this->expectedShowTitle, $panel->showTitle(), $failMessage);
        $this->assertEquals($this->expectedInputs, $panel->getInputs(), $failMessage);
        $this->assertEquals($this->expectedPlaceholders, $panel->getPlaceholders(), $failMessage);
        $this->assertEquals($this->expectedOptions, $panel->getOptions(), $failMessage);

        return $panel;
    }

    /**
     * @covers IDX_Panel_Rooms::getValue
     * @dataProvider valueProvider
     * @depends testDefaultProperties
     * @param $value array
     * @param $panel Mockery\MockInterface|IDX_Panel_Rooms
     */
    public function testGetValue($value, $panel)
    {
        global $_REQUEST;
        $expectedValue = [];
        foreach ($value as $input => $val) {
            if (!empty($val)) {
                $expectedValue[$input] = $val;
            }
            $_REQUEST[$input] = $val;
        }

        $this->assertEquals($expectedValue, $panel->getValue());
    }

    /**
     * Provide sample values for getValue
     * @return array
     */
    public function valueProvider()
    {
        return [
            'Beds & Baths' => [['minimum_bedrooms' => 2, 'minimum_bathrooms' => 1]],
            'Beds' => [['minimum_bedrooms' => 2, 'minimum_bathrooms' => '']],
            'Baths' => [['minimum_bedrooms' => '', 'minimum_bathrooms' => 1]],
            'No Value' => [['minimum_bedrooms' => '', 'minimum_bathrooms' => '']],
        ];
    }
}
