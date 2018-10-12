<?php

namespace REW\Backend\Leads\CustomField;

use REW\Backend\Leads\CustomField;

/**
 * Class CustomText
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class CustomText extends CustomField
{

    /**
     * @var string
     */
    const TABLE_FIELD_TEXT = 'users_field_text';

    /**
     * Get Custom Data Type
     * @return string
     */
    public function getType()
    {
        return 'text field';
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return self::TABLE_FIELD_TEXT;
    }

    /**
     * Render Input
     * @param string $value
     * @return string
     */
    public function renderInput($value)
    {
        // Render Input
        return '<div class="fld">'
            . '<label class="fld-label">' . $this->format->htmlspecialchars($this->getTitle()) . '</label>'
            . '<textarea class="w1/1" name="' . $this->format->htmlspecialchars($this->getName()) . '" rows="6" cols="85">'
            . $this->format->htmlspecialchars($value)
            . '</textarea>'
            . '</div>';
    }
}
