<?php
namespace REW\Api\Validator\Drivetime;

use REW\Api\Exception\Validation\InvalidValueException;
use REW\Api\Validator\Validator;

class GetValidator extends Validator
{
    const REQUIRED_FIELDS = [
        'address',
        'direction',
        'arrivalTime',
        'duration'
    ];

    const VALID_DIRECTIONS = [
        'A',
        'D'
    ];

    /**
     * {@inheritdoc}
     */
    public function validate(array $params)
    {
        $this->params = $params;

        $this->validateRequired(self::REQUIRED_FIELDS);

        if (!empty($params['direction'])) {
            if (!in_array($params['direction'], self::VALID_DIRECTIONS)) {
                $this->errors['direction'] = new InvalidValueException('direction', $params['direction'],
                    sprintf('Direction can only be one of %s.', implode(',', self::VALID_DIRECTIONS)));
            }
        }

        if (!is_numeric($params['duration'])) {
            $this->errors['duration'] = new InvalidValueException('duration', $params['duration'],
                'Duration must be a number representing the number of minutes.');
        }
    }
}
