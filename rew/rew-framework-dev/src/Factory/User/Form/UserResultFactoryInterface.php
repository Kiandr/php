<?php

namespace REW\Factory\User\Form;

use REW\Model\User\Form\UserResultModel;

/**
 * UserResultFactory
 * @package REW\Factory\User\Form
 */
interface UserResultFactoryInterface
{
    /**
     * @param array $data
     * @return UserResultModel
     */
    public function createFromArray(array $data);
}
