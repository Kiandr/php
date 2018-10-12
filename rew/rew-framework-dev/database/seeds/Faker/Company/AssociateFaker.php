<?php

namespace REW\Seed\Faker\Company;

/**
 * AssociateFaker
 * @package REW\Seed\Faker
 */
class AssociateFaker extends AccountFaker
{

    /**
     * Fake associate
     * @return array
     */
    public function getFakeData()
    {
        $faker = $this->getFaker();
        $account = parent::getFakeData();
        return $account + [
            'office_phone' => $faker->optional()->unique()->phoneNumber,
            'home_phone'   => $faker->optional()->unique()->phoneNumber,
            'cell_phone'   => $faker->optional()->unique()->phoneNumber,
            'fax'          => $faker->optional()->unique()->phoneNumber,
            'address'      => $faker->optional()->unique()->streetAddress,
            'city'         => $faker->optional()->city,
            'state'        => $faker->optional()->state,
            'zip'          => $faker->optional()->postcode
        ] + $this->getFakeTimestamps();
    }
}
