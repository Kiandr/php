<?php
namespace REW\Factory\Idx\Search\Field;

use REW\Factory\Idx\Search\FieldFactoryInterface;
use REW\Model\Idx\Search\Field\Field;
use REW\Model\Idx\Search\FieldInterface;

class FieldFactory implements FieldFactoryInterface
{
    const FLD_FORM_FIELD = 'form_field';

    const FLD_NAME = 'name';

    const FLD_MATCH = 'match';

    const FLD_VALUE = 'value';

    const FLD_IDX_FIELD = 'idx_field';

    /**
     * @param array $data
     * @return FieldInterface
     */
    public function createFromArray(array $data)
    {
        $field = (new Field())
            ->withFormFieldName((isset($data[self::FLD_FORM_FIELD]) ? $data[self::FLD_FORM_FIELD] : null))
            ->withDisplayName((isset($data[self::FLD_NAME]) ? $data[self::FLD_NAME] : null))
            ->withSearchOperation((isset($data[self::FLD_MATCH]) ? $data[self::FLD_MATCH] : null))
            ->withSearchValue((isset($data[self::FLD_VALUE]) ? $data[self::FLD_VALUE] : null));

        if (isset($data['idx_field'])) {
           $field = $field->
            withDbFields((is_array($data['idx_field']) ? $data['idx_field'] : [$data['idx_field']]));
        }

        return $field;
    }
}
