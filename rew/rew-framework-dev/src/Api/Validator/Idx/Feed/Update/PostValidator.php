<?php
namespace REW\Api\Validator\Idx\Feed\Update;

use REW\Api\Validator\Validator;

class PostValidator extends Validator
{
    /**
     * @var array
     */
    const REQUIRED_FIELDS = [
        'id', 'title', 'frequency', 'criteria'
    ];

    /**
     * {@inheritdoc}
     */
    public function validate(array $params)
    {
        $this->params = $params;

        $this->validateRequired(self::REQUIRED_FIELDS);
    }
}
