<?php

namespace REW\Seed\Faker\Blog;

use REW\Seed\Faker\AbstractFaker;

/**
 * TagFaker
 * @package REW\Seed\Faker
 */
class TagFaker extends AbstractFaker
{

    /**
     * Fake blog tag
     * @return array
     */
    public function getFakeData()
    {
        $faker = $this->getFaker();
        $link = $faker->unique()->slug();
        $title = $faker->unique()->words(rand(2, 4), true);
        return [
            'link' => $link,
            'title' => $title,
        ] + $this->getFakeTimestamps();
    }
}
