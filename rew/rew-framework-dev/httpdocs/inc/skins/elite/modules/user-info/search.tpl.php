<div class="filter-saves uk-clearfix uk-margin-small-top">
    <?php if ($favorites = $count('favorites')) { ?>
        <span class="save" data-dashboard="favorites"><span><?= Format::number($favorites); ?></span> <?= Locale::spell('Favorites'); ?></span>
    <?php } ?>
    <?php if ($searches = $count('saved_searches')) { ?>
        <span class="save" data-dashboard="searches"><span><?= Format::number($searches); ?></span> <?= Locale::spell('Searches'); ?></span>
    <?php } ?>
    <div class="save-search-controls">
        <div class="adv-save-message uk-margin-top uk-margin-bottom" data-save-over-limit>
            <i class="uk-icon uk-icon-justify uk-icon-exclamation-triangle"></i> Refine your search to less than 500 properties to save.
        </div>
        <button type="button" class="uk-button uk-button-blank uk-button-block uk-margin-small-bottom save-search">Save This Search</button>
        <a class="uk-button uk-button-blank uk-button-block delete-search" data-search-id="<?=$_REQUEST['saved_search_id']?>" data-redirect-to="/idx/"><i class="uk-icon uk-icon-trash"></i> <span>Delete This Search</span></a>
    </div>
</div>