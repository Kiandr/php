<?php

namespace REW\Seed\Faker\Company;

use REW\Seed\Faker\AbstractFaker;

/**
 * TeamFaker
 * @package REW\Seed\Faker
 */
class TeamFaker extends AbstractFaker
{

    /**
     * Fake team
     * @return array
     */
    public function getFakeData()
    {
        $faker = $this->getFaker();
        $timestamp = $faker->dateTimeThisYear();
        return [
            //'agent_id' => NULL,
            //'agent_permissions' => NULL,
            'name' => $faker->catchPhrase,
            'style' => $this->getFakeTeamStyle(),
            'description' => $faker->optional()->sentence,
            //'subdomain' => NULL,
            //'subdomain_link' => NULL,
            //'subdomain_idxs' => NULL,
            'timestamp' => $timestamp->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Get fake team style
     * @return string
     */
    public function getFakeTeamStyle()
    {
        $faker = $this->getFaker();
        $teamStyles = range('a', 'p');
        return $faker->randomElement($teamStyles);
    }
}
