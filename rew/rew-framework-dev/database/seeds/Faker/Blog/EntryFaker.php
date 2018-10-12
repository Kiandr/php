<?php

namespace REW\Seed\Faker\Blog;

use REW\Seed\Faker\AbstractFaker;

/**
 * EntryFaker
 * @package REW\Seed\Faker
 */
class EntryFaker extends AbstractFaker
{

    /**
     * Fake blog entry
     * @return array
     */
    public function getFakeData()
    {
        $faker = $this->getFaker();
        $title = $faker->unique()->sentence();
        $link = $faker->unique()->slug();
        $published = $faker->boolean(75);
        $published_ts = $published ? $faker->dateTimeThisYear() : false;
        $body = sprintf('<p>%s</p>', implode('</p><p>', $faker->paragraphs(rand(1, 6))));
        return [
            'agent' => 1,
            'title' => $title,
            'link' => $link,
            'body' => $body,
            //'tags' => NULL,
            //'categories' => NULL,
            //'meta_tag_desc' => NULL,
            //'meta_tag_keywords' => NULL,
            //'link_title1' => NULL,
            //'link_title2' => NULL,
            //'link_title3' => NULL,
            //'link_url1' => NULL,
            //'link_url2' => NULL,
            //'link_url3' => NULL,
            'views' => $faker->numberBetween(0, 1000),
            'published' => $published ? 'true' : 'false',
            'timestamp_published' => $published_ts ? $published_ts->format('Y-m-d H:i:s') : false
        ] + $this->getFakeTimestamps();
    }
}
