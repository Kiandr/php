<?php

namespace REW\Seed\Faker\CRM;

use REW\Backend\Dashboard\EventListener\FormEventListener;
use REW\Core\Interfaces\DatabaseInterface;
use REW\Core\Interfaces\IDXInterface;
use REW\Seed\Faker\AbstractFaker;

/**
 * FormFaker
 * @package REW\Seed\Faker
 */
class FormFaker extends AbstractFaker
{

    /**
     * @var IDXInterface
     */
    protected $idx;

    /**
     * @var DatabaseInterface
     */
    protected $idxDb;

    /**
     * Listing inquiry comments
     * @var array
     */
    protected $inquiryComments = [
        'Please send me more information regarding {Address} (MLS #{ListingMLS})',
        'This is a great listing. Can you send me more homes in {AddressCity}?',
        'I want to learn more about {Address}, {AddressCity}, {AddressState}',
        'Please call me. I have a serious offer to put on {Address}.',
        'Send me more {ListingType} listings in {AddressCity}.',
        'Can you send me more information for {Address}?'
    ];

    /**
     * Property showing comments
     * @var array
     */
    protected $showingComments = [
        'I\'d like to request a showing of {Address}, {AddressCity}, {AddressState}',
        'Please call me, I would like to see this listing in person on {dayOfWeek}.',
        'Is this property available for viewing? I am available this {dayOfWeek}.',
        'I am available to look at {Address} on {dayOfWeek}. Can this be arrange?',
        'Request to view {Address} and send me more {AddressCity} homes for sale.'
    ];

    /**
     * Seller form comments
     * @var array
     */
    protected $sellingComments = [
        'I want to sell my home: {Address}, {AddressCity}, {AddressState}',
        'I have a property in {AddressCity}, {AddressState} to sell.',
        'Moving next month and need to sell my home by {monthName}',
        'I am looking for a listing agent to sell my home.',
        'Looking for a seller agent in {AddressCity}.',
        'I am inquiring about selling my {ListingType}'
    ];

    /**
     * @param IDXInterface $idx
     * @param DatabaseInterface $idxDb
     */
    public function __construct(IDXInterface $idx, DatabaseInterface $idxDb)
    {
        $this->idx = $idx;
        $this->idxDb = $idxDb;
    }

    /**
     * Fake form
     * @return array
     */
    public function getFakeData()
    {
        $faker = $this->getFaker();
        $type = $this->getFakeFormType();
        $data = $this->getFakeFormData($type);
        $timestamp = $faker->dateTimeThisYear();
        $read = $faker->optional()->dateTimeThisYear($timestamp);
        return [
            //'user_id' => NULL,
            'form'  => $type,
            'data'  => serialize($data),
            'page'  => $faker->optional->url(),
            'read'  => $read ? $read->format('Y-m-d H:i:s') : null,
            'timestamp' => $timestamp->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Get fake form type
     * @return string
     */
    public function getFakeFormType()
    {
        return $this->getFaker()->randomElement(
            array_merge(
                FormEventListener::SELLING_FORMS,
                FormEventListener::SHOWING_FORMS,
                FormEventListener::INQUIRY_FORMS
            )
        );
    }

    /**
     * Get fake form data
     * @param string $type
     * @return array
     */
    public function getFakeFormData($type)
    {
        $formData = ['form' => $type];
        $idxListing = $this->getIDXListingData();

        // Set property information for showing inquiries
        if (in_array($type, FormEventListener::SHOWING_FORMS) || in_array($type, FormEventListener::INQUIRY_FORMS)) {
            $formData['ListingMLS'] = $idxListing['ListingMLS'];
            $formData['ListingType'] = $idxListing['ListingType'];
            $formData['ListingFeed'] = $idxListing['ListingFeed'] ?: $this->idx->getName();
        }

        // Generate listing inquiry comments
        if (in_array($type, FormEventListener::INQUIRY_FORMS)) {
            $formData['comments'] = $this->getInquiryComments($idxListing);

        // Generate property showing comments
        } elseif (in_array($type, FormEventListener::SHOWING_FORMS)) {
            $formData['comments'] = $this->getShowingComments($idxListing);

        // Generate seller form comments
        } elseif (in_array($type, FormEventListener::SELLING_FORMS)) {
            $formData['comments'] = $this->getSellingComments($idxListing);
        }

        // Include property address for seller inquiries
        if (in_array($type, FormEventListener::SELLING_FORMS)) {
            $formData['fm-addr'] = $idxListing['Address'];
            $formData['fm-town'] = $idxListing['AddressCity'];
            $formData['fm-state'] = $idxListing['AddressState'];
            $formData['fm-postcode'] = $idxListing['AddressZipCode'];
        }

        // Return data
        return $formData;
    }

    /**
     * Get IDX listing data
     * @return array $listing {
     */
    public function getIDXListingData()
    {
        $querySelect = $this->idx->selectColumns(null, ['ListingMLS', 'ListingType', 'ListingFeed', 'Address', 'AddressCity', 'AddressState', 'AddressZipCode']);
        $queryString = sprintf("SELECT SQL_CACHE %s FROM `%s` ORDER BY RAND() LIMIT 1;", $querySelect, $this->idx->getTable());
        return $this->idxDb->fetchQuery($queryString);
    }

    /**
     * Get comments for listing inquiry
     * @param array $idxListing
     * @return string
     */
    public function getInquiryComments(array $idxListing)
    {
        $comments = $this->getNext($this->inquiryComments);
        return $this->replaceTags($comments, $idxListing);
    }

    /**
     * Get comments for property showing
     * @param array $idxListing
     * @return string
     */
    public function getShowingComments(array $idxListing)
    {
        $comments = $this->getNext($this->showingComments);
        return $this->replaceTags($comments, $idxListing);
    }

    /**
     * Get comments for seller form
     * @param array $idxListing
     * @return string
     */
    public function getSellingComments(array $idxListing)
    {
        $comments = $this->getNext($this->sellingComments);
        return $this->replaceTags($comments, $idxListing);
    }

    /**
     * Get next value from array
     * @param array $array
     * @return mixed
     */
    protected function getNext(array &$array)
    {
        $value = current($array);
        if (next($array) === false) {
            reset($array);
        }
        return $value;
    }

    /**
     * Replace {tags} in string
     * @param string $comments
     * @param array $tags
     * @return string
     */
    protected function replaceTags($comments, array $tags = [])
    {
        $faker = $this->getFaker();
        $tags = array_merge($tags, [
            'monthName' => $faker->monthName,
            'dayOfWeek' => $faker->dayOfWeek
        ]);
        return str_replace(array_map(function ($tag) {
            return sprintf('{%s}', $tag);
        }, array_keys($tags)), array_values($tags), $comments);
    }
}
