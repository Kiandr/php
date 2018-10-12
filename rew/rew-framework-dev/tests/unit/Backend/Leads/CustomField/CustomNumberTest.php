<?php
namespace REW\Test\Backend\Leads;

use Mockery as m;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\FormatInterface;
use REW\Backend\Leads\CustomField\CustomNumber;

class CustomNumberTest extends \Codeception\Test\Unit
{

    /**
     * @var m \MockInterface|DBInterface
     */
    protected $db;

    /**
     * @var m \MockInterface|FormatInterface
     */
    protected $format;

    protected function _before()
    {
        $this->db = m::mock(DBInterface::class);
        $this->format = m::mock(FormatInterface::class);
    }

    protected function _after()
    {
        m::close();
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomNumber::__construct()
     * @covers \REW\Backend\Leads\CustomField::__construct()
     */
    public function testContruct()
    {
        $customNumber = new CustomNumber($this->db, $this->format, 4, 'number-of-properties', 'Number Of Properties', 1);
        $this->assertInstanceOf('\REW\Backend\Leads\CustomField\CustomNumber', $customNumber);
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomNumber::getId()
     * @covers \REW\Backend\Leads\CustomField::getId()
     */
    public function testGetId()
    {
        $customNumber = new CustomNumber($this->db, $this->format, 4, 'number-of-properties', 'Number Of Properties', 1);
        $this->assertEquals(4, $customNumber->getId());
    }

    /**
     * @covers \REW\Backend\Leads\CustomField::getType()
     * @covers \REW\Backend\Leads\CustomField\CustomNumber::getType()
     */
    public function testGetType()
    {
        $customNumber = new CustomNumber($this->db, $this->format, 3, 'number-of-properties', 'Number Of Properties', 1);
        $this->assertEquals('number', $customNumber->getType());
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomNumber::getName()
     * @covers \REW\Backend\Leads\CustomField::getName()
     */
    public function testGetName()
    {
        $customNumber = new CustomNumber($this->db, $this->format, 3, 'number-of-properties', 'Number Of Properties', 1);
        $this->assertEquals('cst_fld_number-of-properties', $customNumber->getName());
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomNumber::getTitle()
     * @covers \REW\Backend\Leads\CustomField::getTitle()
     */
    public function testGetTitle()
    {
        $customNumber = new CustomNumber($this->db, $this->format, 3, 'number-of-properties', 'Number Of Properties', 1);
        $this->assertEquals('Number Of Properties', $customNumber->getTitle());
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomDate::isEnabled()
     * @covers \REW\Backend\Leads\CustomField::isEnabled()
     */
    public function testIsEnabled()
    {
        $customNumber = new CustomNumber($this->db, $this->format, 3, 'number-of-properties', 'Number Of Properties', 1);
        $this->assertEquals(1, $customNumber->isEnabled());
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomNumber::getTable()
     */
    public function testGetTable()
    {
        $customNumber = new CustomNumber($this->db, $this->format, 4, 'number-of-properties', 'Number Of Properties', 1);
        $this->assertEquals('users_field_numbers', $customNumber->getTable());
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomNumber::loadValue()
     * @covers \REW\Backend\Leads\CustomField::loadValue()
     */
    public function testLoadValue()
    {

        $field_id = 4;
        $lead_id = 5;

        $customNumber = new CustomNumber($this->db, $this->format, $field_id, 'number-of-properties', 'Number Of Properties', 1);

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')->once()->with(['user_id' => $lead_id, 'field_id' => $field_id]);
        $stmt->shouldReceive('fetchColumn')->once()
            ->andReturn(6);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with("SELECT `value` FROM `users_field_numbers` WHERE `user_id` = :user_id AND `field_id` = :field_id")
            ->andReturn($stmt);

        $this->assertEquals(6, $customNumber->loadValue($lead_id));
    }


    /**
     * @covers \REW\Backend\Leads\CustomField\CustomNumber::saveValue()
     * @covers \REW\Backend\Leads\CustomField::saveValue()
     */
    public function testSaveValue()
    {

        $field_id = 3;
        $lead_id = 5;
        $value = 17;

        $customNumber = new CustomNumber($this->db, $this->format, $field_id, 'number-of-properties', 'Number Of Properties', 1);

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')->once()->with(['user_id' => $lead_id, 'field_id' => $field_id, 'value' => $value]);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with('REPLACE INTO `users_field_numbers` SET `user_id` = :user_id, `field_id` = :field_id, `value` = :value;')
            ->andReturn($stmt);

        $this->assertTrue($customNumber->saveValue($lead_id, $value));
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomNumber::validateValue()
     * @covers \REW\Backend\Leads\CustomField::validateValue()
     */
    public function testValidateValue()
    {
        $customNumber = new CustomNumber($this->db, $this->format, 4, 'number-of-properties', 'Number Of Properties', 1);
        $this->assertTrue($customNumber->validateValue(6));
        $this->assertTrue($customNumber->validateValue(6.5));
        $this->assertTrue($customNumber->validateValue('6'));
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomNumber::validateValue()
     * @covers \REW\Backend\Leads\CustomField::validateValue()
     */
    public function testFailedValidateValue()
    {
        $customNumber = new CustomNumber($this->db, $this->format, 4, 'number-of-properties', 'Number Of Properties', 1);
        $this->expectException(\InvalidArgumentException::class);
        $customNumber->validateValue('notanumber');
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomNumber::parseValue()
     * @covers \REW\Backend\Leads\CustomField::parseValue()
     */
    public function testParseValue()
    {
        $customNumber = new CustomNumber($this->db, $this->format, 4, 'number-of-properties', 'Number Of Properties', 1);
        $this->assertEquals(6, $customNumber->parseValue(6));
        $this->assertEquals(7, $customNumber->parseValue(6.5));
        $this->assertEquals(6, $customNumber->parseValue('6'));
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomNumber::getSearchWhere()
     * @covers \REW\Backend\Leads\CustomField::getSearchWhere()
     */
    public function testGetSearchWhere()
    {

        $this->db->shouldReceive('quote')
            ->once()
            ->with('4')
            ->andReturn('\'4\'');

        $customNumber = new CustomNumber($this->db, $this->format, 4, 'number-of-properties', 'Number Of Properties', 1);
        $this->assertEquals('`ufs_cst_fld_number-of-properties`.`value` = \'4\'', $customNumber->getSearchWhere([$customNumber->getName() => 4]));
        $this->assertEquals('', $customNumber->getSearchWhere([]));
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomNumber::getSearchJoin()
     * @covers \REW\Backend\Leads\CustomField::getSearchJoin()
     */
    public function testGetSearchJoin()
    {
        $customNumber = new CustomNumber($this->db, $this->format, 4, 'number-of-properties', 'Number Of Properties', 1);
        $this->assertEquals(" LEFT JOIN `users_field_numbers` `ufs_cst_fld_number-of-properties` ON (`u`.`id` = `ufs_cst_fld_number-of-properties`.`user_id` AND `ufs_cst_fld_number-of-properties`.`field_id` = 4)", $customNumber->getSearchJoin([$customNumber->getName()=> 4], 'u'));
        $this->assertEquals('', $customNumber->getSearchJoin([], 'u'));
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomNumber::getSearchString()
     * @covers \REW\Backend\Leads\CustomField::getSearchString()
     */
    public function testGetSearchString()
    {

        $this->format->shouldReceive('htmlspecialchars')
            ->once()
            ->with('Number Of Properties')
            ->andReturn('Number Of Properties');

        $this->format->shouldReceive('htmlspecialchars')
            ->once()
            ->with('4')
            ->andReturn('4');

        $customNumber = new CustomNumber($this->db, $this->format, 4, 'number-of-properties', 'Number Of Properties', 1);
        $this->assertEquals('<strong>Number Of Properties:</strong> 4', $customNumber->getSearchString([$customNumber->getName() => 4]));
        $this->assertEquals('', $customNumber->getSearchString([]));
    }
}
