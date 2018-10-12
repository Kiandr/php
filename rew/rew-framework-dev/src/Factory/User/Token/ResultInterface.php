<?php
namespace REW\Factory\User\Token;

interface ResultInterface
{
    /**
     * @param array $data
     * @return ResultModel
     */
    public function createFromArray(array $data);
}
