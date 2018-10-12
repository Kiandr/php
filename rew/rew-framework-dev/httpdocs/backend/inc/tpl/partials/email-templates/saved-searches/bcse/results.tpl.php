<?php

/**
 * Saved Searches Email Template - Other Listings partial
 * @var array $style
 * @var array $listings
 */

?>
<!-- Contain -->
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td align="center" style="padding: 40px 0 20px 0;">

            <!-- Wrap -->
            <table class="wrap" border="0" cellpadding="0" cellspacing="0" width="600">
            <?
                while (!empty($row = array_splice($listings, 0, 2))) { ?>

                    <tr>
                        <td class="pb2">

                            <!-- 2 columns -->
                            <?php foreach ($row as $i => $listing) {
                                echo $this->render(__DIR__ . '/result.tpl.php', [
                                        "style" => $style,
                                        "listing" => $listing,
                                        "side" => ["left", "right"][$i]]);
                            } ?>

                        </td>
                    </tr>

                <?php } ?>

            </table>

        </td>
    </tr>
</table>