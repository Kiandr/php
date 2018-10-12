<?php

/**
 * Saved Searches Email Template - Other Listings partial
 * @var array $style
 * @var array $listings
 */

?>
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="columns-table"
       style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background-color: <?= $style["body_bg"]; ?>; border-collapse: collapse !important; border-top: none; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
    <tbody>
<?
while (!empty($row = array_splice($listings, 0, 2))) { ?>

        <tr>
            <td align="center" valign="top"
                style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                <?php foreach ($row as $i => $listing) {
                    echo $this->render(__DIR__ . '/result.tpl.php', [
                            "style" => $style,
                            "listing" => $listing,
                            "side" => ["left", "right"][$i]]);
                } ?>
            </td>
        </tr>

<?php } ?>
</tbody>
    </table><!-- /.columns-table -->
