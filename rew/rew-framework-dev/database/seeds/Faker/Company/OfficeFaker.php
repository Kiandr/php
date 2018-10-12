<?php

namespace REW\Seed\Faker\Company;

use REW\Seed\Faker\AbstractFaker;

/**
 * OfficeFaker
 * @package REW\Seed\Faker
 */
class OfficeFaker extends AbstractFaker
{

    /**
     * Office image width (in pixels)
     * @var int
     */
    const OFFICE_IMAGE_WIDTH = 640;

    /**
     * Office image height (in pixels)
     * @var int
     */
    const OFFICE_IMAGE_HEIGHT = 480;

    /**
     * Office image category
     * @var string
     */
    const OFFICE_IMAGE_CATEGORY = null;

    /**
     * Fake office
     * @return array
     */
    public function getFakeData()
    {
        $faker = $this->getFaker();
        return [
            'title'       => $faker->unique()->company,
            'description' => implode(PHP_EOL, $faker->optional()->paragraphs ?: []),
            'email'       => $faker->optional()->unique()->safeEmail,
            'phone'       => $faker->optional()->unique()->phoneNumber,
            'fax'         => $faker->optional()->unique()->phoneNumber,
            'address'     => $faker->optional()->unique()->streetAddress,
            'city'        => $faker->optional()->city,
            'state'       => $faker->optional()->state,
            'zip'         => $faker->optional()->postcode,
            'display'     => $faker->randomElement(['Y', 'N']),
            'image'       => $this->getFakeOfficeImage()
        ] + $this->getFakeTimestamps();
    }

    /**
     * Get fake office image
     * @return string
     */
    public function getFakeOfficeImage()
    {
        $faker = $this->getFaker();
        return $faker->boolean ? basename(
            $faker->unique()->image(
                $this->getPathToOfficeUploads(),
                self::OFFICE_IMAGE_WIDTH,
                self::OFFICE_IMAGE_HEIGHT,
                self::OFFICE_IMAGE_CATEGORY
            )
        ) : '';
    }

    /**
     * Get path to office uploads
     * @return string
     */
    public function getPathToOfficeUploads()
    {
        return sprintf('%s/offices', $this->getPathToUploads());
    }
}
