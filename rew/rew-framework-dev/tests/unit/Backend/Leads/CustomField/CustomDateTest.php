<?php
namespace REW\Test\Backend\Leads;

use Mockery as m;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\FormatInterface;
use REW\Backend\Leads\CustomField\CustomDate;

class CustomDateTest extends \Codeception\Test\Unit
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
     * @covers \REW\Backend\Leads\CustomField\CustomDate::__construct()
     * @covers \REW\Backend\Leads\CustomField::__construct()
     */
    public function testContruct()
    {
        $customDate = new CustomDate($this->db, $this->format, 3, 'birthday', 'Birthday', 1);
        $this->assertInstanceOf('\REW\Backend\Leads\CustomField\CustomDate', $customDate);
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomDate::getId()
     * @covers \REW\Backend\Leads\CustomField::getId()
     */
    public function testGetId()
    {
        $customDate = new CustomDate($this->db, $this->format, 3, 'birthday', 'Birthday', 1);
        $this->assertEquals(3, $customDate->getId());
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomDate::getType()
     * @covers \REW\Backend\Leads\CustomField::getType()
     */
    public function testGetType()
    {
        $customDate = new CustomDate($this->db, $this->format, 3, 'birthday', 'Birthday', 1);
        $this->assertEquals('date', $customDate->getType());
    }

    /**
     * @covers \REW\Backend\Leads\CustomField::getName()
     */
    public function testGetName()
    {
        $customDate = new CustomDate($this->db, $this->format, 3, 'birthday', 'Birthday', 1);
        $this->assertEquals('cst_fld_birthday', $customDate->getName());
    }

    /**
     * @covers \REW\Backend\Leads\CustomField::getTitle()
     */
    public function testGetTitle()
    {
        $customDate = new CustomDate($this->db, $this->format, 3, 'birthday', 'Birthday', 1);
        $this->assertEquals('Birthday', $customDate->getTitle());
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomDate::isEnabled()
     * @covers \REW\Backend\Leads\CustomField::isEnabled()
     */
    public function testIsEnabled()
    {
        $customDate = new CustomDate($this->db, $this->format, 3, 'birthday', 'Birthday', 1);
        $this->assertEquals(1, $customDate->isEnabled());
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomDate::getTable()
     */
    public function testGetTable()
    {
        $customDate = new CustomDate($this->db, $this->format, 3, 'birthday', 'Birthday', 1);
        $this->assertEquals('users_field_dates', $customDate->getTable());
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomDate::loadValue()
     * @covers \REW\Backend\Leads\CustomField::loadValue()
     */
    public function testLoadValue()
    {

        $field_id = 3;
        $lead_id = 5;

        $customDate = new CustomDate($this->db, $this->format, $field_id, 'birthday', 'Birthday', 1);

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')->once()->with(['user_id' => $lead_id, 'field_id' => $field_id]);
        $stmt->shouldReceive('fetchColumn')->once()
            ->andReturn('1997-02-11');

        $this->db->shouldReceive('prepare')
            ->once()
            ->with("SELECT `value` FROM `users_field_dates` WHERE `user_id` = :user_id AND `field_id` = :field_id")
            ->andReturn($stmt);

        $this->assertEquals('1997-02-11', $customDate->loadValue($lead_id));
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomDate::saveValue()
     * @covers \REW\Backend\Leads\CustomField::saveValue()
     */
    public function testSaveValue()
    {

        $field_id = 3;
        $lead_id = 5;
        $value = '1997-02-11';

        $customDate = new CustomDate($this->db, $this->format, $field_id, 'birthday', 'Birthday', 1);

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')->once()->with(['user_id' => $lead_id, 'field_id' => $field_id, 'value' => $value]);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with('REPLACE INTO `users_field_dates` SET `user_id` = :user_id, `field_id` = :field_id, `value` = :value;')
            ->andReturn($stmt);

        $this->assertTrue($customDate->saveValue($lead_id, $value));
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomDate::validateValue()
     * @covers \REW\Backend\Leads\CustomField::validateValue()
     */
    public function testValidateValue()
    {
        $customDate = new CustomDate($this->db, $this->format, 3, 'birthday', 'Birthday', 1);
        $this->assertTrue($customDate->validateValue('1997-02-11'));
        $this->assertTrue($customDate->validateValue('1997-02-11 11:11:11'));
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomDate::validateValue()
     * @covers \REW\Backend\Leads\CustomField::validateValue()
     */
    public function testFailedValidateValue()
    {
        $customDate = new CustomDate($this->db, $this->format, 3, 'birthday', 'Birthday', 1);
        $this->expectException(\InvalidArgumentException::class);
        $customDate->validateValue('notavalue');
        $this->expectException(\InvalidArgumentException::class);
        $customDate->validateValue('1997-72-53');
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomDate::parseValue()
     * @covers \REW\Backend\Leads\CustomField::parseValue()
     */
    public function testParseValue()
    {
        $customDate = new CustomDate($this->db, $this->format, 3, 'birthday', 'Birthday', 1);
        $this->assertStringStartsWith('1997-02-11', $customDate->parseValue('1997-02-11'));
        $this->assertSame('1997-02-11 11:11:11', $customDate->parseValue('1997-02-11 11:11:11'));
    }


    /**
     * @covers \REW\Backend\Leads\CustomField\CustomDate::getSearchWhere()
     * @covers \REW\Backend\Leads\CustomField::getSearchWhere()
     */
    public function testGetSearchWhere()
    {

        $this->db->shouldReceive('quote')
            ->with('1993-01-11 00:00:00')
            ->andReturn('\'1993-01-11 00:00:00\'');

        $this->db->shouldReceive('quote')
            ->with('1993-11-12 23:59:25')
            ->andReturn('\'1993-11-12 23:59:25\'');

        $customDate = new CustomDate($this->db, $this->format, 3, 'birthday', 'Birthday', 1);
        $this->assertEquals("`ufs_cst_fld_birthday`.`value` >= '1993-01-11 00:00:00' AND `ufs_cst_fld_birthday`.`value` <= '1993-11-12 23:59:25'", $customDate->getSearchWhere([$customDate->getName() . '_start' => '1993-01-11', $customDate->getName() . '_end' => '1993-11-12']));
        $this->assertEquals("`ufs_cst_fld_birthday`.`value` >= '1993-01-11 00:00:00'", $customDate->getSearchWhere([$customDate->getName() . '_start' => '1993-01-11']));
        $this->assertEquals("`ufs_cst_fld_birthday`.`value` <= '1993-11-12 23:59:25'", $customDate->getSearchWhere([$customDate->getName() . '_end' => '1993-11-12']));
        $this->assertEquals('', $customDate->getSearchWhere([]));
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomDate::getSearchJoin()
     * @covers \REW\Backend\Leads\CustomField::getSearchJoin()
     */
    public function testGetSearchJoin()
    {
        $customDate = new CustomDate($this->db, $this->format, 3, 'birthday', 'Birthday', 1);
        $this->assertEquals(" LEFT JOIN `users_field_dates` `ufs_cst_fld_birthday` ON (`u`.`id` = `ufs_cst_fld_birthday`.`user_id` AND `ufs_cst_fld_birthday`.`field_id` = 3)", $customDate->getSearchJoin([$customDate->getName() . '_start' => '1993-01-11', $customDate->getName() . '_end' => '1993-11-12'], 'u'));
        $this->assertEquals('', $customDate->getSearchJoin([], 'u'));
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomDate::getSearchString()
     * @covers \REW\Backend\Leads\CustomField::getSearchString()
     */
    public function testGetSearchString()
    {

        $this->format->shouldReceive('htmlspecialchars')
            ->with('Birthday')
            ->andReturn('Birthday');

        $this->format->shouldReceive('htmlspecialchars')
            ->with('1993-01-11')
            ->andReturn('\'1993-01-11\'');

        $this->format->shouldReceive('htmlspecialchars')
            ->with('1993-11-12')
            ->andReturn('\'1993-11-12\'');

        $customDate = new CustomDate($this->db, $this->format, 3, 'birthday', 'Birthday', 1);

        $this->assertEquals('<strong>Birthday Between:</strong> January 11, 1993 and November 12, 1993', $customDate->getSearchString([$customDate->getName() . '_start' => '1993-01-11', $customDate->getName() . '_end' => '1993-11-12']));
        $this->assertEquals('<strong>Birthday After:</strong> January 11, 1993', $customDate->getSearchString([$customDate->getName() . '_start' => '1993-01-11']));
        $this->assertEquals('<strong>Birthday Before:</strong> November 12, 1993', $customDate->getSearchString([$customDate->getName() . '_end' => '1993-11-12']));
        $this->assertEquals('', $customDate->getSearchWhere([]));
    }
}
