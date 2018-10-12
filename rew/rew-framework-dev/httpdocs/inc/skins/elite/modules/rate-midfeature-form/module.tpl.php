<?php if (!empty($guaranteed_sold_form)) { ?>

    <div id="sub-quicksearch" class="guaranteed">
        <div class="wrap">
            <form id="<?= $this->getUID() ; ?>" class="idx-search grid_12">
                <input class="hidden" name="email" value="" autocomplete="off">
                <div class="guaranteed-sale-cta">
                    <div class="gs-bubble">
                        <div class="wrap">
                            <h3 class="icon-home"><strong>Your Home Sold</strong><em>Guaranteed!</em></h3>
                        </div>
                    </div>
                    <div class="guaranteed-sale-form">
                        <input name="search_address" class="gs-address" placeholder="<?=Format::htmlspecialchars($address_placeholder); ?>" value="<?= Format::htmlspecialchars($user_location); ?>" required>
                        <input type="email" name="mi0moecs" class="gs-email" value="<?=Format::htmlspecialchars($email_value); ?>" placeholder="<?=Format::htmlspecialchars($email_placeholder); ?>" required>
                        <button class="search-button" type="submit"><?=Format::htmlspecialchars($submit_button); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="clearfix"></div>

<?php } ?>
