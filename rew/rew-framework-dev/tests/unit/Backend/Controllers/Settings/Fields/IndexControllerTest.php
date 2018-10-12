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
use REW\Backend\Controller\Settings\Fields\IndexController;
use REW\Backend\Leads\CustomField\CustomString;
use REW\Backend\Exceptions\UnauthorizedPageException;

class IndexControllerTest extends \Codeception\Test\Unit
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
     * @covers \REW\Backend\Controller\Settings\Fields\IndexController::__construct()
     */
    public function testContruct()
    {
        $indexController = new IndexController($this->customAuth, $this->notices, $this->view, $this->auth, $this->db, $this->format, $this->log, $this->customFieldFactory);
        $this->assertInstanceOf('REW\Backend\Controller\Settings\Fields\IndexController', $indexController);
    }

    /**
     * @covers \REW\Backend\Controller\Settings\Fields\IndexController::canManageFields()
     */
    public function testCanManageFields()
    {

        $this->customAuth->shouldReceive('canManageFields')
            ->once()
            ->andReturn(true);

        // Check for no exception
        $indexController = new IndexController($this->customAuth, $this->notices, $this->view, $this->auth, $this->db, $this->format, $this->log, $this->customFieldFactory);
        $indexController->canManageFields();
    }

    /**
     * @covers \REW\Backend\Controller\Settings\Fields\IndexController::canManageFields()
     */
    public function testCanManageFieldsFailed()
    {

        $this->customAuth->shouldReceive('canManageFields')
            ->once()
            ->andReturn(false);

        // Check for no exception
        $indexController = new IndexController($this->customAuth, $this->notices, $this->view, $this->auth, $this->db, $this->format, $this->log, $this->customFieldFactory);
        $this->expectException(UnauthorizedPageException::class);
        $indexController->canManageFields();
    }

    /**
     * @covers \REW\Backend\Controller\Settings\Fields\IndexController::getFilters()
     */
    public function testGetFilters()
    {

        // Build Valid Data Set
        $filters= ['all' => 'All Fields', 'enabled' => 'Enabled Fields', 'disabled' => 'Disabled Fields'];

        // Check for no exception
        $indexController = new IndexController($this->customAuth, $this->notices, $this->view, $this->auth, $this->db, $this->format, $this->log, $this->customFieldFactory);
        $this->assertEquals($filters, $indexController->getFilters());
    }

    /**
     * @covers \REW\Backend\Controller\Settings\Fields\IndexController::getAllFields()
     */
    public function testGetAllFields()
    {

        $exampleFields = [
            new CustomString($this->db, $this->format, 2, 'sample-field', 'Sample Field', 1),
            new CustomString($this->db, $this->format, 3, 'sample-field-two', 'Sample Field Two', 0),
            new CustomString($this->db, $this->format, 4, 'sample-field-three', 'Sample Field Three', 1)
        ];

        $this->customFieldFactory->shouldReceive('loadCustomFields')
            ->once()
            ->andReturn($exampleFields);

        $indexController = new IndexController($this->customAuth, $this->notices, $this->view, $this->auth, $this->db, $this->format, $this->log, $this->customFieldFactory);
        $this->assertEquals($exampleFields, $indexController->getAllFields());
    }

    /**
     * @covers \REW\Backend\Controller\Settings\Fields\IndexController::getEnabledFields()
     */
    public function testGetEnabledFields()
    {

        $enabledFields = [
            new CustomString($this->db, $this->format, 2, 'sample-field', 'Sample Field', 1),
            new CustomString($this->db, $this->format, 4, 'sample-field-three', 'Sample Field Three', 1)
        ];

        $this->customFieldFactory->shouldReceive('loadEnabledCustomFields')
            ->once()
            ->andReturn($enabledFields);

        $indexController = new IndexController($this->customAuth, $this->notices, $this->view, $this->auth, $this->db, $this->format, $this->log, $this->customFieldFactory);
        $this->assertEquals($enabledFields, $indexController->getEnabledFields());
    }

    /**
     * @covers \REW\Backend\Controller\Settings\Fields\IndexController::getDisabledFields()
     */
    public function testGetDisabledFields()
    {

        $disabledFields = [
            new CustomString($this->db, $this->format, 3, 'sample-field-two', 'Sample Field Two', 0)
        ];

        $this->customFieldFactory->shouldReceive('loadDisabledCustomFields')
            ->once()
            ->andReturn($disabledFields);

        $indexController = new IndexController($this->customAuth, $this->notices, $this->view, $this->auth, $this->db, $this->format, $this->log, $this->customFieldFactory);
        $this->assertEquals($disabledFields, $indexController->getDisabledFields());
    }

    /**
     * @covers \REW\Backend\Controller\Settings\Fields\IndexController::formatFields()
     */
    public function testFormatFields()
    {

        $exampleFields = [
            new CustomString($this->db, $this->format, 2, 'sample-field', 'Sample Field', 1),
            new CustomString($this->db, $this->format, 3, 'sample-field-two', 'Sample Field Two', 0),
            new CustomString($this->db, $this->format, 4, 'sample-field-three', 'Sample Field Three', 1)
        ];

        $formattedFields = [
            ['id' => 2, 'title' => 'Sample Field', 'type' => 'Text', 'enabled' => true],
            ['id' => 3, 'title' => 'Sample Field Two', 'type' => 'Text', 'enabled' => false],
            ['id' => 4, 'title' => 'Sample Field Three', 'type' => 'Text', 'enabled' => true]
        ];

        $this->format->shouldReceive('htmlspecialchars')->with(2)->andReturn(2);
        $this->format->shouldReceive('htmlspecialchars')->with('Sample Field')->andReturn('Sample Field');
        $this->format->shouldReceive('htmlspecialchars')->with('text')->andReturn('text');
        $this->format->shouldReceive('htmlspecialchars')->with(3)->andReturn(3);
        $this->format->shouldReceive('htmlspecialchars')->with('Sample Field Two')->andReturn('Sample Field Two');
        $this->format->shouldReceive('htmlspecialchars')->with('text')->andReturn('text');
        $this->format->shouldReceive('htmlspecialchars')->with(4)->andReturn(4);
        $this->format->shouldReceive('htmlspecialchars')->with('Sample Field Three')->andReturn('Sample Field Three');
        $this->format->shouldReceive('htmlspecialchars')->with('text')->andReturn('text');

        $indexController = new IndexController($this->customAuth, $this->notices, $this->view, $this->auth, $this->db, $this->format, $this->log, $this->customFieldFactory);
        $this->assertEquals($formattedFields, $indexController->formatFields($exampleFields));
    }
}
