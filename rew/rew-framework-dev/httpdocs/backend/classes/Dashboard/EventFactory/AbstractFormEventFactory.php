<?php

namespace REW\Backend\Dashboard\EventFactory;

use REW\Backend\Dashboard\AbstractEventFactory;
use REW\Backend\Dashboard\Interfaces\EventIdInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\FormatInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\CacheInterface;
use \Util_IDX;
use \Exception;

/**
 * Class RegistrationEventFactory
 *
 * @category Dashboard
 * @package  REW\Backend\Dashboard
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
abstract class AbstractFormEventFactory extends AbstractEventFactory
{

    /**
     * IDX Factory
     * @var IDXFactory
     */
    protected $idxFactory;


    /**
     * Create AbstractFormEventFactory
     * @param DBInterface $db
     * @param AuthInterface $auth
     * @param SettingsInterface $settings
     * @param FormatInterface $format
     * @param CacheInterface $cache
     * @param IDXFactoryInterface $idxFactory
     */
    public function __construct(
        DBInterface $db,
        AuthInterface $auth,
        SettingsInterface $settings,
        FormatInterface $format,
        CacheInterface $cache,
        IDXFactoryInterface $idxFactory
    ) {
        parent::__construct($db, $auth, $settings, $format, $cache);
        $this->idxFactory = $idxFactory;
    }

    /**
     * Query Events
     * @param EventIdInterface $eventId
     * @return array|null
     */
    protected function queryEvent(EventIdInterface $eventId)
    {

        // Query Event Data
        try {
            $dataQuery = $this->db->prepare(
                'SELECT `u`.`id` AS \'user_id\', `u`.`first_name`, `u`.`last_name`, `u`.`status`, `u`.`email`, `u`.`phone_cell`, `u`.`image`, `u`.`agent`,'
                . ' `uf`.`timestamp`, `uf`.`id` AS \'form_id\', `uf`.`form`, `uf`.`data`'
                . ' FROM ' . LM_TABLE_FORMS . ' `uf`'
                . ' JOIN ' . LM_TABLE_LEADS . ' `u` ON `u`.`id` = `uf`.`user_id`'
                . ' WHERE `uf`.`id` = :id'
            );
            $dataQuery->execute(['id' => $eventId->getId()]);
            return $dataQuery->fetch();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get Event Listing
     * @param string $mls
     * @param string|null $type
     * @param string|null $feed
     * @return array|null
     */
    protected function getListing($mls, $type = null, $feed = null)
    {


        try {
            // Switch
            if ($feed) {
                $this->idxFactory->switchFeed($feed);
            }

            // IDX objects
            $idx = $this->idxFactory->getIdx();
            $db_idx = $this->idxFactory->getDatabase();

            // Query Listing
            $listing = $this->queryListing($idx, $db_idx, $mls, $type);

            // Parse Listing
            if (!empty($listing)) {
                return $this->parseListing($idx, $db_idx, $listing);
            } else {
                return null;
            }
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Query Event Listing
     * @param $idx
     * @param $db_idx
     * @param string $mls
     * @param string $type
     * @return array
     */
    protected function queryListing($idx, $db_idx, $mls, $type)
    {

        // Find Listing from Link
        return $db_idx->fetchQuery(
            "SELECT SQL_CACHE " . $idx->selectColumns()
            . " FROM `" . $idx->getTable() . "`"
            . " WHERE `" . $idx->field('ListingMLS') . "` = '" . $db_idx->cleanInput($mls) . "'"
            . (!empty($type) ? " AND `" . $idx->field('ListingType') . "` = '" . $db_idx->cleanInput($type) . "'" : "")
            . " LIMIT 1;"
        );
    }


    /**
     * Parse Form Data
     * @param int $id
     * @param string $formName
     * @param array $form
     * @return array
     */
    protected function parseFormData($id, $formName, array $form = [])
    {

        // Required Form Data
        $data = [
            'id'         => $id,
            'name'       => $formName,
            'mls_number' => null,
            'type'       => null,
            'feed'       => null
        ];

        // Build Address
        if (!empty($form['fm-addr'])) {
            $data['address'] = $form['fm-addr'];
        } else if (!empty($form['address'])) {
            $data['address'] = $form['address'];
        } else {
            $address = [];
            if (!empty($form['fm-addr'])) {
                $address []= $form['fm-addr'];
            }
            if (!empty($form['fm-town'])) {
                $address []= $form['fm-town'];
            }
            if (!empty($form['fm-state'])) {
                $address []= $form['fm-state'];
            }
            if (!empty($form['fm-postcode'])) {
                $address []= $form['fm-postcode'];
            }
            $data['address'] = implode(', ', $address);
        }

        // Build Listing Data
        if (!empty($form['mls_number'])) {
            $data['mls_number'] = $form['mls_number'];
        }
        if (!isset($data['mls_number']) && !empty($form['ListingMLS'])) {
            $data['mls_number'] = $form['ListingMLS'];
        }
        if (!empty($form['ListingType'])) {
            $data['type'] = $form['ListingType'];
        }
        if (!empty($form['ListingFeed'])) {
            $data['feed'] = $form['ListingFeed'];
        }

        // Build Move Data
        if (!empty($form['move_when'])) {
            $data['move_when'] = $form['move_when'];
        }
        if (!empty($form['when_sell'])) {
            $data['move_when'] = str_replace('_', ' ', $form['when_sell']);
        }
        if (empty($data['move_when']) && !empty($form['move_when'])) {
            $data['move_when'] = $form['when_sell'];
        }
        if (empty($data['move_when']) && !empty($form['move_when'])) {
            $data['move_when'] = $form['when_do_you_plan_to_sell'];
        }

        if (!empty($form['price'])) {
            $data['price'] = '$' . $this->format->number($form['price']);
        }
        if (!empty($form['price_range'])) {
            $data['price_range'] = $form['price_range'];
        }
        if (!empty($form['fm-town'])) {
            $data['city'] = $form['fm-town'];
        }
        if (!empty($form['fm-state'])) {
            $data['state'] = $form['fm-state'];
        }
        if (!empty($form['fm-postcode'])) {
            $data['zip'] = $form['fm-postcode'];
        }
        if (!empty($form['bedrooms'])) {
            $data['bedrooms'] = $form['bedrooms'];
        }
        if (!empty($form['bathrooms'])) {
            $data['bathrooms'] = $form['bathrooms'];
        }
        if (!empty($form['square_feet'])) {
            $data['square_feet'] = $form['square_feet'];
        }
        if (!empty($form['comments'])) {
            $data['comments'] = $form['comments'];
        }
        if (!isset($data['mls_number']) && !empty($form['showing']['comments'])) {
            $data['mls_number'] = $form['showing']['comments'];
        }
        return $data;
    }

    /**
     * Parse Event Listing
     * @param Database_MySQLImproved $idx
     * @param IDXInterface $db_idx
     * @param array $listing
     * @return array
     */
    protected function parseListing($idx, $db_idx, array $listing)
    {

        $listing = Util_IDX::parseListing($idx, $db_idx, $listing);
        $name = trim(implode(', ', [$listing['Address'], $listing['AddressCity'], $listing['AddressState']]));
        return [
            'id' => $listing['id'],
            'mls' => $listing['ListingMLS'],
            'name' => $name,
            'address' => $listing['Address'],
            'city' => $listing['AddressCity'],
            'state' => $listing['AddressState'],
            'image' => $listing['ListingImage'],
            'link' => $listing['url_details']
        ];
    }
}
