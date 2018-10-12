<?php
namespace REW\Test\Backend\Controllers\Settings\Fields;

use Mockery as m;
use REW\Backend\Auth\CustomAuth;
use REW\Backend\Interfaces\NoticesCollectionInterface;
use REW\Backend\View\Interfaces\FactoryInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\FormatInterface;
use REW\Core\Interfaces\LogInterface;
use REW\Backend\Leads\Interfaces\CustomFieldFactoryInterface;
use REW\Backend\Controller\Settings\Fields\AddController;
use REW\Backend\Exceptions\MissingParameterException;

class AddControllerTest extends \Codeception\Test\Unit
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
     * @covers \REW\Backend\Controller\Settings\Fields\AddController::__construct()
     */
    public function testContruct()
    {
        $addController = new AddController($this->customAuth, $this->notices, $this->view, $this->auth, $this->db, $this->format, $this->log, $this->customFieldFactory);
        $this->assertInstanceOf('REW\Backend\Controller\Settings\Fields\AddController', $addController);
    }

    /**
     * @covers \REW\Backend\Controller\Settings\Fields\AddController::checkRequiredFields()
     */
    public function testGetFieldData()
    {

        // Build Valid Data Set
        $completeData = ['title' => 'Birthday','type' => 'Date'];

        // Check for no exception
        $addController = new AddController($this->customAuth, $this->notices, $this->view, $this->auth, $this->db, $this->format, $this->log, $this->customFieldFactory);
        $addController->checkRequiredFields($completeData);
    }

    /**
     * @covers \REW\Backend\Controller\Settings\Fields\AddController::checkRequiredFields()
     */
    public function testCheckRequiredFields()
    {

        // Build Valid Data Set
        $completeData = ['title' => 'Birthday','type' => 'Date'];

        // Check for no exception
        $addController = new AddController($this->customAuth, $this->notices, $this->view, $this->auth, $this->db, $this->format, $this->log, $this->customFieldFactory);
        $addController->checkRequiredFields($completeData);
    }

    /**
     * @covers \REW\Backend\Controller\Settings\Fields\AddController::checkRequiredFields()
     */
    public function testCheckRequiredFieldsMissingParameters()
    {

        // Build Invalid Data Set
        $noTitle = ['type' => 'Date'];
        $noType = ['title' => 'Birthday'];

        // Check for no exception
        $addController = new AddController($this->customAuth, $this->notices, $this->view, $this->auth, $this->db, $this->format, $this->log, $this->customFieldFactory);

        $this->expectException(MissingParameterException::class);
        $addController->checkRequiredFields($noTitle);

        $this->expectException(MissingParameterException::class);
        $addController->checkRequiredFields($noType);
    }

    /**
     * @covers \REW\Backend\Controller\Settings\Fields\AddController::checkTypeField()
     */
    public function testCheckTypeField()
    {

        $this->customFieldFactory->shouldReceive('getTypes')
            ->times(3)
            ->andReturn([
                'string' => 'Text',
                'number' => 'Number',
                'date' => 'Date'
            ]);

        // Check for no exception
        $addController = new AddController($this->customAuth, $this->notices, $this->view, $this->auth, $this->db, $this->format, $this->log, $this->customFieldFactory);
        $addController->checkTypeField('date');
        $addController->checkTypeField('string');
        $addController->checkTypeField('number');
    }

    /**
     * @covers \REW\Backend\Controller\Settings\Fields\AddController::checkTypeField()
     */
    public function testCheckTypeFieldWithInvalidType()
    {

        $this->customFieldFactory->shouldReceive('getTypes')
            ->once()
            ->andReturn([
                'string' => 'Text',
                'number' => 'Number',
                'date' => 'Date'
            ]);

        // Check for no exception
        $addController = new AddController($this->customAuth, $this->notices, $this->view, $this->auth, $this->db, $this->format, $this->log, $this->customFieldFactory);
        $this->expectException(\InvalidArgumentException::class);
        $addController->checkTypeField('notatype');
    }

    /**
     * @covers \REW\Backend\Controller\Settings\Fields\AddController::checkDuplicateName()
     */
    public function testCheckDuplicateName()
    {

        $this->customFieldFactory->shouldReceive('getTable')
            ->once()
            ->andReturn('users_fields');

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')->once()->with(['name' => 'birthday']);
        $stmt->shouldReceive('rowCount')->once()
            ->andReturn(0);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with("SELECT `id` FROM `users_fields` WHERE `name` = :name")
            ->andReturn($stmt);

        // Check for no exception
        $addController = new AddController($this->customAuth, $this->notices, $this->view, $this->auth, $this->db, $this->format, $this->log, $this->customFieldFactory);
        $addController->checkDuplicateName('birthday');
    }

    /**
     * @covers \REW\Backend\Controller\Settings\Fields\AddController::checkDuplicateName()
     */
    public function testCheckDuplicateNameFailure()
    {

        $this->customFieldFactory->shouldReceive('getTable')
            ->once()
            ->andReturn('users_fields');

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')->once()->with(['name' => 'birthday']);
        $stmt->shouldReceive('rowCount')->once()
            ->andReturn(1);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with("SELECT `id` FROM `users_fields` WHERE `name` = :name")
            ->andReturn($stmt);

        // Check for no exception
        $addController = new AddController($this->customAuth, $this->notices, $this->view, $this->auth, $this->db, $this->format, $this->log, $this->customFieldFactory);
        $this->expectException(\InvalidArgumentException::class);
        $addController->checkDuplicateName('birthday');
    }

    /**
     * @covers \REW\Backend\Controller\Settings\Fields\AddController::saveFieldData()
     */
    public function testSaveFieldData()
    {

        $this->customFieldFactory->shouldReceive('getTable')
            ->once()
            ->andReturn('users_fields');

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')->once()->with([
            'name'  => 'valid-field',
            'title' => 'Valid Field',
            'type'  => 'string',
        ]);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with("INSERT INTO `users_fields` SET `name`    = :name, `title`   = :title, `type`    = :type, `enabled` = 1")
            ->andReturn($stmt);

        // Check for no exception
        $addController = new AddController($this->customAuth, $this->notices, $this->view, $this->auth, $this->db, $this->format, $this->log, $this->customFieldFactory);
        $addController->saveFieldData(['name' => 'valid-field', 'title' => 'Valid Field', 'type' => 'string']);
    }
}
