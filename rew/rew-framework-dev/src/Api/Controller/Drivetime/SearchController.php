<?php
namespace REW\Api\Controller\Drivetime;

use REW\Backend\Partner\Inrix\DriveTime;
use REW\Core\Interfaces\SettingsInterface;
use REW\Api\Validator\Drivetime\GetValidator;
use REW\Api\Exception\Request\UnauthorizedRequestException;

/**
 * Class SearchController
 * @package REW\Api\Controller\Drivetime
 */
class SearchController
{
    /**
     * @var DriveTime
     */
    protected $drivetime;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var GetValidator
     */
    protected $getValidator;

    /**
     * @param DriveTime $driveTime
     * @parma SettingsInterface $settings
     * @parma GetValidator $getValidator
     */
    public function __construct(DriveTime $driveTime, SettingsInterface $settings, GetValidator $getValidator)
    {
        $this->driveTime = $driveTime;
        $this->settings = $settings;
        $this->getValidator = $getValidator;
    }

    /**
     * @param array $params
     * @return array
     */
    public function getSearch(array $params)
    {
        if (empty($this->settings->MODULES['REW_IDX_DRIVE_TIME']) || !in_array('drivetime', $this->settings->ADDONS)) {
            throw new UnauthorizedRequestException('Drivetime is not enabled on this domain.');
        }

        // Validate Required Parameters
        $this->getValidator->validateFields($params);
        $travelPolygon = $this->driveTime->authenticateAndBuildPolygon(
            $params['address'],
            $params['lat'],
            $params['lng'],
            $params['direction'],
            $params['duration'],
            $params['arrivalTime']
        );
        return [
            'polygon' => $travelPolygon,
            'address' => $params['address'],
            'direction' => $params['direction'],
            'duration' => $params['duration'],
            'arrivalTime' => $params['arrivalTime']
        ];
    }
}
