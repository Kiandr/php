<a id="homes-for-sale"></a>
<?php

// Is this the 2nd snippet on a page?
global $idxSnippetAlreadyIncluded;

// Check modules
$conflictingModuleLoaded = false;
$conflictingModules = array('cms-listings', 'idx-featured-search', 'agents');
foreach ($conflictingModules as $conflictingModule) {
	$conflictingModuleLoaded = $page->moduleLoaded($conflictingModule);
	if ($conflictingModuleLoaded) break;
}

// Map container
echo '<div id="listings-map-' . $idx->getLink() . '" class="hidden listings-map"></div>';

// This is an IDX snippet
if (!empty($_REQUEST['snippet']) && empty($idxSnippetAlreadyIncluded)) {

	// Don't show if 2col template
	$template = ($template = $page->getTemplate()) ? $template->getName() : false;
	$container = $template === '2col' ? 'sub-feature' : 'idx-snippet';
	$display = $container === 'idx-snippet';

	// Refine search tools
	if (empty($conflictingModuleLoaded)) {
		$this->container($container)->module('idx-search', array(
			'className' => 'snippet-search',
			'button' => 'Update',
			'advanced' => true
		))->display($display);
	}

}

// Search results javascript
$page->addJavascript('js/idx/results.js', 'page');

// Include search message
include $page->locateTemplate('idx', 'misc', 'search-message');

?>
<?php if (empty($_REQUEST['snippet'])) { ?>

	<div class="msg vanilla results">
		<?php if (!empty($results)) { ?>
			<span class="summary">
				<em><?=number_format($search_results_count['total']); ?> Properties Found.
				<?php if (!isset($_GET['auto_save']) && ($search_results_count['total'] > 500) && (empty($saved_search) || (!empty($saved_search) && !empty($lead)))) {
					if (!empty($_COMPLIANCE['limit'])) {
						echo "(Refine search to less than " . $_COMPLIANCE['limit'] . " results to save)";
					} else {
						echo "(Refine search to less than 500 results to save)";
					}
				} ?>
				</em> Showing Page <?=number_format($pagination['page']); ?> of <?=number_format($pagination['pages']); ?>
			</span>
		<?php } ?>
		<span class="nav">
			<?php if (!empty($pagination['prev'])) { ?>
				<a href="<?=$pagination['prev']['url']; ?>" class="prev"><i class="icon-caret-left"></i> Prev</a>
			<?php } ?>
			<?php if (!empty($pagination['next'])) { ?>
				<a href="<?=$pagination['next']['url']; ?>" class="next">Next <i class="icon-caret-right"></i></a>
			<?php } ?>
		</span>
	</div>

	<?php if (!empty($_COMPLIANCE['limit']) && $search_results_count['total'] > $_COMPLIANCE['limit']) { ?>
		<p class="message">Only <?=number_format($_COMPLIANCE['limit']); ?> properties may be displayed per search. To see all of your results, try narrowing your search criteria.</p>
	<?php } ?>

<?php } 

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

<?php if (empty($idxSnippetAlreadyIncluded)) { ?>
    <div class="toolbar">
    	<div class="tabset pills mini">
		    <ul class="tabset">
		        <li<?=(empty($view) || $view == 'grid' || $view == 'map') ? ' class="current"' : ''; ?>><a rel="nofollow" href="#grid" class="view" title="View results in a grid" <?=$grid_id; ?>><i class="icon-th"></i></a>
		        <li<?=($view == 'detailed') ? ' class="current"' : ''; ?>><a rel="nofollow" href="#detailed" class="view"  title="View results in a detailed list" <?=$detailed_id; ?>><i class="icon-th-list"></i></a>
		    </ul>
	    </div>
        <?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING']))  { ?>
			<a rel="nofollow" href="#map" class="buttonstyle mini view<?=($view == 'map' || !empty($_REQUEST['map']['open'])) ? ' current' : ''; ?>" title="View listings on a map"><i class="icon-map-marker"></i> Map</a>
        <?php } ?>
		<?php include $page->locateTemplate('idx', 'misc', 'search-controls'); ?>
    </div>
<?php } elseif (empty($results)) { ?>
	<span class="summary">No listings were found matching your search criteria.</span>
<?php } ?>

<div id="search_summary"></div>

<?php

// No results found
if (empty($results)) {
	echo '<div class="msg"><p>No listings were found matching your search criteria.</p></div>';

} else {

	// Display search results
    echo '<div class="articleset listings ' . ($view == 'detailed' ? 'flowgrid_x1' : 'flowgrid') . '">';
	foreach ($results as $index => $result) {
		include $result_tpl;
	}
	echo '</div>';

}

// Include Pagination
if (empty($idxSnippetAlreadyIncluded)) include $page->locateTemplate('idx', 'misc', 'pagination');

if (!empty($_COMPLIANCE['results']['show_immediately_below_listings'])) {
    \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer();
}
