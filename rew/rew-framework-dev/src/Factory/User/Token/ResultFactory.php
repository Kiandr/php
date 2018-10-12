<?php
namespace REW\Factory\User\Token;

use REW\Model\User\Token\ResultModel;
use REW\Factory\User\Token\ResultInterface;

/**
 * Class ResultFactory
 * @package REW\Factory\User\Token
 */
class ResultFactory implements ResultInterface
{
    /**
     * @param array $data
     * @return ResultModel
     */
    public function createFromArray(array $data)
    {
        $tokenModel = new ResultModel();
        $tokenModel = $tokenModel
            ->withToken($data['token'])
            ->withTimestamp($data['timestamp']);
        return $tokenModel;
    }
}
