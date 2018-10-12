<?php

namespace REW\Seed\Faker;

/**
 * UploadFaker
 * @package REW\Seed\Faker
 */
class UploadFaker extends AbstractFaker
{

    /**
     * CMS uploads table
     * @var string
     */
    const UPLOAD_TABLE_NAME = 'cms_uploads';

    /**
     * Upload image width (in pixels)
     * @var int
     */
    const UPLOAD_IMAGE_WIDTH = 640;

    /**
     * Upload image height (in pixels)
     * @var int
     */
    const UPLOAD_IMAGE_HEIGHT = 480;

    /**
     * Upload image category
     * @var string
     */
    const UPLOAD_IMAGE_CATEGORY = null;

    /**
     * Fake CMS upload
     * @return array
     */
    public function getFakeData()
    {
        $faker = $this->getFaker();
        $image = $this->getFakeImage();
        $timestamp = $faker->dateTimeThisYear();
        return [
            'file' => basename($image),
            'size' => filesize($image),
            'timestamp' => $timestamp->format('Y-m-d H:i:s')
        ];
    }

    /**
     * @return string
     */
    protected function getFakeImage()
    {
        $faker = $this->getFaker();
        return $faker->unique()->image(
            $this->getPathToUploads(),
            self::UPLOAD_IMAGE_WIDTH,
            self::UPLOAD_IMAGE_HEIGHT,
            self::UPLOAD_IMAGE_CATEGORY
        );
    }
}
