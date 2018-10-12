<?php

namespace REW\Test\IDX\Panel;

use IDX_Panel_Mls;
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

class MlsPanelTest extends \Codeception\Test\Unit
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
    protected $expectedTitle = 'MLS&reg; Number';

    /**
     * @var string[]
     */
    protected $expectedInputs = ['search_mls'];

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers IDX_Panel_MLS::__construct()
     * @dataProvider constructProvider
     * @param $options array
     * @param $expectedTitle string
     */
    public function testConstruct($options, $expectedTitle)
    {
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
        $panel = new IDX_Panel_Mls($options);
        $this->assertEquals(
            $expectedTitle,
            $panel->getTitle(),
            'Title does not match expected title set by constructor'
        );
    }

    /**
     * @covers IDX_Panel_Mls::__construct()
     */
    public function testDefaultProperties()
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Mls::class)->makePartial();

        // Test that properties are set correctly
        $this->assertEquals(
            $this->expectedInputs,
            $panel->getInputs(),
            'Input does not match inputs set by class'
        );
    }

    /**
     * @covers IDX_Panel_Mls::getTags()
     * @dataProvider tagsProvider
     * @param $values string[]
     * @param $expectedTitles string[]
     * @param $expectedFields array
     */
    public function testGetTags($values, $expectedTitles, $expectedFields)
    {
        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Mls::class)->makePartial();

        // Mock getValues
        $panel->shouldReceive('getValues')->andReturn($values);

        // Test getTags with valid values returned
        $tags = $panel->getTags();
        $this->assertInternalType(
            'array',
            $tags,
            'Tags returned are not an array'
        );
        foreach ($tags as $key => $tag) {
            $this->assertInstanceOf(
                'IDX_Search_Tag',
                $tag,
                'getTags did not return an instance of IDX_Search_Tag'
            );
            $this->assertEquals(
                $expectedTitles[$key],
                $tag->getTitle(),
                'Values we set did not match the IDX_Search_tag returned'
            );
            $this->assertEquals(
                $expectedFields[$key],
                $tag->getField(),
                'Value and inputName did not match the IDX_Search_Tag returned'
            );
        }
    }

    /**
     * @covers IDX_Panel_Mls::getTags()
     */
    public function testGetTagsNoValues()
    {
        $value = [];

        // Mock Panel class
        $panel = Mockery::mock(IDX_Panel_Mls::class)->makePartial();

        // Mock getValues
        $panel->shouldReceive('getValues')->andReturn($value);

        // Test getTags with no values returned
        $tag = $panel->getTags();
        $this->assertEquals(
            [],
            $tag,
            'getTags did not return an empty array when no values'
        );
    }

    /**
     * Provide values and expected tag title and value
     * @return array
     */
    public function tagsProvider()
    {
        return [
            '1 Value' => [
                ['value'],
                ['MLS&reg; #value'],
                [['search_mls' => 'value']]
            ],
            '2 Values' => [
                ['value', 'value2'],
                ['MLS&reg; #value', 'MLS&reg; #value2'],
                [['search_mls' => 'value'], ['search_mls' => 'value2']]
            ]
        ];
    }

    /**
     * Provide two cases for construct
     * @return array
     */
    public function constructProvider()
    {
        return [
            'title option set' => [
               ['title' => 'Mock Title'],
               $this->expectedTitle
            ],
            'No option set' => [
                [],
                $this->expectedTitle
            ],
        ];
    }
}
