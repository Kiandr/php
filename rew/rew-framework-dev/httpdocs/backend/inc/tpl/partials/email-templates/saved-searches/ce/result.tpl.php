<?php
/**
 * Saved Searches Email Template  - Result partial
 * @var array $style
 * @var array $listing
 * @var string $side
 */

?>

<table border="0" cellpadding="0" cellspacing="0" align="<?= $side; ?>" width="290" class="table-column" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; border-collapse: collapse !important; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
    <tbody>
        <tr>
            <td class="table-column-td" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding-bottom: 20px; padding-<?= $side; ?>: 20px; padding-top: 20px;">
                <a href="<?= $listing["url_details"]; ?>" target="_blank" title="Opens new browser window" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
                    <img src="<?= Format::thumbUrl($listing['ListingImage'], '540x400'); ?>" width="270" height="200" border="0" alt="Photo of <?= $listing["address"]; ?>" class="listing-img" style="-ms-interpolation-mode: bicubic; border: 0; display: inline; height: 200px; line-height: 100%; max-width: 270px; outline: none; text-decoration: none; width: 270px;">
                </a>
            </td>
        </tr>
        <tr>
            <td height="67" valign="top" class="listing-address-td" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding-bottom: 0px; padding-<?= $side; ?>: 20px; padding-top: 0px; color: <?= $style["body_text"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-size: 14px; line-height: 150%; height: 67px;">
                <h2 style="color: <?= $style["body_text"]; ?> !important; display: block; font-family: <?= $style["font_stack"]; ?>; font-size: 22px; font-style: normal; font-weight: normal; letter-spacing: normal; line-height: 115%; margin-bottom: 10px; margin-left: 0; margin-right: 0; margin-top: 0; text-align: left;">
                    <a href="<?= $listing["url_details"]; ?>" target="_blank" title="Opens new browser window" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: <?= $style["body_text"]; ?>; text-decoration: none;">
                        <?= $listing["Address"]; ?>, <?= $listing["AddressCity"]; ?>
                    </a>
                </h2>
            </td>
        </tr>
        <tr>
            <td valign="bottom" class="table-column-td" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding-bottom: 20px; padding-<?= $side; ?>: 20px; padding-top: 0px; color: <?= $style["body_text"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-size: 14px; line-height: 150%;">
                <div class="listing-price" style="color: <?= $style["body_text"]; ?>; font-size: 20px; margin-bottom: 8px; margin-left: 0px; margin-right: 0px; margin-top: 4px;">
                    $<?=Format::number($listing['ListingPrice']); ?>
                </div>
                <div class="listing-details" style="color: <?= $style["body_text"]; ?>; font-size: 16px; margin-bottom: 12px; margin-left: 0px; margin-right: 0px; margin-top: 0px;">
                    <?=htmlspecialchars($listing['NumberOfBedrooms']) ?: 0; ?> Beds<span class="listing-details-divider" style="display: inline-block; padding-right: 4px; padding-left: 4px; color: #cbcbcb;">|</span><?=Format::fraction($listing['NumberOfBathrooms']) ?: 0; ?> Baths<span class="listing-details-divider" style="display: inline-block; padding-right: 4px; padding-left: 4px; color: #cbcbcb;">|</span><?=Format::number($listing['NumberOfSqFt']) ?: 0; ?> Sf
                </div>
                <div>
                    <!--[if mso]>
                        <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="" style="height:40px;v-text-anchor:middle;width:270px;color:<?=$style["button_text"]; ?> !important;" arcsize="<?=$style["arcsize"]; ?>" stroke="f" fillcolor="<?=$style["button_bg"]; ?>">
                        <w:anchorlock/>
                            <center>
                                <![endif]-->
                                    <a href="<?= $listing["url_details"]; ?>" target="_blank" title="Opens new browser window" class="btn-more-details" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: none; background-color: <?= $style["button_bg"]; ?>; border-radius: <?= $style["button_radius"]; ?>; color: <?= $style["button_text"]; ?> !important; display: inline-block; font-family: <?= $style["font_stack"]; ?>; font-size: 15px; font-weight: normal; line-height: 40px; text-align: center; text-decoration: none; width: 100%;">
                                        <font color="<?= $style["button_text"]; ?>">MORE DETAILS</font>
                                    </a>
                                <!--[if mso]>
                            </center>
                        </v:roundrect>
                    <![endif]-->
                </div>
            </td>
        </tr>
    </tbody>
</table><!-- /.table-column -->
