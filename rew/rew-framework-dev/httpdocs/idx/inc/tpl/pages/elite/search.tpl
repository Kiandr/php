<?php
// more than one snippet on a page?
global $idxSnippetAlreadyIncluded;

$error = '';
$alert = '';
$snippet_sufix = $idxSnippetAlreadyIncluded ?: '';

// Check modules
$conflictingModuleLoaded = false;
$conflictingModules = array('cms-listings', 'idx-featured-search', 'agents');
foreach ($conflictingModules as $conflictingModule) {
    $conflictingModuleLoaded = $page->moduleLoaded($conflictingModule);
    if ($conflictingModuleLoaded) break;
}

// Search results javascript

// Include search message
include $page->locateTemplate('idx', 'misc', 'search-message');

if (empty($results)) {
    $error = 'No listings were found matching your search criteria.';
} else {
    $page->info('class', 'idx-data');

    if (empty($_REQUEST['snippet'])) {
        $alert = '<strong>' . number_format($search_results_count['total']) . ' Properties Found.</strong> ';
        $alert .= '<span>Showing Page ' . number_format($pagination['page']) . ' of ' . number_format($pagination['pages']) . '</span>';
        if (!empty($_COMPLIANCE['limit']) && $search_results_count['total'] > $_COMPLIANCE['limit']) {
            $error = 'Only ' . number_format($_COMPLIANCE['limit']) . ' properties may be displayed per search. To see all of your results, try narrowing your search criteria.';
        }
    }

}

// Tags are not set to be hidden
if ((empty($_REQUEST['hide_tags'])) && $this->config('hideTags') !== true) {
    // Search search tags
    $idx_tags = IDX_Panel::tags();
}
?>

<a id="homes-for-sale<?= $snippet_sufix ?>"></a>

<?php if (!empty($saved_search)) { ?>
    <div class="uk-panel uk-panel-box uk-margin-top">
        <p>
            <?php if (!empty($_REQUEST['edit_search'])) { ?>
                <strong>Editing saved search:</strong> <?=Format::htmlspecialchars($saved_search['title']); ?>
                <button type="button" class="uk-button uk-button-primary uk-button-small uk-float-right save-search">Save Changes</button>
                <button type="button" class="uk-button uk-button-primary uk-button-small uk-float-right save-search-email">Save and Email Results</button>
            <?php } else { ?>
                <strong>Viewing saved search:</strong> <?=Format::htmlspecialchars($saved_search['title']); ?>
                <a href="?edit_search=true&saved_search_id=<?=$saved_search['id']; ?>" class="uk-button uk-button-primary uk-button-small uk-float-right">Edit Search</a>
            <?php } ?>
        </p>
    </div>
    <?php if (!empty($_REQUEST['edit_search'])) { ?>
        <?php $search_title = isset($_REQUEST['search_title']) ? $_REQUEST['search_title'] : $saved_search['title']; ?>
        <?php $search_frequency = isset($_REQUEST['frequency']) ? $_REQUEST['frequency'] : $saved_search['frequency']; ?>
        <div class="uk-container uk-margin">
            <form class="uk-form uk-form-stacked" action="?submit" method="get">
                <input type="hidden" name="saved_search_id" value="<?=Format::htmlspecialchars($saved_search['id']); ?>">
                <?php if (!empty($backend_user) && !empty($lead)) { ?>
                    <input type="hidden" name="lead_id" value="<?=Format::htmlspecialchars($lead['id']); ?>">
                <?php } ?>
                <input type="hidden" name="edit_search" value="true">
                <div class="uk-grid uk-grid-small">
                    <div class="uk-width-1-1 uk-width-medium-1-2 uk-margin-bottom">
                        <label class="uk-form-label">Search Title</label>
                        <div class="uk-form-controls">
                            <input class="uk-width-1-1 uk-form-large" name="search_title" value="<?=Format::htmlspecialchars($search_title); ?>" required>
                        </div>
                     </div>
                     <div class="uk-width-1-1 uk-width-medium-1-2 uk-margin-bottom">
                        <label class="uk-form-label">Update Frequency</label>
                        <div class="uk-form-controls">
                            <select class="uk-width-1-1 uk-form-large" name="frequency">
                                <option value="never"<?=$search_frequency === 'never' ? ' selected' : ''; ?>>Never</option>
                                <option value="immediately"<?=$search_frequency === 'immediately' ? ' selected' : ''; ?>>Immediately</option>
                                <option value="daily"<?=$search_frequency === 'daily' ? ' selected' : ''; ?>>Daily</option>
                                <option value="weekly"<?=$search_frequency === 'weekly' ? ' selected' : ''; ?>>Weekly</option>
                                <option value="monthly"<?=$search_frequency === 'monthly' ? ' selected' : ''; ?>>Monthly</option>
                            </select>
                        </div>
                     </div>
                </div>
                <div class="uk-form-row uk-margin-bottom">
                    <button type="submit" class="uk-button uk-button-small save-search">Save Changes</button>
                    <button type="submit" class="uk-button uk-button-small save-search-email">Save and Email Results</button>
                    <a class="uk-button uk-button-small delete-search" data-search-id="<?=$saved_search['id']?>" data-redirect-to="/idx/">Delete</a>
                </div>
            </form>
        </div>
    <?php } ?>
<?php } ?>

<?php if ($alert) { ?>
    <div class="uk-panel uk-panel-box uk-margin-top">
        <p><?= $alert ?></p>
    </div>
<?php } ?>

<?php if ($error) { ?>
    <div class="uk-alert uk-alert-danger" data-uk-alert>
        <a href="" class="uk-alert-close uk-close"></a>
        <p><strong><?= $error ?></strong></p>
    </div>
<?php } ?>

<?php if (empty($idxSnippetAlreadyIncluded)) { ?>

    <!-- REFINE SEARCH MESSAGE -->
    <div class="adv-save-message uk-hidden uk-margin-top" data-save-over-limit>
        <i class="uk-icon uk-icon-justify uk-icon-exclamation-triangle"></i> Refine your search to less than 500 properties to save.
    </div>

    <!-- FILTERS -->
    <div class="idx-filters uk-margin-top" data-uk-margin>
        <button type="button" class="uk-button uk-margin-small-right js-advanced-search-trigger">Filters <i class="ion-ios-plus-empty uk-margin-small-left"></i></button>
        <?php foreach ($idx_tags ?: array() as $tag) { ?>
            <button class="uk-button uk-button-tertiary uk-margin-small-right idx-filter-remove js-idx-filter-remove-trigger"
                    data-idx-tag="<?= Format::htmlspecialchars(json_encode($tag->getField())); ?>"><?= Format::htmlspecialchars($tag->getTitle()); ?></button>
        <?php } ?>
        <?php if (empty($_REQUEST['snippet'])) { ?>
            <div class="uk-float-right">
                <button class="uk-button uk-button-secondary save-search" type="button">
                    <?php if (!empty($saved_search)) { ?>
                        Save Changes
                    <?php } else { ?>
                        Save this Search
                    <?php } ?>
                </button>
            </div>
            <?php if (!empty($saved_search)) { ?>
            <div class="uk-float-right">
                <button class="uk-button uk-button-secondary save-search-email" type="button">
                    Save and Email Results
                </button>
            </div>
            <?php } ?>
        <?php } ?>
    </div>

    <!-- MOBILE TOOLBAR -->
    <div class="uk-container uk-margin-top uk-margin-bottom uk-visible-small toolbar-mobile">
        <div class="uk-grid uk-clearfix uk-grid-divider">
            <?php if (Settings::getInstance()->MODULES['REW_IDX_MAPPING']) { ?>
                <div class="uk-width-1-3 uk-padding-remove uk-text-center">
                    <div class="uk-button-group">
                        <a class="uk-button uk-button-idx-toolbar" id="view-map" data-toggle-map><i class="uk-icon-map-marker"></i> <span>Map</span></a>
                    </div>
                </div>
            <?php } ?>
            <?php $useGrid = true;
            include $page->locateTemplate('idx', 'misc', 'search-controls');
            unset($useGrid); ?>
        </div>
    </div>

<?php
//View toggle removes office and disclaimer when compliance rule met
$id = 'id="%s"';
$grid_id = "";
$detailed_id = "";

if($_COMPLIANCE['hide_office_grid']) {
    $grid_id = sprintf($id, "gridViewOffice");
    $detailed_id = sprintf($id, "detailedViewOffice");

} elseif ($_COMPLIANCE['show_list_view']) {
    $grid_id = sprintf($id, "gridView");
    $detailed_id = sprintf($id, "detailedView");

}?>
    <!-- TOOLBAR -->
	<div class="toolbar uk-margin-top uk-margin-bottom uk-clearfix uk-hidden-small" id="idx-toolbar">
        <div class="uk-button-group">
            <div <?=$grid_id; ?>><a<?= Skin_ELITE::buildSearchAttributesForView($view, array('grid' => 'uk-button selected', 'detailed' => 'uk-button')); ?>
                id="view-grid" href="#grid"><i class="uk-icon-th uk-icon-justify"></i> <span>Grid</span></a></div>
            <div <?=$detailed_id; ?>><a<?= Skin_ELITE::buildSearchAttributesForView($view, array('grid' => 'uk-button', 'detailed' => 'uk-button selected')); ?>
                id="view-detailed" href="#detailed"><i class="uk-icon-th-list uk-icon-justify"></i> <span>List</span></a></div>
            <?php if (Settings::getInstance()->MODULES['REW_IDX_MAPPING']) { ?>
                <a class="uk-button <?= ($view == 'map') ? ' selected' : '' ?>" id="view-map" data-toggle-map><i
                        class="uk-icon-map-marker uk-icon-justify"></i> <span>Map</span></a>
            <?php } ?>
        </div>
        <?php $useGrid = false;
        include $page->locateTemplate('idx', 'misc', 'search-controls');
        unset($useGrid); ?>
        <?php if (empty($idxSnippetAlreadyIncluded)) include $page->locateTemplate('idx', 'misc', 'pagination'); ?>
    </div>
    <?php $hideMap = true;
    if (Settings::getInstance()->MODULES['REW_IDX_MAPPING']) {
        require $page->locateTemplate('idx', 'misc', 'map-container');
    }
}
?>



<?php

// Display search results
if (!empty($results)) { ?>

<div id="search_summary<?= $snippet_sufix ?>"></div>

<div class="uk-grid uk-grid-medium" data-uk-grid-match>
    <?php
    foreach ($results as $index => $result) {
        include $result_tpl;
    }
    echo '</div>';
    }

    // Include Pagination
    if (empty($idxSnippetAlreadyIncluded) && !empty($pagination['links'])) { ?>
        <div class="idx-listings-pagination-bottom-wrapper uk-margin-top uk-clearfix">
            <div class="idx-listings-pagination-bottom" id="idx-listings-pagination-bottom">
                <?php include $page->locateTemplate('idx', 'misc', 'pagination'); ?>
            </div>
        </div>
    <?php } ?>

    <?php

    if (!empty($_COMPLIANCE['results']['show_immediately_below_listings'])) {
        \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer();
    }

    ?>
