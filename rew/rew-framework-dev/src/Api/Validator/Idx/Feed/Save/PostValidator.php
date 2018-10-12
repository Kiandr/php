<?php
namespace REW\Api\Validator\Idx\Feed\Save;

use REW\Api\Exception\Validation\InvalidValueException;
use REW\Api\Exception\Validation\InvalidValueException\InvalidJsonException;
use REW\Api\Validator\Validator;
use REW\Core\Interfaces\SettingsInterface;

class PostValidator extends Validator
{
    const REQUIRED_FIELDS = [
        'title', 'frequency', 'criteria'
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

        if ($params['feed'] !== $config['idx_feed'] && !in_array($params['feed'],
                (!empty($feeds) ? $feeds : []))) {
            $this->errors['feed'] = new InvalidValueException('feed', $params['feed']);
        }
    }
}
