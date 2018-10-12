<?php

// Can save search
$max_save = 500;
$can_save = isset($_GET['auto_save']) || $search_results_count['total'] <= $max_save;

// Skin URL
$skinUrl = $page->getSkin()->getUrl();

// Search page's required javascript
$page->addJavascript('js/idx/search.js', 'page')
->addJavascript('var mapOptions = $.extend(true, mapOptions || {}, ' . json_encode(array(
	'manager' => array(
		'icon' => $skinUrl . '/img/map-ico.png',
		'iconWidth' => 21,
		'iconHeight' => 26,
	)
)) . ');', 'dynamic', false);

// This is an IDX snippet - include refine search bar
global $idxSnippetAlreadyIncluded;
if (empty($idxSnippetAlreadyIncluded)) {

	// Check conflicting modules
	$conflictingModuleLoaded = false;
	$conflictingModules = array('cms-listings', 'idx-featured-search', 'agents');
	foreach ($conflictingModules as $conflictingModule) {
		$conflictingModule = $page->moduleLoaded($conflictingModule);
		if ($conflictingModule) {
			$container = $conflictingModule->getContainer();
			$conflictingModuleLoaded = $container && $container->getID() !== 'navigation-feature';
			if (!empty($conflictingModuleLoaded)) break;
		}
	}

	// Load refine search tools
	if (empty($conflictingModuleLoaded)) {
		$this->container('idx-feature')->module('idx-search', array(
			'className'	=> 'snippet-search',
			'hideFeed'	=> !empty($_REQUEST['snippet']),
			'isSaved'	=> $saved_search,
			'canSave'	=> $can_save,
			'maxSave'	=> $max_save,
			'button'	=> 'Refine',
			'advanced'	=> true
		))->display();
	}

	// Include anchor link
	echo '<a id="homes-for-sale"></a>';

}

// IDX compliance limit
if (empty($_REQUEST['snippet'])) {
	if (!empty($_COMPLIANCE['limit']) && $search_results_count['total'] > $_COMPLIANCE['limit']) {
		echo '<p class="message">Only ' . number_format($_COMPLIANCE['limit']) . ' properties may be displayed per search. To see all of your results, try narrowing your search criteria.</p>';
	}
}

//View toggle removes office and disclaimer when compliance rule met
$id = 'id="%s"';
$grid_id = "";
$detailed_id = "";

if($_COMPLIANCE['hide_office_grid']) {
    $grid_id = sprintf($id, "gridViewOffice");
    $detailed_id = sprintf($id, "detailedViewOffice");

} elseif($_COMPLIANCE['show_list_view']) {
    $grid_id = sprintf($id, "gridView");
    $detailed_id = sprintf($id, "detailedView");
}?>

<?php if (empty($idxSnippetAlreadyIncluded)) { ?>
		<div class="toolbar">
    <?php } ?>
		<div class="tabset pills mini">
		    <ul class="tabset">
		        <li <?=$grid_id; ?> <?=(empty($view) || $view == 'grid' || $view == 'map') ? ' class="current"' : ''; ?>>
		        	<a rel="nofollow" href="#grid" class="view" title="View results in a grid">
						<i class="icon-viewGrid"></i>
		        	</a>
		        </li>
		        <li <?=$detailed_id; ?> <?=($view == 'detailed') ? ' class="current"' : ''; ?>>
		        	<a rel="nofollow" href="#detailed" class="view"  title="View results in a detailed list">
						<i class="icon-viewList"></i>
		        	</a>
		        </li>
		        <?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING']))  { ?>
					<li>
						<a rel="nofollow" href="#map" class="map-toggle view<?=($view == 'map' || !empty($_REQUEST['map']['open'])) ? ' current' : ''; ?>" title="Toggle map">
							<i class="icon-viewMap"></i>
						</a>
					</li>
		        <?php } ?>
		    </ul>
			<?php if (!empty($results)) { ?>
				<span class="summary">
					<em><?=number_format($search_results_count['total']); ?> Properties Found.</em>
					Page <?=number_format($pagination['page']); ?> of <?=number_format($pagination['pages']); ?>
				</span>
			<?php } ?>
	    </div>
	    <div class="sort">
		    <form action="<?=Settings::getInstance()->SETTINGS['URL_IDX']; ?>">
		        <select name="sort" onchange="window.location = this.value">
					<?php foreach (IDX_Builder::getSortOptions() as $sortorder) { ?>
		            	<option value="?<?=htmlspecialchars(http_build_query(array_merge($querystring_nosort, array('sortorder' => $sortorder['value'])))); ?>"<?=($_REQUEST['sortorder'] == $sortorder['value'] ? ' selected' : ''); ?>><?= Format::htmlspecialchars($sortorder['title']); ?></option>
					<?php } ?>
		        </select>
		    </form>
	    </div>
    </div>
    <?php include $page->locateTemplate('idx', 'misc', 'search-controls'); ?>

<?php

// Map Container
echo '<div id="listings-map" class="hidden"></div>';

// Find "Result" TPL
$result_tpl = $page->locateTemplate('idx', 'misc', 'result');

// Search Results
if (!empty($results)) {
    echo '<div class="colset equal-heights listings ' . ($view == 'detailed' ? 'colset-1 layout-detailed' : 'colset-1-sm colset-2-md colset-3-lg colset-3-xl') . '">';
	foreach ($results as $index => $result) {
		include $result_tpl;
	}
	echo '</div>';
} else {
	echo '<div class="msg"><p>No listings were found matching your search criteria.</p></div>';
}

// Include Pagination
if (empty($idxSnippetAlreadyIncluded)) include $page->locateTemplate('idx', 'misc', 'pagination');
if (!empty($_COMPLIANCE['results']['show_immediately_below_listings'])) {
	echo '<div class="show-immediately-below-listings">';
    \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer();
	echo '</div>';
}
