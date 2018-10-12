<?php

/**
 * Backend_Mailer_ListingRecommendation
 *
 * @package Backend
 */
class Backend_Mailer_ListingRecommendation extends Backend_Mailer
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

        // MLS Listing
        $listing = $this->data['listing'];

        // Generate Subject
        return 'Recommended Listing: ' . $listing['Address'] . ' (' . Lang::write('MLS_NUMBER') . $listing['ListingMLS'] . ')';
    }

    /**
     * Generate HTML Email Message to Send
     *
     * @param array Tags
     * @return string Email message to be sent
     */
    public function getMessage(&$tags = array())
    {

        global $_COMPLIANCE;

        // MLS Listing
        $listing = $this->data['listing'];

        // Listing Preview HTML
        ob_start();

?>
<!-- MLS Listing -->
<table width="625" cellpadding="5" cellspacing="0" style="border: 1px solid #ccc; background-color: #fff;">
    <tr valign="top">
        <td width="200">
            <a href="<?=$listing['url_details']; ?>"><img src="<?=IDX_Feed::thumbUrl($listing['ListingImage'], IDX_Feed::IMAGE_SIZE_SMALL); ?>" alt="" width="200" height="150" style="width: 200px; height: 150px" border="0" /></a>
        </td>
        <td width="425" style="vertical-align: top;">
            <div style="padding: 0 10px; font-size: 14px;">
                <?php if (!empty($_COMPLIANCE['results']['show_icon'])) : ?>
                    <span style="float: right;"><?=$_COMPLIANCE['results']['show_icon']; ?></span>
                <?php endif; ?>
                <i style="color: #777; font-size: 13px;"><?=Lang::write('MLS_NUMBER'); ?><?=$listing['ListingMLS']; ?></i><br />
                <b>$<?=Format::number($listing['ListingPrice']); ?></b><br />
                <?=$listing['NumberOfBedrooms']; ?> Bedrooms, <?=$listing['NumberOfBathrooms']; ?> Bathrooms<br />
                <?=ucwords(strtolower($listing['AddressCity'])); ?>, <?=ucwords(strtolower($listing['AddressState'])); ?>
                <?=!empty($_COMPLIANCE['results']['show_status']) ? '<br />Status: ' . $listing['ListingStatus'] : ''; ?>
                <p style="font-size: 12px; margin: 0px;"><?=substr(ucwords(strtolower($listing['ListingRemarks'])), 0, 125); ?>...</p>
                <a href="<?=$listing['url_details']; ?>" style="color: #333; font-size: 12px; font-family: georgia; font-style: italic; display: block; background: #ddd; padding: 2px; margin-top: 5px;">Read More &raquo;</a>
                <?php if (!empty($_COMPLIANCE['details']['lang']['provider_bold'])) { ?>
                    <?php if (!empty($_COMPLIANCE['results']['show_agent']) || !empty($_COMPLIANCE['results']['show_office'])) { ?>
                        <p style="font-size: 12px; font-weight: bold;"><?=!empty($_COMPLIANCE['results']['lang']['provider']) ? $_COMPLIANCE['results']['lang']['provider'] : 'Provided by:';?> <?=!empty($_COMPLIANCE['results']['show_agent']) && !empty($listing['ListingAgent']) ? $listing['ListingAgent'] : ''; ?><?=!empty($_COMPLIANCE['results']['show_office']) && !empty($listing['ListingOffice']) ? (!empty($_COMPLIANCE['results']['show_agent']) ? ', ' : '') . $listing['ListingOffice'] : ''; ?></p>
                    <?php } ?>
                <?php } else { ?>
                    <?php if (!empty($_COMPLIANCE['results']['show_agent']) || !empty($_COMPLIANCE['results']['show_office'])) { ?>
                        <p style="font-size: 10px;"><?=!empty($_COMPLIANCE['results']['lang']['provider']) ? $_COMPLIANCE['results']['lang']['provider'] : 'Listing courtesy of';?> <?=!empty($_COMPLIANCE['results']['show_agent']) && !empty($listing['ListingAgent']) ? $listing['ListingAgent'] : ''; ?><?=!empty($_COMPLIANCE['results']['show_office']) && !empty($listing['ListingOffice']) ? (!empty($_COMPLIANCE['results']['show_agent']) && !empty($listing['ListingAgent']) ? ', ' : '') . $listing['ListingOffice'] : ''; ?></p>
                    <?php } ?>
                <?php } ?>
            </div>
        </td>
    </tr>
</table>
<?php
        \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(true);

        // Listing Preview HTML
        $listing['preview'] = ob_get_clean();

        // Agent's Message
        $message = trim($this->data['message']);

        // Sender
        $sender = $this->getSender();

        // Generate Message
        $this->message  = '<p><b>' . Format::htmlspecialchars($sender['name']) . '</b> thinks you\'ll like this ' . Lang::write('MLS') . ' Listing:</p>' . PHP_EOL;
        $this->message .= '<p>' . $listing['preview'] . '</p>' . PHP_EOL;
if (!empty($message)) {
    $this->message .= '<p>' . nl2br($message) . '</p>' . PHP_EOL;
}

        // Append Signature
        $signature = $this->getSignature();
if (!empty($signature) && !empty($this->append)) {
    $this->message .= '<p>' . $signature . '</p>' . PHP_EOL;
}

        // Return Message
        return $this->message;
    }
}
