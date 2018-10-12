<?php
/**
 * Saved Searches Email Template  - Result partial
 * @var array $style
 * @var array $listing
 * @var string $side
 */

?>

<table class="wrap" border="0" cellpadding="0" cellspacing="0" width="47%" align="<?= $side; ?>">
    <tr>
        <td class="pb2">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td align="center">
                        <a href="<?= $listing["url_details"]; ?>" target="_blank" title="View listing details" style="text-decoration: none; color: <?= $style["body_text"]; ?>;">
                            <img src="<?= Format::thumbUrl($listing['ListingImage'], '540x400'); ?>" alt="Photo of <?= $listing["address"]; ?>" width="280" height="218" border="0" class="img" style="display: block; padding: 0; width: 100%; max-width:100%; color: <?= $style["body_text"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-weight: bold; font-size: 18px; text-align: center; background-color: #ffffff; -webkit-border-radius: 4px; border-radius: 4px;" />
                        </a>
                    </td>
                </tr>
                <tr>
                    <td align="left" style="padding: 30px 10px 0px 10px !important; color: <?= $style["body_text"]; ?>; font-family: <?= $style["font_stack"]; ?>; line-height: 28px; text-align: center;">
                        <a href="<?= $listing["url_details"]; ?>" target="_blank" title="View listing details" style="text-decoration: none; color: <?= $style["body_text"]; ?>;">
                            <h2 style="font-size: 22px; font-weight: normal; margin: 0 0 0 0;"><?= $listing["Address"]; ?>, <?= $listing["AddressCity"]; ?></h2>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td align="left" class="text-padding" style="padding: 10px 0 10px 0; color: <?= $style["body_text"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-weight: normal; font-size: 18px; line-height: 22px; text-align: center;">
                        <span>$<?=Format::number($listing['ListingPrice']); ?></span>
                    </td>
                </tr>
                <tr>
                    <td align="left" class="text-padding" style="color: <?= $style["body_text"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-weight: normal; font-size: 18px; line-height: 22px;">
                        <table align="left" style="width: 28%; text-align: center; padding-left: 10px;">
                            <tr>
                                <td style="padding-left: 10px;">
                                    <table align="center">
                                        <tr>
                                            <td><?=htmlspecialchars($listing['NumberOfBedrooms']) ?: 0; ?></td>
                                        </tr>
                                    </table>
                                    <table align="center">
                                        <tr>
                                            <td>BEDS</td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="color: #cccccc; padding: 10px 4px 0 4px;">|</td>
                            </tr>
                        </table>
                        <table align="left" style="width: 28%; text-align: center; padding-left: 10px;">
                            <tr>
                                <td style="padding-left: 10px;">
                                    <table align="center">
                                        <tr>
                                            <td><?=Format::fraction($listing['NumberOfBathrooms']) ?: 0; ?></td>
                                        </tr>
                                    </table>
                                    <table align="center">
                                        <tr>
                                            <td>BATHS</td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="color: #cccccc; padding: 10px 4px 0 4px;">|</td>
                            </tr>
                        </table>
                        <table align="left" style="width: 28%; text-align: center; padding-left: 10px;">
                            <tr>
                                <td style="padding-left: 10px;">
                                    <table align="center">
                                        <tr>
                                            <td><?=Format::number($listing['NumberOfSqFt']) ?: 0; ?></td>
                                        </tr>
                                    </table>
                                    <table align="center">
                                        <tr>
                                            <td>SQFT</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="border: 1px solid transparent;">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
                            <tr>
                                <td align="center" style="padding: 20px 0 20px 0;" class="plr1">
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                        <tr>
                                            <td align="center" bgcolor="<?= $style["button_bg"]; ?>" style="-webkit-border-radius: 3px; border-radius: 3px; background-color: <?= $style["button_bg"]; ?>;">
                                                <a href="<?= $listing["url_details"]; ?>" target="_blank" title="View listing details" style="padding: 12px 18px 12px 18px; display: block; font-size: 16px; font-family: <?= $style["font_stack"]; ?>; font-weight: normal; color: #ffffff; text-decoration: none;">More Details</a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>