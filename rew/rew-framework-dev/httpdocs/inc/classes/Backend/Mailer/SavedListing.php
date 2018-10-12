<?php

/**
 * Backend_Mailer_SavedListing extends Backend_Mailer and is used for sending 'Saved Favorite' notifications to an Agent when a lead saved a favorite listing.
 *
 * Here is an example on how to use this class:
 * <code>
 * <?php
 *
 * // Lead Row
 * $lead = array(
 *     'id'         => 1,
 *     'first_name' => 'Lead',
 *     'last_name'  => 'Name',
 *     'email'      => 'user@domain.com',
 *     'phone'      => '123-456-7890'
 * );
 *
 * // Listing Row
 * $listing = array(
 *     'ListingMLS'        => 'L234234',
 *     'Address'           => '1234 Street Name',
 *     'ListingPrice'      => 329000,
 *     'NumberOfBedrooms'  => 3,
 *     'NumberOfBathrooms' => 2,
 *     'NumberOfSqFt'      => 1950,
 *     'YearBuilt'         => 2002,
 *     'ListingStatus'     => 'Active',
 *     'ListingRemarks'    => 'Listing Remarks Here'
 * );
 *
 * // Setup Mailer
 * $mailer = new Backend_Mailer_SavedListing(array(
 *     'lead'    => $lead,
 *     'listing' => $listing
 * ));
 *
 * // Add Recipient
 * $mailer->setRecipient('agent@example.com', 'Agent Name');
 *
 * // Send Email
 * $mailer->Send();
 *
 * ?>
 * </code>
 * @package Backend
 */
class Backend_Mailer_SavedListing extends Backend_Mailer
{

    /**
     * Get Email Subject to Send
     *
     * @return string Email Subject
     */
    public function getSubject()
    {

        // Subject Already Set
        if (!empty($this->subject)) {
            return $this->subject;
        }

        // Saved Favorite
        $listing = $this->data['listing'];

        // Default Subject
        return 'Saved ' . Locale::spell('Favorite') . ' Notification - ' . Lang::write('MLS_NUMBER') . $listing['ListingMLS'];
    }

    /**
     * Generate HTML Email Message to Send
     *
     * @return string Email message to be sent
     */
    public function getMessage()
    {

        // Message Already Set
        if (!empty($this->message)) {
            return $this->message;
        }

        // Rejected Lead
        $lead = $this->data['lead'];
        $lead = new Backend_Lead($lead);
        $leadlink = $lead->getName();

        // Saved Favorite
        $listing = $this->data['listing'];

        // Message Body (HTML)
        $this->message  = '<p>Hello ' . Format::htmlspecialchars($this->recipient['name']) . ',</p>' . PHP_EOL;
        $this->message .= '<p>You have received this notification because <a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/summary/?id=' . $lead['id'] . '">' . Format::htmlspecialchars($lead->getNameOrEmail()) . '</a> has added a listing to their Saved ' . Locale::spell('Favorites') . ': <a href="' . $listing['url_details'] . '">' . Lang::write('MLS_NUMBER') . $listing['ListingMLS'] . '</a></p>' . PHP_EOL;
        $this->message .= '<========================>' . '<br>' . PHP_EOL;
        if (!empty($leadlink)) {
            $this->message .= '<strong>Name:</strong> <a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/summary/?id=' . $lead['id'] . '">' . Format::htmlspecialchars($lead->getName()) . '</a><br>' . PHP_EOL;
        }
        $this->message .= '<strong>Email:</strong> <a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/email/?id=' . $lead['id'] . '">' . $lead['email'] . '</a><br>' . PHP_EOL;
        if (!empty($lead['phone'])) {
            $this->message .= '<strong>Phone:</strong> ' . $lead['phone'] . '<br>' . PHP_EOL;
        }
        if (!empty($listing['Address'])) {
            $this->message .= '<strong>Address:</strong> ' . $listing['Address'] . '<br>' . PHP_EOL;
        }
        if (!empty($listing['ListingPrice'])) {
            $this->message .= '<strong>Price:</strong> $' . Format::number($listing['ListingPrice']) . '<br>' . PHP_EOL;
        }
        if (!empty($listing['NumberOfBedrooms'])) {
            $this->message .= '<strong>Bedrooms:</strong> ' . Format::number($listing['NumberOfBedrooms']) . '<br>' . PHP_EOL;
        }
        if (!empty($listing['NumberOfBathrooms'])) {
            $this->message .= '<strong>Bathrooms:</strong> ' . Format::fraction($listing['NumberOfBathrooms']) . '<br>' . PHP_EOL;
        }
        if (!empty($listing['NumberOfSqFt'])) {
            $this->message .= '<strong>Square Feet:</strong> ' . Format::number($listing['NumberOfSqFt']) . '<br>' . PHP_EOL;
        }
        if (!empty($listing['YearBuilt'])) {
            $this->message .= '<strong>Year Built:</strong> ' . $listing['YearBuilt'] . '<br>' . PHP_EOL;
        }
        if (!empty($listing['ListingStatus'])) {
            $this->message .= '<strong>Status:</strong> ' . $listing['ListingStatus'] . '<br>' . PHP_EOL;
        }
        if (!empty($listing['ListingRemarks'])) {
            $this->message .= '<strong>Remarks:</strong> ' . $listing['ListingRemarks'] . '<br>' . PHP_EOL;
        }
        $this->message .= '<========================>' . PHP_EOL;
        $this->message .= '<p><a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/edit/?id=' . $lead['id'] . '">Click here</a> to change notification settings.</p>' . PHP_EOL;
        $this->message .= '<p>Have a nice day!</p>';

        // Return Message
        return $this->message;
    }
}
