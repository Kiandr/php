<?php if (empty($showForm)) return; ?>
<div class="uk-width-small-1-1 uk-width-medium-1-1 uk-width-large-1-1 uk-width-xlarge-7-10" id="<?=$this->getUID(); ?>">
    <form id="home-valuation" class="uk-form uk-hidden-small" action="<?=Format::htmlspecialchars($formAction); ?>" method="GET">
        <input type="hidden" name="feed" value="<?=Format::htmlspecialchars($idxFeed); ?>">
        <input class="hidden" name="email" type="email" value="" autocomplete="off">
        <div class="uk-grid uk-grid-collapse">
            <div class="uk-width-5-10">
                <input class="uk-width-1-1 js-ac-google-places" type="text" name="adr" autocomplete="off" placeholder="<?=Format::htmlspecialchars($addressPlaceholder); ?>" value="<?=Format::htmlspecialchars($addressValue); ?>" required>
            </div>
            <div class="uk-width-3-10">
                <input class="uk-width-1-1 js-email" type="email" name="mi0moecs" autocomplete="email" placeholder="<?=Format::htmlspecialchars($emailPlaceholder); ?>" value="<?=Format::htmlspecialchars($emailValue); ?>" required>
            </div>
            <div class="uk-width-2-10">
                <button class="uk-button" type="submit"><?=Format::htmlspecialchars($buttonText); ?></button>
            </div>
        </div>
    </form>
    <a href="<?=Format::htmlspecialchars($formAction); ?>" class="uk-button uk-visible-small"><?=Format::htmlspecialchars($buttonText); ?></a>
    <div class="uk-hidden uk-alert js-places-message"></div>
</div>