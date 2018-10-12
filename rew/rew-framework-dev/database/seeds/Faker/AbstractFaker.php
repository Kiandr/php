<?php

namespace REW\Seed\Faker;

use \Faker\Factory as FakerFactory;

abstract class AbstractFaker
{

    /**
     * Faker generator
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * Generate fake data
     * @return array
     */
    abstract public function getFakeData();

    /**
     * Make fake record
     * @param array $mergeData
     * @return array
     */
    public function make($mergeData = [])
    {
        $record = $this->getFakeData();
        if (!empty($mergeData)) {
            $record = array_merge($record, $mergeData);
        }
        return array_filter($record);
    }

    /**
     * Generate fake records
     * @param int $numRecords
     * @param array $mergeData
     * @return array
     */
    public function generate($numRecords, $mergeData = [])
    {
        $records = [];
        for ($i = 0; $i < $numRecords; $i++) {
            $records[] = $this->make($mergeData);
        }
        return $records;
    }

    /**
     * Get faker generator
     * @return \Faker\Generator
     */
    public function getFaker()
    {
        if ($this->faker) {
            return $this->faker;
        }
        return $this->faker = FakerFactory::create();
    }

    /**
     * Generate fake timestamps
     * @return array
     */
    public function getFakeTimestamps()
    {
        $faker = $this->getFaker();
        $updated = $faker->dateTimeThisMonth;
        $created = $faker->dateTimeThisYear($updated);
        return [
            'timestamp_created' => $created->format('Y-m-d H:i:s'),
            'timestamp_updated' => $updated->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Get path to uploads folder
     * @throws \UnexpectedValueException If uploads folder is not found
     * @return string
     */
    public function getPathToUploads()
    {
        $pathToUploads = realpath(__DIR__ . '/../../../httpdocs/uploads/');
        if (!is_dir($pathToUploads)) {
            throw new \UnexpectedValueException('Path to uploads not found.');
        }
        return $pathToUploads;
    }
}
