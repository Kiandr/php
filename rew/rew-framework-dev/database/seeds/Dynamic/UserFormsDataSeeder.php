<?php

namespace REW\Seed\Dynamic;

use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\SettingsInterface;

/**
 * Class UserFormsDataSeeder
 * @package REW\Seed\Dynamic
 */
class UserFormsDataSeeder
{

    const DEMO_LEAD_EMAIL_DOMAIN = 'rewdemo.com';

    const DEFAULT_FORM_SUBMISSION_TYPE = 'Quick Inquire';

    const NUM_FORM_SUBMISSIONS = 8;

    const MLS_LISTING_FETCH_FIELDS = [
        'Address',
        'AddressState',
        'AddressCity',
        'ListingMLS',
        'ListingType'
    ];

    /**
     * @var DBInterface
     */
    protected $db;

    /**
     * @var IDXFactoryInterface
     */
    protected $idxFactory;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    public function __construct(
        DBInterface $db,
        IDXFactoryInterface $idxFactory,
        SettingsInterface $settings
    ) {
        $this->db = $db;
        $this->idxFactory = $idxFactory;
        $this->settings = $settings;
    }

    /**
     * Run the dynamic data seeder: Generate + Run queries
     * @throws Exception
     * @throws PDOException
     */
    public function run()
    {
        // Only need to do this if the site is hooked up with an IDX feed
        if (!empty($this->settings->IDX_FEED) && $this->settings->IDX_FEED !== 'cms') {

            // Get a Valid Lead ID
            $lead_ids = $this->getDemoLeadIds(1);
            if (empty($lead_ids)) {
                throw new \Exception('No demo leads available.');
            }

            // Populate User Forms
            $listings = $this->getRandomMlsListings(self::NUM_FORM_SUBMISSIONS);
            $form_query = $this->buildUserFormsQuery($listings);
            $query = $this->db->prepare($form_query);
            $query->execute(['lead_id' => (int) reset($lead_ids)['id']]);
            $num_forms_query = $this->db->prepare(sprintf(
                'UPDATE `%s` '
                . ' SET `num_forms` = :num_forms '
                . ' WHERE `id` = :lead_id '
                . ';',
                $this->settings->TABLES['LM_LEADS']
            ));
            $num_forms_query->execute([
                'num_forms' => (self::NUM_FORM_SUBMISSIONS + 4),
                'lead_id' => (int) reset($lead_ids)['id']
            ]);
        }
    }

    /**
     * Get ID of first demo lead
     * @param int $limit
     * @throws InvalidArgumentException
     * @throws PDOException
     * @return array
     */
    protected function getDemoLeadIds($limit)
    {
        if (!is_int($limit) || $limit <= 0) {
            throw new \InvalidArgumentException('You must provide a limit when requesting demo leads.');
        }
        return $this->db->fetchAll(sprintf(
            "SELECT `id` "
            . " FROM `%s` "
            . " WHERE `email` LIKE :demo_domain "
            . " ORDER BY `id` ASC "
            . " LIMIT %s "
            . ";",
            $this->settings->TABLES['LM_LEADS'],
            (int) $limit
        ), [
            'demo_domain' => '%' . self::DEMO_LEAD_EMAIL_DOMAIN
        ]);
    }

    /**
     * Pull <= X amount of random MLS listings that have images available from the primary active feed
     *
     * @param int $limit
     * @return array
     */
    protected function getRandomMlsListings($limit)
    {
        if (!is_int($limit) || $limit <= 0) {
            throw new \InvalidArgumentException('You must provide a limit when requesting demo listings.');
        }
        $this->idxFactory->switchFeed($this->settings->IDX_FEED);
        $idx = $this->idxFactory->getIdx($this->settings->IDX_FEED);
        $idx_db = $this->idxFactory->getDatabase($this->settings->IDX_FEED);
        $queryString = sprintf(
            'SELECT %s '
            . ' FROM %s '
            . ' WHERE `ListingImage` != \'\' '
            . ' AND `ListingImage` IS NOT NULL '
            . ' AND `ListingPrice` >= 50000 '
            . ' ORDER BY RAND() '
            . ' LIMIT %s '
            . ';',
            $idx->selectColumns('', self::MLS_LISTING_FETCH_FIELDS),
            $idx->getTable(),
            (int) $limit
        );
        $listings = [];
        $results = $idx_db->query($queryString);
        while ($result = $idx_db->fetchArray($results)) {
            $listings[] = array_merge(
                ['idx' => $idx->getName()],
                $result
            );
        }
        return $listings;
    }

    /**
     * @param $mls_number
     * @param $type
     * @param $idx
     * @param $comment
     * @return string
     */
    protected function buildUserFormsRow($form_type, $mls_number, $listing_type, $feed, $comment)
    {
        return sprintf(
            ' (:lead_id, \'%s\', \'a:5:{s:4:"form";s:%s:"%s";s:10:"ListingMLS";s:%s:"%s";s:11:"ListingType";s:%s:"%s";s:11:"ListingFeed";s:%s:"%s";s:8:"comments";s:%s:"%s";}\', NULL, NOW()), ',
            $form_type,
            strlen($form_type),
            $form_type,
            strlen($mls_number),
            $mls_number,
            strlen($listing_type),
            $listing_type,
            strlen($feed),
            $feed,
            strlen($comment),
            $comment
        );
    }

    /**
     * @param array $listings
     * @throws PDOException
     * @return string|null
     */
    protected function buildUserFormsQuery($listings)
    {
        if (count($listings) < self::NUM_FORM_SUBMISSIONS) {
            throw new \Exception('Failed to load enough MLS listings to generate dynamic seeder data.');
        } else {
            $form_details = [
                [
                    'type' => 'Property Showing',
                    'comment' => sprintf(
                        'I would like to request a showing of %s, %s, %s',
                        $listings[0]['Address'],
                        $listings[0]['AddressCity'],
                        $listings[0]['AddressState']
                    )
                ],
                [
                    'type' => 'Property Showing',
                    'comment' => 'Please call me, I would like to see this listing in person on Thursday.'
                ],
                [
                    'type' => 'Quick Showing',
                    'comment' => 'Is this property available for viewing? I am available this Sunday.'
                ],
                [
                    'type' => 'Quick Showing',
                    'comment' => sprintf(
                        'I am available to look at %s on Friday. Can this be arranged?',
                        $listings[3]['Address']
                    )
                ],
                [
                    'type' => 'Quick Inquire',
                    'comment' => sprintf(
                        'Please send me more information regarding %s (MLS #%s)',
                        $listings[4]['Address'],
                        $listings[4]['ListingMLS']
                    )
                ],
                [
                    'type' => 'Quick Inquire',
                    'comment' => sprintf(
                        'This is a great listing. Can you send me more homes in %s?',
                        $listings[5]['AddressCity']
                    )
                ],
                [
                    'type' => 'Quick Showing',
                    'comment' => sprintf(
                        'I want to learn more about %s, %s, %s',
                        $listings[6]['Address'],
                        $listings[6]['AddressCity'],
                        $listings[6]['AddressState']
                    )
                ],
                [
                    'type' => 'IDX Inquiry',
                    'comment' => sprintf(
                        'Please call me. I have a serious offer to put on %s.',
                        $listings[7]['Address']
                    )
                ],
            ];

            $sql = [];
            $sql[] = "INSERT INTO `users_forms` (`user_id`, `form`, `data`, `page`, `timestamp`) VALUES ";

            for ($i = 0; $i < self::NUM_FORM_SUBMISSIONS; $i++) {
                if ($i >= count($form_details)) {
                    $form_details[$i]['type'] = self::DEFAULT_FORM_SUBMISSION_TYPE;
                    $form_details[$i]['comment'] = 'Test comment!';
                }
                $sql[] = $this->buildUserFormsRow(
                    $form_details[$i]['type'],
                    $listings[$i]['ListingMLS'],
                    $listings[$i]['ListingType'],
                    $listings[$i]['idx'],
                    $form_details[$i]['comment']
                );
            }

            $sql[] = " (:lead_id, 'Seller Form', 'a:6:{s:4:\"form\";s:11:\"Seller Form\";s:8:\"comments\";s:74:\"I want to sell my home: 35200 CATHEDRAL CANYON DR V173, Cathedral City, CA\";s:7:\"fm-addr\";s:30:\"35200 CATHEDRAL CANYON DR V173\";s:7:\"fm-town\";s:14:\"Cathedral City\";s:8:\"fm-state\";s:2:\"CA\";s:11:\"fm-postcode\";s:5:\"92234\";}', NULL, NOW() - INTERVAL 8 HOUR), ";
            $sql[] = " (:lead_id, 'Seller Form', 'a:6:{s:4:\"form\";s:11:\"Seller Form\";s:8:\"comments\";s:52:\"I have a property in Los Angeles (City), CA to sell.\";s:7:\"fm-addr\";s:15:\"11346 REGENT ST\";s:7:\"fm-town\";s:18:\"Los Angeles (City)\";s:8:\"fm-state\";s:2:\"CA\";s:11:\"fm-postcode\";s:5:\"90066\";}', NULL, NOW() - INTERVAL 9 HOUR), ";
            $sql[] = " (:lead_id, 'CMA Form', 'a:6:{s:4:\"form\";s:8:\"CMA Form\";s:8:\"comments\";s:54:\"Moving next month and need to sell my home by November\";s:7:\"fm-addr\";s:25:\"23618 MALIBU COLONY RD 56\";s:7:\"fm-town\";s:6:\"Malibu\";s:8:\"fm-state\";s:2:\"CA\";s:11:\"fm-postcode\";s:5:\"90265\";}', NULL, NOW() - INTERVAL 10 HOUR), ";
            $sql[] = " (:lead_id, 'CMA Form', 'a:6:{s:4:\"form\";s:8:\"CMA Form\";s:8:\"comments\";s:49:\"I am looking for a listing agent to sell my home.\";s:7:\"fm-addr\";s:16:\"4799 ROCK ROW DR\";s:7:\"fm-town\";s:18:\"Los Angeles (City)\";s:8:\"fm-state\";s:2:\"CA\";s:11:\"fm-postcode\";s:5:\"90041\";}', NULL, NOW() - INTERVAL 11 HOUR) ";
            $sql[] = ";";

            $sql_string = implode(' ', $sql);
        }

        return $sql_string ?: null;
    }

}