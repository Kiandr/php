<?php

namespace REW\Test\IDX;

use Mockery\Exception\NoMatchingExpectationException;
use REW\Core\Interfaces\DatabaseInterface;
use REW\Core\Interfaces\Hook\CollectionInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\HookInterface;
use REW\Core\Interfaces\HooksInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\DBInterface;
use ReflectionProperty;
use Cache;
use IDX_Panel;
use IDX;
use Locale;
use Mockery;

class PanelTest extends \Codeception\Test\Unit
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
     * @return void
     */
    protected function _before()
    {
        $this->settings = Mockery::mock(SettingsInterface::class);
        $this->container = Mockery::mock('alias:Container');
        $this->collection = Mockery::mock(CollectionInterface::class);
        $this->idxfactory = Mockery::mock(IDXFactoryInterface::class);
        $this->db = Mockery::mock(DBInterface::class);
        $this->idx = Mockery::mock(IDX::class);
    }

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers \IDX_Panel::__construct()
     */
    public function testConstruct()
    {

        $expectedOptionItems = [
            ['value' => 'value1', 'title' => 'Value1'],
            ['value' => 'value2', 'title' => 'Value2'],
            ['value' => 'value1, value2', 'title' => 'Value1, Value2'],
        ];

        $options = [
            'id' => 'panel',
            'mode' => 'mode',
            'title' => 'test',
            'field' => 'ListingStatus',
            'display' => false,
            'toggle' => true,
            'closed' => false,
            'hidden' => true,
            'options' => $expectedOptionItems,
            'formatOptions' => function ($options) {
                return $options;
            },
            'inputName' => 'test',
            'inputClass' => 'input',
            'showTitle' => false,
            'titleElement' => 'div',
            'hiddenClass' => 'hidden'
        ];
        $hookSettingsArray = ['setTitleClasses' => 'class'];

        // Mock Collection and Hooks objects
        $this->collection = Mockery::mock(CollectionInterface::class);
        $this->hooks = Mockery::mock(HooksInterface::class)->makePartial();

        // Make sure hooks run
        $this->hooks->shouldReceive('hook')->with(HooksInterface::HOOK_IDX_PANEL_SETTINGS)->once()
            ->andReturn($this->collection);
        $this->collection->shouldReceive('run')->with($options['id'])
            ->andReturn($hookSettingsArray)->once();
        $this->hooks->shouldReceive('hook')->with(HooksInterface::HOOK_IDX_PANEL_CONSTRUCT)->once()
            ->andReturn($this->collection);
        $this->collection->shouldReceive('run')->with(IDX_Panel::class)
            ->andReturnUsing(function ($panel) {
                $panel->setLocked(true);
            })->once();
        $this->container->shouldReceive('get')->with(HooksInterface::class)->andReturn($this->hooks)->twice();
        $this->mockCommonConstructorMethods();

        // Mock Panel class
        $panel = $this->getMockBuilder('IDX_Panel')
            ->setMockClassName('MockPanel')
            ->setConstructorArgs([$options])
            ->getMockForAbstractClass();

        // Confirm Panel Settings Hook ran
        $titleClassP = new ReflectionProperty('MockPanel', 'titleClasses');
        $titleClassP->setAccessible(true);
        $this->assertEquals(
            $hookSettingsArray['setTitleClasses'],
            $titleClassP->getValue($panel),
            'Settings Hook was not run'
        );

        // Confirm Panel Constructor Hook ran
        $this->assertEquals(
            true,
            $panel->isLocked(),
            'Constructor Hook was not run'
        );

        // Confirm Constructor set options passed in args
        foreach ($options as $key => $value) {
            $keyProp = new ReflectionProperty('MockPanel', $key);
            $keyProp->setAccessible(true);
            $this->assertEquals(
                $value,
                $keyProp->getValue($panel),
                'Option was not set by constructor ' . $key
            );
        }

        return $panel;
    }

    /**
     * @covers IDX_Panel::checkField
     */
    public function testCheckField()
    {
        $this->mockCommonIDXDB();
        $this->mockCommonConstructorMethods();

        // Positive and negative result for checkField
        $checkFieldResults = [['field' => 'result'], false];
        $this->db->shouldReceive('fetchQuery')
            ->andReturnValues($checkFieldResults);

        $field = 'Field';

        // Check field only gets called if field is populated
        $this->idx->shouldReceive('field')->andReturn($field);
        $this->assertTrue(IDX_Panel::checkField($field), "Positive result didn't return true");
        $this->assertFalse(IDX_Panel::checkField($field), "Negative result didn't return false");

        // If field doesn't exist confirm checkField still returns false
        $this->idx->shouldReceive('field')->andReturn(false);
        $this->assertFalse(IDX_Panel::checkField($field), "False field didn't return false");
    }

    /**
     * @covers IDX_Panel::fetchOptions
     */
    public function testFetchOptions()
    {
        $field = 'Field';
        $where = 'Where';
        $order = 'Order';

        // Positive and negative result for fetchOptions
        $optionResults = [
            ['title' => 'value1'],
            ['title' => 'value2'],
            ['title' => 'value1, value2'],
            false
        ];
        $expectedOptionItems = [
            ['value' => 'value1', 'title' => 'Value1'],
            ['value' => 'value2', 'title' => 'Value2'],
            ['value' => 'value1, value2', 'title' => 'Value1, Value2'],
        ];

        // Mock IDXDB methods and classes
        $this->mockCommonIDXDB();
        $this->idx->shouldReceive('field')->andReturn($field);
        $this->idx->shouldReceive('executeSearchWhereCallback');
        $this->db->shouldReceive('fetchArray')
            ->andReturnValues($optionResults);
        $this->mockCommonConstructorMethods();

        // Make sure args are used in query
        $this->db->shouldReceive('query')->with(Mockery::on(function ($arg) use ($field, $where, $order) {
            if ((strpos($arg, $field) !== false)
                    && (strpos($arg, $where) !== false)
                    && (strpos($arg, $order) !== false)) {
                return true;
            }
            return false;
        }))->andReturn(true);

        // Test that fetchOptions returns expected format
        try {
            $this->assertEquals($expectedOptionItems, IDX_Panel::fetchOptions($field, $where, $order));
        } catch (NoMatchingExpectationException $e) {
            // Catch and fail assertion if query doesn't use field, where, and order arguments
            $this->assertTrue(false, $e->getMessage());
        }
    }

    /**
     * @covers IDX_Panel::getOptions()
     * @dataProvider optionsProvider
     * @param $panelProperties array
     * @param $expectedResult array
     */
    public function testGetOptions($panelProperties, $expectedResult)
    {
        $optionResults = [
            ['title' => 'value1'],
            ['title' => 'value2'],
            false
        ];

        // Mock Panel class
        $panel = $this->getMockBuilder('IDX_Panel')
            ->setMockClassName('MockPanel')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        // Set panel properties used by getOption method
        foreach ($panelProperties as $property => $value) {
            $panelProperties[$property] = new ReflectionProperty($panel, $property);
            $panelProperties[$property]->setAccessible(true);
            $panelProperties[$property]->setValue($panel, $value);
        }

        // Mock Classes and methods used by fetchOptions
        $this->mockCommonIDXDB();
        $this->idx->shouldReceive('field')->andReturn($panelProperties['field']);
        $this->idx->shouldReceive('executeSearchWhereCallback');
        $this->db->shouldReceive('fetchArray')
            ->andReturnValues($optionResults);
        $this->db->shouldReceive('query')->andReturn(true);
        $this->mockCommonConstructorMethods();

        // Make sure options we set earlier are returned
        $this->assertEquals(
            $expectedResult,
            $panel->getOptions(),
            'Expected Options do not match options returned'
        );
    }

    /**
     * @covers IDX_Panel::getClosed()
     * @covers IDX_Panel::setClosed()
     * @depends testConstruct
     * @param $panel Mockery\MockInterface|IDX_Panel
     */
    public function testGetSetClosed($panel)
    {
        // Test get after set
        foreach ([true, false] as $closed) {
            $panel->setClosed($closed);
            $this->assertEquals(
                $closed,
                $panel->getClosed(),
                'Get Closed does not match set Closed value'
            );
        }
    }

    /**
     * @covers IDX_Panel::isHidden()
     * @covers IDX_Panel::setHidden()
     * @depends testConstruct
     * @param $panel Mockery\MockInterface|IDX_Panel
     */
    public function testGetSetHidden($panel)
    {
        // Test get after set
        foreach ([true, false] as $hidden) {
            $panel->setHidden($hidden);
            $this->assertEquals(
                $hidden,
                $panel->isHidden(),
                'Get Hidden does not match set Hidden value'
            );
        }
    }

    /**
     * @covers IDX_Panel::getDisplay()
     * @covers IDX_Panel::setDisplay()
     * @depends testConstruct
     * @param $panel Mockery\MockInterface|IDX_Panel
     */
    public function testGetSetDisplay($panel)
    {
        // Test get after set
        foreach ([true, false] as $display) {
            $panel->setDisplay($display);
            $this->assertEquals(
                $display,
                $panel->getDisplay(),
                'Get Display does not match set Display value'
            );
        }
    }

    /**
     * @covers IDX_Panel::getToggle()
     * @covers IDX_Panel::setToggle()
     * @depends testConstruct
     * @param $panel Mockery\MockInterface|IDX_Panel
     */
    public function testGetSetToggle($panel)
    {
        // Test get after set
        foreach ([true, false] as $toggle) {
            $panel->setToggle($toggle);
            $this->assertEquals(
                $toggle,
                $panel->getToggle(),
                'Get Toggle does not match set Toggle value'
            );
        }
    }

    /**
     * @covers IDX_Panel::setTitleElement()
     * @depends testConstruct
     * @param $panel Mockery\MockInterface|IDX_Panel
     */
    public function testSetTitleElement($panel)
    {
        $titleElement = 'title element';

        $panel->setTitleElement($titleElement);
        $titleElementP = new ReflectionProperty($panel, 'titleElement');
        $titleElementP->setAccessible(true);

        $this->assertEquals(
            $titleElement,
            $titleElementP->getValue($panel),
            'TitleElement does not match after set'
        );
    }

    /**
     * @covers IDX_Panel::setContainerElement
     * @depends testConstruct
     * @param $panel Mockery\MockInterface|IDX_Panel
     */
    public function testSetContainerElement($panel)
    {
        $containerElement = 'container element';

        $panel->setContainerElement($containerElement);
        $containerElementP = new ReflectionProperty($panel, 'containerElement');
        $containerElementP->setAccessible(true);

        $this->assertEquals(
            $containerElement,
            $containerElementP->getValue($panel),
            'Container Element does not match after set'
        );
    }

    /**
     * @covers IDX_Panel::setTitleClasses
     * @depends testConstruct
     * @param $panel Mockery\MockInterface|IDX_Panel
     */
    public function testSetTitleClasses($panel)
    {
        $titleClasses = 'Title Classes';

        $panel->setTitleClasses($titleClasses);
        $titleClassesP = new ReflectionProperty($panel, 'titleClasses');
        $titleClassesP->setAccessible(true);

        $this->assertEquals(
            $titleClasses,
            $titleClassesP->getValue($panel),
            'Title Classes does not match after set'
        );
    }

    /**
     * @covers IDX_Panel::getPanelClass()
     * @covers IDX_Panel::setPanelClass()
     * @depends testConstruct
     * @param $panel Mockery\MockInterface|IDX_Panel
     */
    public function testGetSetPanelClass($panel)
    {
        // Test get after set
        foreach (['Panel Class'] as $panelClass) {
            $panel->setPanelClass($panelClass);
            $this->assertEquals(
                $panelClass,
                $panel->getPanelClass(),
                'Get PanelClass does not match set PanelClass value'
            );
        }
    }

    /**
     * @covers IDX_Panel::setDetailsClass
     * @depends testConstruct
     * @param $panel Mockery\MockInterface|IDX_Panel
     */
    public function testSetDetailsClass($panel)
    {
        $detailsClass = 'details class';

        $panel->setDetailsClass($detailsClass);
        $detailsClassP = new ReflectionProperty($panel, 'detailsClass');
        $detailsClassP->setAccessible(true);

        $this->assertEquals(
            $detailsClass,
            $detailsClassP->getValue($panel),
            'Details class does not match after set'
        );
    }

    /**
     * @covers IDX_Panel::setHiddenClass
     * @depends testConstruct
     * @param $panel Mockery\MockInterface|IDX_Panel
     */
    public function testSetHiddenClass($panel)
    {
        $hiddenClass = 'hidden class';

        $panel->setHiddenClass($hiddenClass);
        $hiddenClassP = new ReflectionProperty($panel, 'hiddenClass');
        $hiddenClassP->setAccessible(true);

        $this->assertEquals(
            $hiddenClass,
            $hiddenClassP->getValue($panel),
            'Hidden class does not match after set'
        );
    }

    /**
     * @covers IDX_Panel::getInputClass()
     * @covers IDX_Panel::setInputClass()
     * @depends testConstruct
     * @param $panel Mockery\MockInterface|IDX_Panel
     */
    public function testGetSetInputClass($panel)
    {
        $actualInputClass = 'class';

        $inputClassP = new ReflectionProperty($panel, 'inputClass');
        $inputClassP->setAccessible(true);
        $inputClassP->setValue($panel, $actualInputClass);

        // Test get after set
        $expectedInputClass = $actualInputClass;
        foreach (['class1', 'class2'] as $inputClass) {
            $panel->setInputClass($inputClass);
            $expectedInputClass = $expectedInputClass . ' ' . $inputClass;
            $this->assertEquals(
                $expectedInputClass,
                $panel->getInputClass(),
                'Get InputClass does not match set InputClass values'
            );
        }
    }

    /**
     * @covers IDX_Panel::getFormGroup()
     * @covers IDX_Panel::setFormGroup()
     * @depends testConstruct
     * @param $panel Mockery\MockInterface|IDX_Panel
     */
    public function testGetSetFormGroup($panel)
    {
        // Test get after set
        foreach (['formGroup1', 'formGroup2'] as $formGroup) {
            $panel->setFormGroup($formGroup);
            $this->assertEquals(
                $formGroup,
                $panel->getFormGroup(),
                'Get FormGroup does not match set FormGroup value'
            );
        }
    }

    /**
     * @covers IDX_Panel::setMarkupStyle()
     * @depends testConstruct
     * @param $panel Mockery\MockInterface|IDX_Panel
     */
    public function testSetMarkupStyle($panel)
    {
        $markupStyle = 'markup style';

        $panel->setMarkupStyle($markupStyle);
        $markupStyleP = new ReflectionProperty($panel, 'markupStyle');
        $markupStyleP->setAccessible(true);

        $this->assertEquals(
            $markupStyle,
            $markupStyleP->getValue($panel),
            'Markup Style does not match after set'
        );
    }

    /**
     * @covers IDX_Panel::getTitle()
     * @depends testConstruct
     * @param $panel Mockery\MockInterface|IDX_Panel
     */
    public function testGetTitle($panel)
    {
        $title = 'title';

        $titleP = new ReflectionProperty($panel, 'title');
        $titleP->setAccessible(true);
        $titleP->setValue($panel, $title);

        $this->assertEquals(
            $title,
            $panel->getTitle(),
            'Get Title does not match Title Property'
        );
    }

    /**
     * @covers IDX_Panel::getInputs
     * @depends testConstruct
     * @param $panel Mockery\MockInterface|IDX_Panel
     */
    public function testGetInputs($panel)
    {
        $inputName = 'inputName';

        $inputNameP = new ReflectionProperty($panel, 'inputName');
        $inputNameP->setAccessible(true);
        $inputNameP->setValue($panel, $inputName);

        $this->assertEquals(
            [$inputName],
            $panel->getInputs(),
            'Get Inputs does not match array of inputName'
        );
    }

    /**
     * @covers IDX_Panel::isAvailable()
     * @dataProvider availableProvider
     * @param $expectedResult bool
     * @param $field string|null
     * @param $blocked bool
     * @param $checkFieldResult array|false
     */
    public function testIsAvailable($expectedResult, $field, $blocked, $checkFieldResult)
    {
        $this->mockCommonIDXDB();
        $this->mockCommonConstructorMethods();

        // Mock Panel class
        $panel = $this->getMockBuilder('IDX_Panel')
            ->setMockClassName('MockPanel')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $fieldP = new ReflectionProperty($panel, 'field');
        $fieldP->setAccessible(true);
        $fieldP->setValue($panel, $field);

        $this->idx->shouldReceive('field')->andReturn($field);
        $this->db->shouldReceive('fetchQuery')
            ->andReturn($checkFieldResult);

        $panel->setBlocked($blocked);

        $this->assertEquals($expectedResult, $panel->isAvailable());
    }

    /**
     * @covers \IDX_Panel::formatOptions()
     */
    public function testFormatOptions()
    {
        $expectedOptionItems = [
            ['value' => 'value1', 'title' => 'Value1'],
            ['value' => 'value2', 'title' => 'Value2'],
            ['value' => 'value1, value2', 'title' => 'Value1, Value2'],
        ];
        $formattedOptionItems = [
            ['value' => 'value1', 'title' => 'value1'],
            ['value' => 'value2', 'title' => 'value2'],
            ['value' => 'value1, value2', 'title' => 'value1, value2'],
        ];
        $formatOptions = function ($options) {
            $options['title'] = strtolower($options['title']);
            return $options;
        };

        // Mock Panel class
        $panel = $this->getMockBuilder('IDX_Panel')
            ->setMockClassName('MockPanel')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        // Test with no formatOptions set in constructor
        $options = $panel->formatOptions($expectedOptionItems);
        $this->assertInternalType(
            'array',
            $options,
            "Format Options didn't return an options array"
        );

        // Mock Panel class
        $panel = $this->getMockBuilder('IDX_Panel')
            ->setMockClassName('MockPanel')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $formatP = new ReflectionProperty($panel, 'formatOptions');
        $formatP->setAccessible(true);
        $formatP->setValue($panel, $formatOptions);

        // Test with formatOptions set in Panel object
        $options = $panel->formatOptions($expectedOptionItems);
        $this->assertInternalType(
            'array',
            $options,
            "Format Options didn't return an options array with default set"
        );
        $this->assertEquals(
            $formattedOptionItems,
            $options,
            'Class did not use formatOptions closure from constructor'
        );
    }

    /**
     * @covers IDX_Panel::isLocked()
     * @covers IDX_Panel::setLocked()
     * @depends testConstruct
     * @param $panel Mockery\MockInterface|IDX_Panel
     */
    public function testGetSetLocked($panel)
    {
        // Test get after set
        foreach ([true, false] as $locked) {
            $panel->setLocked($locked);
            $this->assertEquals(
                $locked,
                $panel->isLocked(),
                'Get Locked does not match set Locked value'
            );
        }
    }

    /**
     * @covers IDX_Panel::isBlocked()
     * @covers IDX_Panel::setBlocked()
     * @depends testConstruct
     * @param $panel Mockery\MockInterface|IDX_Panel
     */
    public function testGetSetBlocked($panel)
    {
        // Test get after set
        foreach ([true, false] as $blocked) {
            $panel->setBlocked($blocked);
            $this->assertEquals(
                $blocked,
                $panel->isBlocked(),
                'Get Blocked does not match set Blocked value'
            );
        }
    }

    /**
     * @covers \IDX_Panel::defaults()
     *
     */
    public function testDefaults()
    {

        $this->mockCommonConstructorMethods();
        $expectedDefaults = $this->getExpectedDefaults();
        $this->hooks = Mockery::mock(HooksInterface::class);
        $this->collection = Mockery::mock(CollectionInterface::class);
        $this->container->shouldReceive('get')->with(HooksInterface::class)->andReturn($this->hooks);
        $this->hooks->shouldReceive('hook')->with(HooksInterface::HOOK_BACKEND_IDX_PANELS)->andReturn($this->collection);
        $this->collection->shouldReceive('run')->with($expectedDefaults)->andReturn($expectedDefaults);

        // Test that defaults returns an associative array
        $defaults = IDX_Panel::defaults();
        $this->assertInternalType('array', $defaults);

        foreach ($defaults as $id => $option) {
            $this->assertInternalType(
                'string',
                $id,
                "Default panel id is not a string"
            );
            $this->assertInternalType(
                'array',
                $option,
                "Panel default does not have array of options: " . $id
            );
        }
    }

    /**
     * @covers \IDX_Panel::getClass()
     * @dataProvider defaultsProvider
     */
    public function testGetClass($id)
    {
        $this->mockCommonConstructorMethods();

        // Make sure class returns a string
        $classString = IDX_Panel::getClass($id);
        $this->assertInternalType(
            'string',
            $classString,
            'Panel defined in defaults does not return a class string: ' . $id
        );
    }

    /**
     * @covers \IDX_Panel::getClass()
     */
    public function testInvalidGetClass()
    {
        $this->mockCommonConstructorMethods();

        // Make sure incorrect ID returns null
        $classString = IDX_Panel::getClass(null);
        $this->assertNull(
            $classString,
            'Incorrect panel id does not return Null'
        );
    }

    /**
     * @covers \IDX_Panel::get()
     * @dataProvider defaultsProvider
     */
    public function testGet($id, $options)
    {
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
        $this->mockCommonConstructorMethods();

        // Test get with no options defined
        $class = IDX_Panel::get($id);
        $this->assertNotNull($class, 'Panel defined does not exist: ' . $id);

        // Test with all options but panelClass defined
        $class = IDX_Panel::get($id, $options);
        $this->assertNotNull($class, 'Panel defined does not accept populated options array: ' . $id);
    }

    /**
     * @covers \IDX_Panel::get()
     */
    public function testInvalidGet()
    {
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

        $this->mockCommonConstructorMethods();

        // Make sure null id returns null
        $class = IDX_Panel::get(null);
        $this->assertNull($class, 'Null Panel does not return null.');
    }

    /**
     * Setup common mock methods used by Panel Constructor.
     */
    public function mockCommonConstructorMethods()
    {
        // Mock methods
        $this->container->shouldReceive('getInstance')->andReturn($this->container);
        $this->settings->IDX_FEED = self::IDX_FEED;
        $this->container->shouldReceive('get')->with(SettingsInterface::class)->andReturn($this->settings);
        $this->container->shouldReceive('get')->with(IDXFactoryInterface::class)->andReturn($this->idxfactory);
    }

    /**
     * Setup common mock methods used for IDX database.
     */
    public function mockCommonIDXDB()
    {
        // Mock Cache Class
        $this->cache = Mockery::mock('alias:Cache');
        $this->cache->shouldReceive('getCache')->andReturnNull();
        $this->cache->shouldReceive('setCache');

        // Mock IDX methods
        $this->idxfactory->shouldReceive('getIdx')->andReturn($this->idx);
        $this->idxfactory->shouldReceive('getDatabase')->andReturn($this->db);
        $this->idx->shouldReceive('getTable')->andReturn('_rewidx_listings');
        $this->idx->shouldReceive('getName')->andReturn('MFR');
        $this->db->shouldReceive('db')->andReturn('rewidx_mfr');
    }

    /**
     * data provider for defaults tests
     * @return array
     */
    public function defaultsProvider()
    {

        $expectedDefaults = $this->getExpectedDefaults();
        $this->hooks = Mockery::mock(HooksInterface::class);
        $this->settings = Mockery::mock(SettingsInterface::class);
        $this->collection = Mockery::mock(CollectionInterface::class);
        $this->container = Mockery::mock('alias:Container');
        $this->container->shouldReceive('getInstance')->andReturn($this->container);
        $this->container->shouldReceive('get')->with(SettingsInterface::class)->andReturn($this->settings);
        $this->container->shouldReceive('get')->with(HooksInterface::class)->andReturn($this->hooks);
        $this->hooks->shouldReceive('hook')->with(HooksInterface::HOOK_BACKEND_IDX_PANELS)->andReturn($this->collection);
        $this->collection->shouldReceive('run')->with($expectedDefaults)->andReturn($expectedDefaults);


        $defaults = IDX_Panel::defaults();
        $defaultsData = [];
        foreach ($defaults as $id => $option) {
            $defaultsData[$id] = [$id, $option];
        }
        return $defaultsData;
    }

    /**
     * Provides parameters and expected results for getOptions
     * @return array
     */
    public function optionsProvider()
    {
        return [
            'Options set' => [
                [
                    'field' => 'Field',
                    'where' => 'Where',
                    'order' => 'Order',
                    'options' => [['value' => 'value1', 'title' => 'value1']],
                ],
                [['value' => 'value1', 'title' => 'value1']],
            ],
            'No Options set' => [
                [
                    'field' => 'Field',
                    'where' => 'Where',
                    'order' => 'Order',
                    'options' => null,
                ],
                [
                    ['value' => 'value1', 'title' => 'Value1'],
                    ['value' => 'value2', 'title' => 'Value2'],
                ]
            ],
            'No Options or Field set' => [
                [
                    'field' => null,
                    'where' => 'Where',
                    'order' => 'Order',
                    'options' => null,
                ],
                []
            ]
        ];
    }

    /**
     * Provider for isAvailable
     * @return array
     */
    public function availableProvider()
    {
        return [
            'field not set' => [
                true,
                null,
                false,
                ['field' => 'result']
            ],
            'field set & blocked' => [
                false,
                'field',
                true,
                ['field' => 'result']
            ],
            'field set & not blocked & check field result' => [
                true,
                'field',
                false,
                ['field' => 'result']
            ],
            'field set & not blocked & no check field result' => [
                false,
                'field',
                false,
                false
            ],
        ];
    }

    protected function getExpectedDefaults () {
        return array(
            'polygon' => array(
                'hidden' => true
            ),
            'radius' => array(
                'hidden' => true
            ),
            'bounds' => array(
                'hidden' => true
            ),
            'location' => array(
                'display' => true
            ),
            'city' => array(
                'display' => true
            ),
            'subdivision' => array(
                'display' => true
            ),
            'zip' => array(
                'display' => true
            ),
            'area' => array(
                'display' => false
            ),
            'county' => array(
                'display' => false
            ),
            'mls' => array(
                'display' => true
            ),
            'address' => array(
                'display' => false
            ),
            'type' => array(
                'display' => true
            ),
            'subtype' => array(
                'display' => true
            ),
            'status' => array(
                'display' => false
            ),
            'price' => array(
                'display' => true
            ),
            'reduced_price' => array(
                'display' => false
            ),
            'rooms' => array(
                'display' => true
            ),
            'bedrooms' => array(
                'display' => false
            ),
            'bathrooms' => array(
                'display' => false
            ),
            'sqft' => array(
                'display' => true
            ),
            'acres' => array(
                'display' => true
            ),
            'year' => array(
                'display' => true
            ),
            'school_elementary' => array(
                'display' => false
            ),
            'school_middle' => array(
                'display' => false
            ),
            'school_high' => array(
                'display' => false
            ),
            'school_district' => array(
                'display' => false
            ),
            'dom' => array(
                'display' => true
            ),
            'dow' => array(
                'display' => true
            ),
            'age' => array(
                'display' => false
            ),
            'waterfront' => array(
                'display' => false
            ),
            'foreclosure' => array(
                'display' => false
            ),
            'shortsales' => array(
                'display' => false
            ),
            'bankowned' => array(
                'display' => false
            ),
            'features' => array(
                'display' => true
            ),
            'office' => array(
                'display' => false
            ),
            'office_id' => array(
                'display' => false
            ),
            'agent' => array(
                'display' => false
            ),
            'agent_id' => array(
                'display' => false
            ),
            'has_open_house' => array(
                'display' => false
            ),
        );
    }
}
