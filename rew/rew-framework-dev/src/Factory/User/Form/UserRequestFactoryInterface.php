<?php

namespace REW\Factory\User\Form;

use REW\Model\User\Form\UserRequestModel;

/**
 * UserRequestFactory
 * @package REW\Factory\User\Form
 */
interface UserRequestFactoryInterface
{
    /**
     * @param array $data
     * @return UserRequestModel
     */
    public function createFromArray(array $data);
}
