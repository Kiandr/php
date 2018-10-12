<?php

namespace REW\Seed\Faker\Content;

use REW\Seed\Faker\AbstractFaker;

/**
 * TestimonialFaker
 * @package REW\Seed\Faker
 */
class TestimonialFaker extends AbstractFaker
{

    /**
     * Fake testimonial
     * @return array
     */
    public function getFakeData()
    {
        $faker = $this->getFaker();
        return [
            //'agent_id' => NULL,
            'client' => $faker->optional()->unique()->name,
            'testimonial' => $faker->text(),
            //'link' => NULL
        ] + $this->getFakeTimestamps();
    }
}
