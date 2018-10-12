<?php

namespace REW\Seed\Faker\Content;

use REW\Seed\Faker\AbstractFaker;

/**
 * PageFaker
 * @package REW\Seed\Faker
 */
class PageFaker extends AbstractFaker
{

    /**
     * Fake CMS page
     * @return array
     */
    public function getFakeData()
    {
        $faker = $this->getFaker();
        $fileName = $faker->slug();
        $linkName = $faker->unique()->sentence(rand(3, 8));
        $metaKeywords = $faker->optional()->words(10) ?: [];
        return [
            //'agent' => NULL,
            //'team' => NULL,
            'category' => $fileName,
            'file_name' => $fileName,
            'link_name' => $linkName,
            'page_title' => $faker->optional()->sentence,
            'meta_tag_desc' => $faker->optional()->text(200),
            'category_html' => sprintf('<p>%s</p>', implode('</p><p>', $faker->paragraphs(rand(1, 10)))),
            'hide' => 't' //$faker->randomElement(['t', 'f'])
        ] + $this->getFakeTimestamps();
    }
}
