<?php

namespace REW\Seed\Faker\CRM;

use REW\Seed\Faker\ContactFaker;

/**
 * LeadFaker
 * @package REW\Seed\Faker
 */
class LeadFaker extends ContactFaker
{

    /**
     * Fake remarks
     * @var array
     */
    protected $fakeRemarks = [
        "The {lname} family is moving from out of state for {fname}'s new job in {area} {city}.",
        "Looking to move to {city} in {monthName} but has not sold their {description} {property} yet.",
        "Open house of {address} is scheduled for {dayOfWeek} at {timeOfDay} - don't forget to {doSomething}.",
        "{fname} is moving from {state} and is interesting in buying a {description} {property} in {city}.",
        "{title} {lname} is away all week in {city} and wont be back until {dayOfWeek}.",
        "Don't forget: {fname} is only available during the evening after {timeOfNight}",
        "Set up an {appointment} for {timeOfDay} {dayOfWeek} to meet {fname} and their {family}.",
        "{fname} asked to see more {properties} within {distance} of {school}",
        "Only interested in {properties} outside of {city} with {requirement}",
        "{interested} {property} located near {street}. Must have {requirement}.",
        "{address} is being considered. Need to {doSomething} on {dayOfWeek}.",
        "{fname} is already working with an another agent in {city}",
        "{interested} {description} {property} in {area} {city} for {budget}",
        "{interested} {property} {distance} of {city} for {budget}",
        "Must {doSomething} on {dayOfWeek} before {timeOfNight}."
    ];

    /**
     * Fake lead
     * @return array
     */
    public function getFakeData()
    {
        $faker = $this->getFaker();
        $contact = parent::getFakeData();
        $status = $this->getFakeLeadStatus();
        $timestamp = $faker->dateTimeThisYear();
        $timestamp_active = $faker->dateTimeThisYear($timestamp);
        $timestamp_assigned = $faker->dateTimeThisYear($timestamp);
        return $contact + [
            'email_alt'            => $faker->optional(.1)->safeEmail,
            'address1'             => $faker->optional()->streetAddress,
            'address2'             => $faker->optional(.1)->secondaryAddress,
            //'address3'             => NULL,
            //'country'              => NULL,
            'state'                => $faker->optional()->state,
            'city'                 => $faker->optional()->city,
            'zip'                  => $faker->optional()->postcode,
            'phone'                => $faker->optional()->phoneNumber,
            'phone_cell'           => $faker->optional()->phoneNumber,
            'phone_work'           => $faker->optional()->phoneNumber,
            'phone_fax'            => $faker->optional()->phoneNumber,
            //'phone_home_status'    => NULL,
            //'phone_cell_status'    => NULL,
            //'phone_work_status'    => NULL,
            //'password'             => NULL,
            'contact_method'       => $faker->optional()->randomElement(['email', 'phone', 'text']),
            'comments'             => null,
            'remarks'              => $this->getFakeRemarks($contact),
            'notes'                => $this->getFakeNotes($contact),
            'heat'                 => $this->getFakeLeadHeat(),
            'status'               => $status,
            'rejectwhy'            => $status === 'rejected' ? $faker->sentence : null,
            'manual'               => $faker->optional()->randomElement(['yes', 'no']),
            'verified'             => $faker->optional()->randomElement(['yes', 'no']),
            'opt_marketing'        => $faker->optional()->randomElement(['in', 'out']),
            'opt_searches'         => $faker->optional()->randomElement(['in', 'out']),
            'opt_texts'            => $faker->optional()->randomElement(['in', 'out']),
            'bounced'              => $faker->optional()->randomElement(['true', 'false']),
            'fbl'                  => $faker->optional()->randomElement(['true', 'false']),
            'notify_favs'          => $faker->optional()->randomElement(['yes', 'no']),
            'notify_searches'      => $faker->optional()->randomElement(['yes', 'no']),
            'share_lead'           => null,
            'imported'             => $faker->optional()->randomElement(['Y', 'N']),
            'auto_search'          => $faker->optional()->randomElement(['Y', 'N']),
            'auto_rotate'          => $faker->optional()->randomElement(['true', 'false']),
            'search_auto'          => $faker->optional()->randomElement(['true', 'false']),
            'referer'              => $faker->optional()->domainName,
            'keywords'             => $faker->optional()->bs,
            'search_type'          => $this->getFakeSearchType(),
            'search_city'          => $faker->optional()->city,
            //'search_subdivision'   => NULL,
            //'search_minimum_price' => NULL,
            //'search_maximum_price' => NULL,
            'timestamp'            => $timestamp->format('Y-m-d H:i:s'),
            'timestamp_active'     => $timestamp_active->format('Y-m-d H:i:s'),
            'timestamp_assigned'   => $timestamp_assigned->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Get fake lead heat
     * @return string|NULL
     */
    public function getFakeLeadHeat()
    {
        return $this->getFaker()->optional()->randomElement(['hot', 'mediumhot', 'warm', 'lukewarm', 'cold']);
    }

    /**
     * Get fake lead status
     * @return string
     */
    public function getFakeLeadStatus()
    {
        return $this->getFaker()->randomElement(['unassigned', 'pending', 'accepted', 'rejected', 'closed']);
    }

    /**
     * Get fake search type
     * @return string|NULL
     */
    public function getFakeSearchType()
    {
        $searchTypes = ['Single Family', 'Residential', 'House', 'Condo', 'Condominium', 'Commercial', 'Rental'];
        return $this->getFaker()->optional->randomElement($searchTypes);
    }

    /**
     * Get fake lead remarks
     * @param array $contact
     * @return string|NULL
     */
    public function getFakeRemarks(array $contact = [])
    {
        if ($this->faker->boolean) {
            return null;
        }
        $remarks = $this->fakeRemarks[array_rand($this->fakeRemarks)];
        return $this->replacePlaceholderTags($remarks, $contact);
    }

    /**
     * Get fake lead notes
     * @param array $contact
     * @return string|NULL
     */
    public function getFakeNotes(array $contact = [])
    {
        if ($this->faker->boolean) {
            return null;
        }
        $remarks = $this->fakeRemarks[array_rand($this->fakeRemarks)];
        return $this->replacePlaceholderTags($remarks, $contact);
    }

    /**
     * Replace placeholder tags
     * @param string $remarks
     * @param array $contact
     * @return string
     */
    protected function replacePlaceholderTags($remarks, $contact)
    {
        $faker = $this->getFaker();
        foreach ([
             'fname'        => $contact['first_name'],
             'lname'        => $contact['last_name'],
             'title'        => $faker->title,
             'city'         => $faker->city,
             'state'        => $faker->state,
             'street'       => $faker->streetName,
             'address'      => $faker->streetAddress,
             'monthName'    => $faker->monthName,
             'dayOfWeek'    => $faker->dayOfWeek,
             'timeOfDay'    => $faker->boolean() ? $faker->numberBetween(8, 11) . 'am' : $faker->numberBetween(1, 9) . 'pm',
             'timeOfNight'  => $faker->numberBetween(5, 9) . 'p]',
             'doSomething'  => $faker->randomElement(['bring the keys', 'sign the contract', 'print the report', 'review the property report', 'recommend referal', 'send newsletter', 'gather paperwork', 'talk to ' . $faker->firstName]),
             'requirement'  => $faker->randomElement(['an oversized bonus/entertainment room', 'a swimming pool', 'hardwood floors', 'granite countertops', 'a storage shed', 'an open floor plan', 'a jacuzzi tub', 'a 2-car garage', 'plenty of living space']),
             'description'  => $faker->randomElement(['2 bedroom', 'waterfront', 'lake-front', 'beach', '2 bed, 3 bath', 'beautifully landscaped', 'fully furnished', 'family-friendly', 'western exposure']),
             'property'     => $faker->randomElement(['property', 'listing', 'single family home', 'home', 'house', 'single-story residence', 'condominium', 'condo', 'duplex', 'fixer-upper', 'apartment', 'townhome', 'farm', 'acreage']),
             'properties'   => $faker->randomElement(['properties', 'listings', 'single family homes', 'homes', 'condominiums', 'condos', 'fixer-uppers', 'apartments', 'townhomes']),
             'distance'     => $faker->numberBetween(1, 10) . ' miles',
             'family'       => $faker->randomElement(['family', 'partner', 'spouse', 'wife', 'kids']),
             'appointment'  => $faker->randomElement(['appointment', 'meeting']),
             'interested'   => $faker->randomElement(['Interested in', 'Looking for', 'Hoping to find', 'Wants to find', 'In search of']),
             'school'       => $faker->city . ' ' . $faker->randomElement(['Elementary', 'Middle', 'High']) . ($faker->boolean ? ' School' : ''),
             'area'         => $faker->randomElement(['South', 'East', 'North', 'West', 'Downtown', 'Uptown']),
             'budget'       => $faker->randomElement(['under {highPrice}', 'over {lowPrice}', 'for {highPrice}+', 'between {lowPrice} and {highPrice}']),
             'lowPrice'     => '$' . number_format($faker->randomElement([100000, 150000, 200000, 225000, 250000, 275000, 280000, 300000, 325000])),
             'highPrice'    => '$' . number_format($faker->randomElement([350000, 375000, 400000, 425000, 450000, 500000, 600000]))
         ] as $search => $replace) {
            $remarks = str_replace('{' . $search . '}', $replace, $remarks);
        }
        return $remarks;
    }
}
