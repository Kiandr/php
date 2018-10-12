<?php

namespace REW\Factory\User\Form;

use REW\Model\User\Form\UserResultModel;

/**
 * UserResultFactory
 * @package REW\Factory\User\Form
 */
class UserResultFactory implements UserResultFactoryInterface
{
    /**
     * @param array $data
     * @return UserResultModel
     */
    public function createFromArray(array $data)
    {
        $userResult = new UserResultModel();
        $userResult = $userResult
            ->withSuccess(!empty($data['success']) ? $data['success'] : '')
            ->withErrors(!empty($data['errors']) ? $data['errors'] : [])
            ->withPpc(!empty($data['ppc']) ? $data['ppc'] : [])
            ->withData(!empty($data['data']) ? $data['data'] : []);
        return $userResult;
    }
}
