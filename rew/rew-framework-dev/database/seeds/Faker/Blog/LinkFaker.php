<?php

namespace REW\Seed\Faker\Blog;

use REW\Seed\Faker\AbstractFaker;

/**
 * LinkFaker
 * @package REW\Seed\Faker
 */
class LinkFaker extends AbstractFaker
{

    /**
     * Fake blog link
     * @return array
     */
    public function getFakeData()
    {
        $faker = $this->getFaker();
        return [
            'link' => $faker->unique()->url(),
            'title' => $faker->unique()->catchPhrase,
            'target' => $faker->randomElement(['_blank', '_self'])
        ] + $this->getFakeTimestamps();
    }
}
