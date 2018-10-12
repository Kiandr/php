<?php

namespace REW\Seed\Faker\Blog;

use REW\Seed\Faker\AbstractFaker;

/**
 * CategoryFaker
 * @package REW\Seed\Faker
 */
class CategoryFaker extends AbstractFaker
{

    /**
     * Fake blog category
     * @return array
     */
    public function getFakeData()
    {
        $faker = $this->getFaker();
        $link = $faker->unique()->slug();
        $title = $faker->unique()->words(rand(2, 4), true);
        $description = $faker->optional()->paragraph() ?: '';
        return [
            'link' => $link,
            'title' => $title,
            'description' => $description,
            //'parent' => $parent,
            //'page_title' => NULL,
            //'meta_tag_desc' => NULL,
            //'meta_tag_keywords' => NULL,
            //'order' => NULL
        ] + $this->getFakeTimestamps();
    }
}
