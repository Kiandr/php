<?php
namespace REW\Factory\Community\Request;

use REW\Factory\Community\RequestModelFactoryInterface;
use REW\Model\Community\Request\RequestModel;

class RequestModelFactory implements RequestModelFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createFromArray(array $data = [])
    {
        $requestModel = new RequestModel();
        if (isset($data[self::ARRAY_FLD_MODE])) {
            $requestModel = $requestModel->withMode($data[self::ARRAY_FLD_MODE]);
        }

        if (isset($data[self::ARRAY_EXCLUDED_IDS])) {
            $requestModel = $requestModel->withIdsExcluded($data[self::ARRAY_EXCLUDED_IDS]);
        }

        return $requestModel;
    }
}
