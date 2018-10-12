<?php

namespace REW\Seed\Faker\CRM;

use REW\Seed\Faker\AbstractFaker;

/**
 * GroupFaker
 * @package REW\Seed\Faker
 */
class GroupFaker extends AbstractFaker
{

    /**
     * Fake group
     * @return array
     */
    public function getFakeData()
    {
        $faker = $this->getFaker();
        $timestamp = $faker->dateTimeThisYear();
        return [
            //'agent_id' => NULL,
            //'associate' => NULL,
            'name' => $faker->catchPhrase,
            'style' => $this->getFakeGroupStyle(),
            'description' => $faker->optional()->sentence,
            'timestamp' => $timestamp->format('Y-m-d H:i:s'),
            'user' => $faker->randomElement(['true', 'false'])
        ];
    }

    /**
     * Get fake group style
     * @return string
     */
    public function getFakeGroupStyle()
    {
        $faker = $this->getFaker();
        $groupStyles = range('a', 'p');
        return $faker->randomElement($groupStyles);
    }
}
