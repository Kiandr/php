<?php

namespace REW\Seed\Faker\Company;

use REW\Seed\Faker\ContactFaker;

/**
 * AccountFaker
 * @package REW\Seed\Faker
 */
class AccountFaker extends ContactFaker
{

    /**
     * Default account password
     * @var string
     */
    const DEFAULT_PASSWORD = 'changeme';

    /**
     * Track unique user names
     * @var array
     */
    protected static $uniqueUserNames = [];

    /**
     * Fake agent
     * @return array
     */
    public function getFakeData()
    {
        $contact = parent::getFakeData();
        return $contact + [
            'username' => $this->getUsername($contact),
            'password' => $this->getPassword()
        ] + $this->getFakeTimestamps();
    }

    /**
     * Generate username from first & last name
     * @param array $contact {
     *   @type string $first_name
     *   @type string $last_name
     *   @type string $email
     * }
     * @return string
     */
    public function getUsername(array $contact)
    {
        $faker = $this->getFaker();
        $firstName = $contact['first_name'];
        $lastName = $contact['last_name'];
        $primaryEmail = $contact['email'];
        if ($faker->boolean(20)) {
            return $primaryEmail;
        } elseif ($faker->boolean(20)) {
            $firstName = substr($firstName, 0, 1);
            $lastName = strtolower($lastName);
        } elseif ($faker->boolean(20)) {
            $lastName = substr($lastName, 0, 1);
            $firstName = strtolower($firstName);
        }
        // Remove unacceptable characters
        $firstName = preg_replace('/[^a-zA-Z]/', '', $firstName);
        $lastName = preg_replace('/[^a-zA-Z]/', '', $lastName);
        if ($faker->boolean) {
            $userName = $firstName . $lastName;
        } else {
            // Build username from full name
            $delimiters = ['-', '_', '.', ' '];
            $join = $delimiters[array_rand($delimiters)];
            $userName = implode($join, [$firstName, $lastName]);
        }
        // Username must be unique (or it will fail)
        if (isset(self::$uniqueUserNames[$userName])) {
            return sprintf('%s%s', $userName, self::$uniqueUserNames[$userName]++);
        } else {
            self::$uniqueUserNames[$userName] = 1;
            return $userName;
        }
    }

    /**
     * Generate account password
     * @return string
     */
    public function getPassword()
    {
        return password_hash(self::DEFAULT_PASSWORD, PASSWORD_DEFAULT, ['cost' => 10]);
    }
}
