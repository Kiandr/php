<?php

namespace REW\Seed\Faker\Content;

use REW\Seed\Faker\AbstractFaker;

/**
 * LinkFaker
 * @package REW\Seed\Faker
 */
class LinkFaker extends AbstractFaker
{

    /**
     * Fake CMS link
     * @return array
     */
    public function getFakeData()
    {
        $faker = $this->getFaker();
        $url = $faker->unique()->url();
        return [
            'category'  => $url,
            'file_name' => $url,
            'link_name' => $faker->catchPhrase,
            'footer'    => $faker->randomElement(['_blank', '_self']),
            'hide'      => 't', //$faker->randomElement(['t', 'f'])
            'is_link'   => 't'
        ] + $this->getFakeTimestamps();
    }
}
