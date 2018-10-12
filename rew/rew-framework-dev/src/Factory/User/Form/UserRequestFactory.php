<?php

namespace REW\Factory\User\Form;

use REW\Model\User\Form\UserRequestModel;

/**
 * UserRequestFactory
 * @package REW\Factory\User\Form
 */
class UserRequestFactory implements UserRequestFactoryInterface
{
    /**
     * @param array $data
     * @return UserRequestModel
     */
    public function createFromArray(array $data)
    {
        $userResult = new UserRequestModel();
        $userResult = $userResult
            ->withId(!empty($data['id']) ? $data['id'] : null)
            ->withFirstName(!empty($data['onc5khko']) ? trim($data['onc5khko']) : null)
            ->withLastName(!empty($data['sk5tyelo']) ? trim($data['sk5tyelo']) : null)
            ->withEmail(!empty($data['mi0moecs']) ? trim($data['mi0moecs']) : null)
            ->withHoneypot(!empty($data['registration_type']) ? $data['registration_type'] : null)
            ->withPhone(!empty($data['phone']) ? $data['phone'] : null)
            ->withPassword(!empty($data['password']) ? $data['password'] : null)
            ->withPasswordConfirmation(!empty($data['confirm_password']) ? $data['confirm_password'] : null)
            ->withForm(!empty($data['form']) ? $data['form'] : '')
            ->withAutoresponder(!empty($data['autoresponder']) ? $data['autoresponder'] : '')
            ->withOptMarketing(!empty($data['opt_marketing'] == 'in') ? true : false)
            ->withOptTexts(!empty($data['opt_texts'] == 'in') ? true: false)
            ->withContactMethod(!empty($data['contact_method']) ? $data['contact_method'] : '')
            ->withComplianceAgree(!empty($data['agree'] == 'true') ? true: false)
            ->withListing(!empty($data['listing']) ? $data['listing'] : null)
            ->withEmailValidationCode(!empty($data['code']) ? $data['code'] : null);
        return $userResult;
    }
}
