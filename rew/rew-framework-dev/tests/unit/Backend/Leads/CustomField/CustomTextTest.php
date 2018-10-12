<?php
namespace REW\Test\Backend\Leads;

use Mockery as m;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\FormatInterface;
use REW\Backend\Leads\CustomField\CustomText;

class CustomTextTest extends \Codeception\Test\Unit
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
     * @covers \REW\Backend\Leads\CustomField\CustomText::__construct()
     * @covers \REW\Backend\Leads\CustomField::__construct()
     */
    public function testContruct()
    {
        $customText = new CustomText($this->db, $this->format, 5, 'wishlist', 'Wishlist', 1);
        $this->assertInstanceOf('\REW\Backend\Leads\CustomField\CustomText', $customText);
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomText::getId()
     * @covers \REW\Backend\Leads\CustomField::getId()
     */
    public function testGetId()
    {
        $customText= new CustomText($this->db, $this->format, 5, 'wishlist', 'Wishlist', 1);
        $this->assertEquals(5, $customText->getId());
    }

    /**
     * @covers \REW\Backend\Leads\CustomField::getType()
     * @covers \REW\Backend\Leads\CustomField\CustomText::getType()
     */
    public function testGetType()
    {
        $customText= new CustomText($this->db, $this->format, 5, 'wishlist', 'Wishlist', 1);
        $this->assertEquals('text field', $customText->getType());
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomText::getName()
     * @covers \REW\Backend\Leads\CustomField::getName()
     */
    public function testGetName()
    {
        $customText= new CustomText($this->db, $this->format, 5, 'wishlist', 'Wishlist', 1);
        $this->assertEquals('cst_fld_wishlist', $customText->getName());
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomText::getTitle()
     * @covers \REW\Backend\Leads\CustomField::getTitle()
     */
    public function testGetTitle()
    {
        $customText= new CustomText($this->db, $this->format, 5, 'wishlist', 'Wishlist', 1);
        $this->assertEquals('Wishlist', $customText->getTitle());
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomText::isEnabled()
     * @covers \REW\Backend\Leads\CustomField::isEnabled()
     */
    public function testIsEnabled()
    {
        $customText= new CustomText($this->db, $this->format, 5, 'wishlist', 'Wishlist', 1);
        $this->assertEquals(1, $customText->isEnabled());
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomText::getTable()
     */
    public function testGetTable()
    {
        $customText= new CustomText($this->db, $this->format, 5, 'wishlist', 'Wishlist', 1);
        $this->assertEquals('users_field_text', $customText->getTable());
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomText::loadValue()
     * @covers \REW\Backend\Leads\CustomField::loadValue()
     */
    public function testLoadValue()
    {

        $field_id = 5;
        $lead_id = 5;

        $customText= new CustomText($this->db, $this->format, $field_id, 'wishlist', 'Wishlist', 1);

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')->once()->with(['user_id' => $lead_id, 'field_id' => $field_id]);
        $stmt->shouldReceive('fetchColumn')->once()
            ->andReturn('I want doors, a roof and a few walls.  Floors would also be great.');

        $this->db->shouldReceive('prepare')
            ->once()
            ->with("SELECT `value` FROM `users_field_text` WHERE `user_id` = :user_id AND `field_id` = :field_id")
            ->andReturn($stmt);

            $this->assertEquals('I want doors, a roof and a few walls.  Floors would also be great.', $customText->loadValue($lead_id));
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomText::saveValue()
     * @covers \REW\Backend\Leads\CustomField::saveValue()
     */
    public function testSaveValue()
    {

        $field_id = 3;
        $lead_id = 5;
        $value = 'I want doors, a roof and a few walls.  Floors would also be great.';

        $customText = new CustomText($this->db, $this->format, $field_id, 'wishlist', 'Wishlist', 1);

        $stmt = m::mock(\PDOStatement::class);
        $stmt->shouldReceive('execute')->once()->with(['user_id' => $lead_id, 'field_id' => $field_id, 'value' => $value]);

        $this->db->shouldReceive('prepare')
            ->once()
            ->with('REPLACE INTO `users_field_text` SET `user_id` = :user_id, `field_id` = :field_id, `value` = :value;')
            ->andReturn($stmt);

        $this->assertTrue($customText->saveValue($lead_id, $value));
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomText::validateValue()
     * @covers \REW\Backend\Leads\CustomField::validateValue()
     */
    public function testValidateValue()
    {
        $customText= new CustomText($this->db, $this->format, 5, 'wishlist', 'Wishlist', 1);
        $this->assertTrue($customText->validateValue('I want doors, a roof and a few walls.  Floors would also be great.'));
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomText::parseValue()
     * @covers \REW\Backend\Leads\CustomField::parseValue()
     */
    public function testParseValue()
    {
        $value = 'I want doors, a roof and a few walls.  Floors would also be great.';
        $customText = new CustomText($this->db, $this->format, 5, 'wishlist', 'Wishlist', 1);
        $this->assertStringStartsWith($value, $customText->parseValue($value));
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomText::getSearchWhere()
     * @covers \REW\Backend\Leads\CustomField::getSearchWhere()
     */
    public function testGetSearchWhere()
    {

        $this->db->shouldReceive('quote')
            ->once()
            ->with('%doors%')
            ->andReturn('\'%doors%\'');

        $customText= new CustomText($this->db, $this->format, 5, 'wishlist', 'Wishlist', 1);
        $this->assertEquals('`ufs_cst_fld_wishlist`.`value` LIKE \'%doors%\'', $customText->getSearchWhere([$customText->getName() => 'doors']));
        $this->assertEquals('', $customText->getSearchWhere([]));
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomText::getSearchJoin()
     * @covers \REW\Backend\Leads\CustomField::getSearchJoin()
     */
    public function testGetSearchJoin()
    {
        $customText= new CustomText($this->db, $this->format, 5, 'wishlist', 'Wishlist', 1);
        $this->assertEquals(" LEFT JOIN `users_field_text` `ufs_cst_fld_wishlist` ON (`u`.`id` = `ufs_cst_fld_wishlist`.`user_id` AND `ufs_cst_fld_wishlist`.`field_id` = 5)", $customText->getSearchJoin([$customText->getName()=> 'doors'], 'u'));
        $this->assertEquals('', $customText->getSearchJoin([], 'u'));
    }

    /**
     * @covers \REW\Backend\Leads\CustomField\CustomText::getSearchString()
     * @covers \REW\Backend\Leads\CustomField::getSearchString()
     */
    public function testGetSearchString()
    {

        $this->format->shouldReceive('htmlspecialchars')
            ->once()
            ->with('Wishlist')
            ->andReturn('Wishlist');

        $this->format->shouldReceive('htmlspecialchars')
            ->once()
            ->with('doors')
            ->andReturn('doors');

        $customText= new CustomText($this->db, $this->format, 5, 'wishlist', 'Wishlist', 1);
        $this->assertEquals('<strong>Wishlist:</strong> doors', $customText->getSearchString([$customText->getName() => 'doors']));
        $this->assertEquals('', $customText->getSearchString([]));
    }
}
