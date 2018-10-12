<?php

namespace REW\Seed\Faker;

/**
 * ContactFaker
 * @package REW\Seed\Faker
 */
class ContactFaker extends AbstractFaker
{

    /**
     * Track unique emails
     * @var array
     */
    protected static $uniqueEmails = [];

    /**
     * Fake contact
     * @return array
     */
    public function getFakeData()
    {
        $firstName = $this->getFirstName();
        $lastName = $this->getLastName();
        $email = $this->getEmail($firstName, $lastName);
        return [
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'email'      => $email
        ];
    }

    /**
     * Pick a gender
     * @return string|NULL
     */
    public function getGender()
    {
        $genders = [null, 'Male', 'Female'];
        return $genders[array_rand($genders)];
    }

    /**
     * Get first name to match gender
     * @param string $gender
     * @return string
     */
    public function getFirstName($gender = null)
    {
        while (is_null($gender)) {
            $gender = $this->getGender();
        }
        if ($gender === 'Male') {
            return $this->getFaker()->firstNameMale;
        } elseif ($gender === 'Female') {
            return $this->getFaker()->firstNameFemale;
        }
    }

    /**
     * Get a random last name
     * @return string
     */
    public function getLastName()
    {
        return $this->getFaker()->lastName;
    }

    /**
     * Generate email from first/last name
     * @param string $firstName
     * @param string $lastName
     * @return string
     */
    public function getEmail($firstName, $lastName)
    {
        $faker = $this->getFaker();
        $email = [];
        // Concat first and last name
        $lowerCase = $faker->boolean(75);
        $concatName = $faker->optional(25)->randomElement(['', '-', '.', '_']);
        if ($concatName) {
            $email[] = $lowerCase ? strtolower($firstName) : $firstName;
            $email[] = $concatName;
            $email[] = $lowerCase ? strtolower($lastName) : $lastName;
        // Use first name
        } elseif ($faker->boolean(25)) {
            if (strlen($firstName) <= 4) {
                $concat = $faker->randomElement(['-', '.', '_']);
                $prefix = str_replace(' ', $concat, $faker->state);
                $email[] = $lowerCase ? strtolower($prefix) : $prefix;
                $email[] = $concat;
            }
            $email[] = $lowerCase ? strtolower($firstName) : $firstName;
        // Use last name
        } else {
            $email[] = $lowerCase ? strtolower($lastName) : $lastName;
        }
        // Append random number
        $parts = count($email);
        if ($faker->boolean(25) || $parts === 1) {
            $email[] = $faker->numberBetween(1, 100);
        // Append suffix
        } elseif ($faker->boolean(10) && $parts < 3) {
            $email[] = $faker->suffix;
        }
        // Return email address
        $emailUser = implode($email);
        $emailUser = preg_replace('/[^a-z0-9\-\.]/i', $concatName ?: '.', $emailUser);
        $emailUser = str_replace(['..', '--', '__'], ['.', '-', '_'], $emailUser);
        $emailUser = trim($emailUser, '-._+');
        $emailDomain = '@' . $faker->safeEmailDomain;
        $emailAddress = $emailUser . $emailDomain;
        // Email must be unique (or it will fail)
        if (isset(self::$uniqueEmails[$emailAddress])) {
            return sprintf('%s%s%s', $emailUser, ++self::$uniqueEmails[$emailAddress], $emailDomain);
        } else {
            self::$uniqueEmails[$emailAddress] = 1;
            return $emailAddress;
        }
    }
}
