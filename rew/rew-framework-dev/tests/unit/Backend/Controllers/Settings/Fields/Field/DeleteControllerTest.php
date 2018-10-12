<?php
namespace REW\Test\Backend\Controllers\Settings\Fields\Field;

use Mockery as m;
use REW\Backend\Auth\CustomAuth;
use REW\Backend\Interfaces\NoticesCollectionInterface;
use REW\Backend\View\Interfaces\FactoryInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\FormatInterface;
use REW\Core\Interfaces\LogInterface;
use REW\Backend\Leads\Interfaces\CustomFieldFactoryInterface;
use REW\Backend\Controller\Settings\Fields\Field\DeleteController;
use REW\Backend\Leads\CustomField\CustomString;

class DeleteControllerTest extends \Codeception\Test\Unit
{

    /**
     * @var m \MockInterface|DBInterface
     */
    protected $db;

    protected function _before()
    {
        $this->customAuth = m::mock(CustomAuth::class);
        $this->notices = m::mock(NoticesCollectionInterface::class);
        $this->view = m::mock(FactoryInterface::class);
        $this->auth = m::mock(AuthInterface::class);
        $this->db = m::mock(DBInterface::class);
        $this->format = m::mock(FormatInterface::class);
        $this->log = m::mock(LogInterface::class);
        $this->customFieldFactory = m::mock(CustomFieldFactoryInterface::class);
    }

    protected function _after()
    {
        m::close();
    }

    /**
     * @covers \REW\Backend\Controller\Settings\Fields\Field\DeleteController::__construct()
     */
    public function testContruct()
    {
        $deleteController = new DeleteController($this->customAuth, $this->notices, $this->view, $this->auth, $this->db, $this->format, $this->log, $this->customFieldFactory);
        $this->assertInstanceOf('REW\Backend\Controller\Settings\Fields\Field\DeleteController', $deleteController);
    }

    /**
     * @covers \REW\Backend\Controller\Settings\Fields\Field\DeleteController::deleteField()
     */
    public function testDeleteField()
    {

        $id = 7;

        $this->customFieldFactory->shouldReceive('getTable')
            ->once()
            ->andReturn('users_fields');

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')->once()->with(['id' => $id]);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with("DELETE FROM `users_fields` WHERE `id` = :id")
            ->andReturn($stmt);

        $exampleField = new CustomString($this->db, $this->format, $id, 'sample-field', 'Sample Field', 1);

        $deleteController = new DeleteController($this->customAuth, $this->notices, $this->view, $this->auth, $this->db, $this->format, $this->log, $this->customFieldFactory);
        $deleteController->deleteField($exampleField);
    }
}
