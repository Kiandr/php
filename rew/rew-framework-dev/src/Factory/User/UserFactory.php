<?php

namespace REW\Factory\User;

use REW\Model\User\Search\UserResultModel;

/**
 * UserFactory
 * @package REW\Factory\User
 */
class UserFactory
{
    /**
     * @param array $data
     * @return UserResultModel
     */
    public function createFromArray(array $data)
    {
        $userResult = new UserResultModel();
        $userResult = $userResult
            ->withId(!empty($data['id']) ? $data['id'] : null)
            ->withAgent(!empty($data['agent']) ? $data['agent'] : null)
            ->withFirstName(!empty($data['first_name']) ? $data['first_name'] : null)
            ->withLastName(!empty($data['last_name']) ? $data['last_name'] : null)
            ->withEmail(!empty($data['email']) ? $data['email'] : null)
            ->withPhone(!empty($data['phone']) ? $data['phone'] : null)
            ->withGuid(!empty($data['guid']) ? $data['guid'] : null)
            ->withNotifyFavs(!empty($data['notify_favs'] && $data['notify_favs'] === 'yes') ? true : false)
            ->withNotifySearches(!empty($data['notify_searches'] && $data['notify_searches'] === 'yes') ? true : false);
        return $userResult;
    }
}
