<?php
$sortorders = IDX_Builder::getSortOptions(false);
$width = $idx->hasMappingData() ? 3 : 2;
if (!isset($useGrid)) {
    $useGrid = true;
}
// Sort Options
?>
<?php if ($useGrid) { ?>
    <div class="uk-width-1-<?= $width; ?> uk-padding-remove uk-text-center">
<?php } ?>
        <div class="uk-button-group">
            <div data-uk-dropdown="{mode:'click'}">
                <button class="uk-button uk-button-dark-grey" id="view-sort"><i class="uk-icon-exchange rotate-90"></i> <span>Sort</span> <i class="ion-ios-arrow-down uk-margin-left uk-hidden-small"></i></button>
                <div class="uk-dropdown uk-dropdown-small">
                    <ul class="uk-nav uk-nav-dropdown">
                        <?php foreach ($sortorders as $sortorder) { ?>
                            <?php $class = ($_REQUEST['sortorder'] == $sortorder['value']) ? 'item-active' : ''; ?>
                            <?php $value = '?' . Format::htmlspecialchars(http_build_query(array_merge($querystring_nosort, array('sortorder' => $sortorder['value'])))); ?>
                            <li><a class="<?= $class; ?>" href="<?= Format::htmlspecialchars($value); ?>"><?= Format::htmlspecialchars($sortorder['title']); ?></a></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
<?php if ($useGrid) { ?>
    </div>
    <div class="uk-width-1-<?= $width; ?> uk-padding-remove uk-text-center">
<?php } ?>
	<div class="uk-button-group">
			<a class="uk-button js-advanced-search-trigger"><i class="uk-icon-sliders"></i> <span class="uk-visible-large">Refine</span> <span>Search</span></a>
	</div>
<?php if ($useGrid) { ?>
    </div>
<?php } ?>
