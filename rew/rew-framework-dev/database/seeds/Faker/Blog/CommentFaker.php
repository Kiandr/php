<?php

namespace REW\Seed\Faker\Blog;

use REW\Seed\Faker\AbstractFaker;

/**
 * CommentFaker
 * @package REW\Seed\Faker
 */
class CommentFaker extends AbstractFaker
{

    /**
     * Fake blog comment
     * @return array
     */
    public function getFakeData()
    {
        $faker = $this->getFaker();
        return [
            //'agent' => NULL,
            //'entry' => NULL,
            'name'  => $faker->name,
            'email' => $faker->safeEmail,
            'website' => $faker->optional()->url,
            'comment' => $faker->paragraphs(rand(1, 2), true), //$faker->realText(),
            'ip_address' => null, //$faker->ipv4,
            'published' => $faker->randomElement(['true', 'false']),
            'subscribed' => 'false', //$faker->randomElement(['true', 'false']),
        ] + $this->getFakeTimestamps();
    }
}
