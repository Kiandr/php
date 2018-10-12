<?php
namespace REW\Test\Backend\Leads;

use Mockery as m;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\FormatInterface;
use REW\Backend\Leads\CustomField\CustomString;

class CustomStringTest extends \Codeception\Test\Unit
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
     * @covers \REW\Backend\Leads\CustomField\CustomString::__construct()
     * @covers \REW\Backend\Leads\CustomField::__construct()
     */
    public function testContruct()
    {
        $customString = new CustomString($this->db, $this->format, 5, 'grandchilds-name', 'Grandchilds Name', 1);
        $this->assertInstanceOf('\REW\Backend\Leads\CustomField\CustomString', $customString);
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomString::getId()
     * @covers \REW\Backend\Leads\CustomField::getId()
     */
    public function testGetId()
    {
        $customString = new CustomString($this->db, $this->format, 5, 'grandchilds-name', 'Grandchilds Name', 1);
        $this->assertEquals(5, $customString->getId());
    }

    /**
     * @covers \REW\Backend\Leads\CustomField::getType()
     * @covers \REW\Backend\Leads\CustomField\CustomString::getType()
     */
    public function testGetType()
    {
        $customString = new CustomString($this->db, $this->format, 3, 'grandchilds-name', 'Grandchilds Name', 1);
        $this->assertEquals('text', $customString->getType());
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomString::getName()
     * @covers \REW\Backend\Leads\CustomField::getName()
     */
    public function testGetName()
    {
        $customString = new CustomString($this->db, $this->format, 3, 'grandchilds-name', 'Grandchilds Name', 1);
        $this->assertEquals('cst_fld_grandchilds-name', $customString->getName());
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomString::getTitle()
     * @covers \REW\Backend\Leads\CustomField::getTitle()
     */
    public function testGetTitle()
    {
        $customString = new CustomString($this->db, $this->format, 3, 'grandchilds-name', 'Grandchilds Name', 1);
        $this->assertEquals('Grandchilds Name', $customString->getTitle());
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomString::isEnabled()
     * @covers \REW\Backend\Leads\CustomField::isEnabled()
     */
    public function testIsEnabled()
    {
        $customString = new CustomString($this->db, $this->format, 3, 'grandchilds-name', 'Grandchilds Name', 1);
        $this->assertEquals(1, $customString->isEnabled());
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomString::getTable()
     */
    public function testGetTable()
    {
        $customString = new CustomString($this->db, $this->format, 3, 'grandchilds-name', 'Grandchilds Name', 1);
        $this->assertEquals('users_field_strings', $customString->getTable());
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomString::loadValue()
     * @covers \REW\Backend\Leads\CustomField::loadValue()
     */
    public function testLoadValue()
    {

        $field_id = 5;
        $lead_id = 5;

        $customString = new CustomString($this->db, $this->format, $field_id, 'grandchilds-name', 'Grandchilds Name', 1);

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')->once()->with(['user_id' => $lead_id, 'field_id' => $field_id]);
        $stmt->shouldReceive('fetchColumn')->once()
            ->andReturn('Albert');

        $this->db->shouldReceive('prepare')
            ->once()
            ->with("SELECT `value` FROM `users_field_strings` WHERE `user_id` = :user_id AND `field_id` = :field_id")
            ->andReturn($stmt);

            $this->assertEquals('Albert', $customString->loadValue($lead_id));
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomString::saveValue()
     * @covers \REW\Backend\Leads\CustomField::saveValue()
     */
    public function testSaveValue()
    {

        $field_id = 3;
        $lead_id = 5;
        $value = 'Simon Belmont';

        $customString = new CustomString($this->db, $this->format, $field_id, 'grandchilds-name', 'Grandchilds Name', 1);

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')->once()->with(['user_id' => $lead_id, 'field_id' => $field_id, 'value' => $value]);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with('REPLACE INTO `users_field_strings` SET `user_id` = :user_id, `field_id` = :field_id, `value` = :value;')
            ->andReturn($stmt);

        $this->assertTrue($customString->saveValue($lead_id, $value));
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomString::validateValue()
     * @covers \REW\Backend\Leads\CustomField::validateValue()
     */
    public function testValidateValue()
    {
        $customString = new CustomString($this->db, $this->format, 5, 'grandchilds-name', 'Grandchilds Name', 1);
        $this->assertTrue($customString->validateValue('Albert'));
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomString::validateValue()
     * @covers \REW\Backend\Leads\CustomField::validateValue()
     */
    public function testFailedValidateValue()
    {
        $customString = new CustomString($this->db, $this->format, 5, 'grandchilds-name', 'Grandchilds Name', 1);
        $this->expectException(\InvalidArgumentException::class);
        $customString->validateValue('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomString::parseValue()
     * @covers \REW\Backend\Leads\CustomField::parseValue()
     */
    public function testParseValue()
    {
        $customString = new CustomString($this->db, $this->format, 5, 'grandchilds-name', 'Grandchilds Name', 1);
        $this->assertStringStartsWith('Albert', $customString->parseValue('Albert'));
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomString::getSearchWhere()
     * @covers \REW\Backend\Leads\CustomField::getSearchWhere()
     */
    public function testGetSearchWhere()
    {

        $this->db->shouldReceive('quote')
            ->once()
            ->with('%Jack%')
            ->andReturn('\'%Jack%\'');

        $customString = new CustomString($this->db, $this->format, 5, 'grandchilds-name', 'Grandchilds Name', 1);
        $this->assertEquals('`ufs_cst_fld_grandchilds-name`.`value` LIKE \'%Jack%\'', $customString->getSearchWhere([$customString->getName() => 'Jack']));
        $this->assertEquals('', $customString->getSearchWhere([]));
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomString::getSearchJoin()
     * @covers \REW\Backend\Leads\CustomField::getSearchJoin()
     */
    public function testGetSearchJoin()
    {
        $customString = new CustomString($this->db, $this->format, 5, 'grandchilds-name', 'Grandchilds Name', 1);
        $this->assertEquals(" LEFT JOIN `users_field_strings` `ufs_cst_fld_grandchilds-name` ON (`u`.`id` = `ufs_cst_fld_grandchilds-name`.`user_id` AND `ufs_cst_fld_grandchilds-name`.`field_id` = 5)", $customString->getSearchJoin([$customString->getName()=> 'Jack'], 'u'));
        $this->assertEquals('', $customString->getSearchJoin([], 'u'));
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomString::getSearchString()
     * @covers \REW\Backend\Leads\CustomField::getSearchString()
     */
    public function testGetSearchString()
    {

        $this->format->shouldReceive('htmlspecialchars')
            ->once()
            ->with('Grandchilds Name')
            ->andReturn('Grandchilds Name');

        $this->format->shouldReceive('htmlspecialchars')
            ->once()
            ->with('Jack')
            ->andReturn('Jack');

        $customString = new CustomString($this->db, $this->format, 5, 'grandchilds-name', 'Grandchilds Name', 1);
        $this->assertEquals('<strong>Grandchilds Name:</strong> Jack', $customString->getSearchString([$customString->getName() => 'Jack']));
        $this->assertEquals('', $customString->getSearchString([]));
    }
}
