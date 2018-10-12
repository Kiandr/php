<?php
namespace REW\Api\Validator\Idx\Feed;

use REW\Api\Exception\Validation\InvalidTypeException;
use REW\Api\Exception\Validation\InvalidValueException;
use REW\Api\Exception\Validation\InvalidValueException\InvalidJsonException;
use REW\Api\Validator\Validator;
use REW\Core\Interfaces\SettingsInterface;
use REW\Factory\Idx\Search\ListingRequestFactory;
use REW\Model\Idx\Map\Radius\Radius;

class GetValidator extends Validator
{
    const REQUIRED_FIELDS = [
        'feed'
    ];

    const VALID_SORT_DIRS = [
        'ASC',
        'DESC'
    ];

    const MAX_LIMIT = 500;

    protected $settings;

    /**
     * GetValidator constructor.
     * @param \REW\Core\Interfaces\SettingsInterface $settings
     */
    public function __construct(SettingsInterface $settings)
    {
        $this->settings = $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $params)
    {
        $this->params = $params;

        $this->validateRequired(self::REQUIRED_FIELDS);
        $config = $this->settings->getConfig();

        $feeds = !empty($config['idx_feeds']) ? array_keys($config['idx_feeds']) : [];
        if ($params['feed'] !== $config['idx_feed'] && !in_array($params['feed'], $feeds)) {
            $this->errors['feed'] = new InvalidValueException('feed', $params['feed']);
        }

        if (!empty($params['before']) && !empty($params['after'])) {

            // Setting this message for both before and after.
            $beforeAndAfterErrorMessage = 'Only one of either before or after can be set!';

            $this->errors['before'] = new InvalidValueException('before', $params['before'],
                $beforeAndAfterErrorMessage);
            $this->errors['after'] = new InvalidValueException('after', $params['after'],
                $beforeAndAfterErrorMessage);
        }

        // Make sure limit is a number and does not exceed self::MAX_LIMIT
        if (!empty($params['limit'])) {
            if (!is_numeric($params['limit']) || (int) $params['limit'] != $params['limit']) {
                $this->errors['limit'] = new InvalidTypeException('limit', $params['limit'], 'int');
            }

            if ($params['limit'] > self::MAX_LIMIT) {
                $this->errors['limit'] = new InvalidValueException('limit', $params['limit'],
                    sprintf('Limit can only be %d at most.', self::MAX_LIMIT));
            }
        }

        // Make sure sort direction is valid.
        if (!empty($params['sort'])) {
            if (!in_array($params['sort'], self::VALID_SORT_DIRS)) {
                $this->errors['limit'] = new InvalidValueException('sort', $params['sort'],
                    sprintf('Sort can only be one of %s.', implode(',', self::VALID_SORT_DIRS)));
            }
        }

        if (isset($params['criteria'])) {
            $criteria = json_decode($params['criteria'], true);

            if (null === $criteria) {
                $this->errors['criteria'] = new InvalidJsonException('criteria', $params['criteria'],
                    json_last_error_msg());
            }

            // Map radius criteria
            if (!empty($criteria[ListingRequestFactory::FLD_MAP_RADIUS])) {
                if (!is_array($criteria[ListingRequestFactory::FLD_MAP_RADIUS])) {
                    $this->errors['criteria'] = new InvalidValueException('criteria', $params['criteria'], 'Provided radius is malformed.');
                } else {
                    foreach ($criteria[ListingRequestFactory::FLD_MAP_RADIUS] as $radius) {
                        if (empty($radius[Radius::FLD_LATITUDE]) || !is_numeric($radius[Radius::FLD_LATITUDE])) {
                            $this->errors['criteria'] = new InvalidValueException('criteria', $params['criteria'], 'Provided radius latitude is missing or non-numeric.');
                        }
                        if (empty($radius[Radius::FLD_LONGITUDE]) || !is_numeric($radius[Radius::FLD_LONGITUDE])) {
                            $this->errors['criteria'] = new InvalidValueException('criteria', $params['criteria'], 'Provided radius longitude is missing or non-numeric.');
                        }
                        if (empty($radius[Radius::FLD_RADIUS]) || !is_numeric($radius[Radius::FLD_RADIUS])) {
                            $this->errors['criteria'] = new InvalidValueException('criteria', $params['criteria'], 'Provided radius is missing or non-numeric.');
                        }
                    }
                }
            }
        }
    }
}
