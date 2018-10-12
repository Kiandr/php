<?php
namespace REW\Test\Backend\Leads;

use Mockery as m;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\ContainerInterface;
use REW\Backend\Leads\CustomFieldFactory;
use REW\Backend\Leads\CustomField;

class CustomFieldFactoryTest extends \Codeception\Test\Unit
{

    /**
     * @var m\MockInterface|DBInterface
     */
    protected $db;

    /**
     * @var m\MockInterface|SettingsInterface
     */
    protected $settings;

    /**
     * @var m\MockInterface|ContainerInterface
     */
    protected $container;

    protected function _before()
    {
        $this->db = m::mock(DBInterface::class);
        $this->settings = m::mock(SettingsInterface::class);
        $this->container = m::mock(ContainerInterface::class);
    }

    protected function _after()
    {
        m::close();
    }


    /**
     * @covers \REW\Backend\Leads\CustomFieldFactory::__construct()
     */
    public function testContruct()
    {
        $factory = new CustomFieldFactory($this->settings, $this->db, $this->container);
        $this->assertInstanceOf('REW\Backend\Leads\CustomFieldFactory', $factory);
    }

    /**
     * @covers \REW\Backend\Leads\CustomFieldFactory::__construct()
     * @covers \REW\Backend\Leads\CustomFieldFactory::loadCustomField()
     */
    public function testLoadCustomField()
    {

        // The Id to Query
        $id = 2;

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')->once()->with(['id' => $id]);
        $stmt->shouldReceive('fetch')->once()
            ->andReturn(['name' => 'prefered-school-district', 'title' => 'Prefered School District', 'type' => 'string', 'enabled' => 1]);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with("SELECT `name`, `title`, `type`, `enabled` FROM `users_fields` WHERE `id` = :id")
            ->andReturn($stmt);

        $this->settings->shouldReceive('offsetGet')->once()->with('CUSTOM_FIELD_TYPES')
            ->andReturn(['string' => [
                    'title' => 'text',
                'class' => "\REW\Backend\Leads\CustomField\CustomString"
                ]
            ]);

        $this->container->shouldReceive('make')
            ->once()
            ->with(
                '\REW\Backend\Leads\CustomField\CustomString',
                ['id'=>2,'name'=>'prefered-school-district','title'=>'Prefered School District','enabled' => 1]
            )
            ->andReturn($customField = m::mock(CustomField::class));

        $factory = new CustomFieldFactory($this->settings, $this->db, $this->container);
        $this->assertEquals($customField, $factory->loadCustomField($id));
    }

    /**
     * @covers \REW\Backend\Leads\CustomFieldFactory::__construct()
     * @covers \REW\Backend\Leads\CustomFieldFactory::loadEnabledCustomFields()
     * @covers \REW\Backend\Leads\CustomFieldFactory::loadCustomFields()
     */
    public function testLoadEnabledCustomFields()
    {

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')->once();
        $stmt->shouldReceive('fetchAll')->once()
            ->andReturn([
                ['id' => 2, 'name' => 'prefered-school-district', 'title' => 'Prefered School District', 'type' => 'string', 'enabled' => 1],
                ['id' => 3, 'name' => 'number-of-properties', 'title' => 'Number Of Properties', 'type' => 'number', 'enabled' => 1],
                ['id' => 4, 'name' => 'birthday', 'title' => 'Birthday', 'type' => 'date', 'enabled' => 1]
            ]);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with("SELECT `id`, `name`, `title`, `type`, `enabled` FROM `users_fields` WHERE `enabled` = 1 ORDER BY `id`")
            ->andReturn($stmt);

        $this->settings->shouldReceive('offsetGet')->once()->with('CUSTOM_FIELD_TYPES')
            ->andReturn(['string' => [
                'title' => 'Text',
                'class' => "\REW\Backend\Leads\CustomField\CustomString"
            ],
            'number' => [
                'title' => 'Number',
                'class' => "\REW\Backend\Leads\CustomField\CustomNumber"
            ],
            'date' => [
                'title' => 'Date',
                'class' => "\REW\Backend\Leads\CustomField\CustomDate"
            ]
            ]);

        $this->container->shouldReceive('make')
            ->once()
            ->with(
                '\REW\Backend\Leads\CustomField\CustomString',
                ['id'=>2,'name'=>'prefered-school-district','title'=>'Prefered School District', 'enabled' => 1]
            )
            ->andReturn($customString = m::mock(CustomField::class));

        $this->container->shouldReceive('make')
            ->once()
            ->with(
                '\REW\Backend\Leads\CustomField\CustomNumber',
                ['id'=>3,'name'=>'number-of-properties','title'=>'Number Of Properties', 'enabled' => 1]
            )
            ->andReturn($customNumber = m::mock(CustomField::class));

        $this->container->shouldReceive('make')
            ->once()
            ->with(
                '\REW\Backend\Leads\CustomField\CustomDate',
                ['id'=>4,'name'=>'birthday','title'=>'Birthday', 'enabled' => 1]
            )
            ->andReturn($customDate = m::mock(CustomField::class));

        $factory = new CustomFieldFactory($this->settings, $this->db, $this->container);
        $this->assertEquals([$customString, $customNumber, $customDate], $factory->loadEnabledCustomFields());
    }

    /**
     * @covers \REW\Backend\Leads\CustomFieldFactory::__construct()
     * @covers \REW\Backend\Leads\CustomFieldFactory::loadDisabledCustomFields()
     * @covers \REW\Backend\Leads\CustomFieldFactory::loadCustomFields()
     */
    public function testLoadDisabledCustomFields()
    {

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')->once();
        $stmt->shouldReceive('fetchAll')->once()
        ->andReturn([
            ['id' => 2, 'name' => 'prefered-school-district', 'title' => 'Prefered School District', 'type' => 'string', 'enabled' => 0],
            ['id' => 3, 'name' => 'number-of-properties', 'title' => 'Number Of Properties', 'type' => 'number', 'enabled' => 0],
            ['id' => 4, 'name' => 'birthday', 'title' => 'Birthday', 'type' => 'date', 'enabled' => 0]
        ]);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with("SELECT `id`, `name`, `title`, `type`, `enabled` FROM `users_fields` WHERE `enabled` = 0 ORDER BY `id`")
            ->andReturn($stmt);

        $this->settings->shouldReceive('offsetGet')->once()->with('CUSTOM_FIELD_TYPES')
        ->andReturn(['string' => [
                'title' => 'Text',
                'class' => "\REW\Backend\Leads\CustomField\CustomString"
            ],
            'number' => [
                'title' => 'Number',
                'class' => "\REW\Backend\Leads\CustomField\CustomNumber"
            ],
            'date' => [
                'title' => 'Date',
                'class' => "\REW\Backend\Leads\CustomField\CustomDate"
            ]
        ]);

        $this->container->shouldReceive('make')
            ->once()
            ->with(
                '\REW\Backend\Leads\CustomField\CustomString',
                ['id'=>2,'name'=>'prefered-school-district','title'=>'Prefered School District', 'enabled' => 0]
            )
            ->andReturn($customString = m::mock(CustomField::class));

        $this->container->shouldReceive('make')
            ->once()
            ->with(
                '\REW\Backend\Leads\CustomField\CustomNumber',
                ['id'=>3,'name'=>'number-of-properties','title'=>'Number Of Properties', 'enabled' => 0]
            )
            ->andReturn($customNumber = m::mock(CustomField::class));

        $this->container->shouldReceive('make')
            ->once()
            ->with(
                '\REW\Backend\Leads\CustomField\CustomDate',
                ['id'=>4,'name'=>'birthday','title'=>'Birthday', 'enabled' => 0]
            )
            ->andReturn($customDate = m::mock(CustomField::class));

        $factory = new CustomFieldFactory($this->settings, $this->db, $this->container);
        $this->assertEquals([$customString, $customNumber, $customDate], $factory->loadDisabledCustomFields());
    }

    /**
     * @covers \REW\Backend\Leads\CustomFieldFactory::__construct()
     * @covers \REW\Backend\Leads\CustomFieldFactory::getTable()
     */
    public function testGetTable()
    {

        $factory = new CustomFieldFactory($this->settings, $this->db, $this->container);
        $this->assertEquals('users_fields', $factory->getTable());
    }

    /**
     * @covers \REW\Backend\Leads\CustomFieldFactory::__construct()
     * @covers \REW\Backend\Leads\CustomFieldFactory::getTypes()
     * @covers \REW\Backend\Leads\CustomFieldFactory::getCustomFieldTypes()
     */
    public function testGetTypes()
    {

        $this->settings->shouldReceive('offsetGet')->once()->with('CUSTOM_FIELD_TYPES')
            ->andReturn(['string' => [
                'title' => 'Text',
                'class' => "\REW\Backend\Leads\CustomField\CustomString"
            ],
            'number' => [
                'title' => 'Number',
                'class' => "\REW\Backend\Leads\CustomField\CustomNumber"
            ],
            'date' => [
                'title' => 'Date',
                'class' => "\REW\Backend\Leads\CustomField\CustomDate"
            ]
            ]);

        $factory = new CustomFieldFactory($this->settings, $this->db, $this->container);
        $this->assertEquals([
            'string' => 'Text',
            'number' => 'Number',
            'date' => 'Date'
        ], $factory->getTypes());
    }
}
