<?php $page = $this->getPage(); ?>
<?php $isSaved = $this->config('isSaved') ?: false; ?>
<?php $maxSave = $this->config('maxSave') ?: false; ?>
<?php $canSave = $this->config('canSave') ?: false; ?>
<?php $hideFeed = $this->config('hideFeed') ?: false; ?>
<?php $className = $this->config('className') ?: false; ?>
<?php $buttonText = $this->config('button') ?: 'Search'; ?>
<div id="sub-quicksearch"<?=$className ? ' class="' . $className . '"' : ''; ?>>
	<div class="wrap">

		<?php

			// IDX feed switcher
			if (empty($hideFeed)) {
				$page->addContainer('idx-feeds')->addModule('idx-feeds', array(
					'template' => 'idx-search.tpl.php'
				))->display();
			}

		?>

		<form id="<?=$this->getUID() ; ?>" action="<?=Settings::getInstance()->SETTINGS['URL_IDX_SEARCH']; ?>" method="get" class="idx-search grid_12">
			<span class="search-icon icon-search"></span>

			<div class="s-inputs">
				<!-- Refine Search -->
				<input type="hidden" name="refine" value="true">

				<?php if ($this->config('advanced') || !empty($_REQUEST['snippet'])) { ?>

					<!-- Sort Order -->
					<input type="hidden" name="sortorder" value="<?=htmlspecialchars($_REQUEST['sortorder']); ?>">

					<!-- Map Info -->
					<input type="hidden" name="map[longitude]" value="<?=htmlspecialchars($_REQUEST['map']['longitude']); ?>">
					<input type="hidden" name="map[latitude]" value="<?=htmlspecialchars($_REQUEST['map']['latitude']); ?>">
					<input type="hidden" name="map[zoom]" value="<?=htmlspecialchars($_REQUEST['map']['zoom']); ?>">

					<!-- Map Tools -->
					<input type="hidden" name="map[polygon]" value="<?=htmlspecialchars($_REQUEST['map']['polygon']); ?>">
					<input type="hidden" name="map[radius]" value="<?=htmlspecialchars($_REQUEST['map']['radius']); ?>">
					<input type="hidden" name="map[bounds]" value="<?=(!empty($_REQUEST['bounds']) ? 1 : 0); ?>">
					<input type="hidden" name="map[ne]" value="<?=htmlspecialchars($_REQUEST['map']['ne']); ?>">
					<input type="hidden" name="map[sw]" value="<?=htmlspecialchars($_REQUEST['map']['sw']); ?>">

				<?php } ?>

				<?php

					// Create Lead Search
					if (!empty($_REQUEST['create_search']) && !empty($backend_user) && !empty($lead)) {
						echo '<input type="hidden" name="create_search" value="true">';
						echo '<input type="hidden" name="lead_id" value="' . $lead['id'] . '">';

					// Edit Saved Search
					} else if (!empty($saved_search) && !empty($_REQUEST['edit_search'])) {
						echo '<input type="hidden" name="edit_search" value="true">';
						echo '<input type="hidden" name="saved_search_id" value="' . $saved_search['id'] . '">';

						// Edit Lead Search
						if (!empty($backend_user) && !empty($lead)) {
							echo '<input type="hidden" name="lead_id" value="' . $lead['id'] . '">';
						}

					}

					// Display location search
					echo IDX_Panel::get('Location', array(
						'toggle'		=> false,
						'inputClass'	=> 'autocomplete location',
						'placeholder'	=> 'City, ' . Locale::spell('Neighborhood') . ', Address, ' . Locale::spell('Zip') . ' or ' . Lang::write('MLS') . ' #'
					))->getMarkup();

				?>

				<nav class="nav-dropdowns" role="navigation">
					<ul>
						<li class="search-price">
							<span class="dropdown-title">
								Price <i class="icon-chevron-down"></i>
							</span>
							<div class="dropdown">
								<a class="dd-toggle"><i class="icon-closeX"></i></a>
								<?php IDX_Panel::get('Price', array('toggle' => false))->display(); ?>
								<button type="submit" class="extra-submit buttonstyle"><?=$buttonText; ?></button>
							</div>
						</li>
						<li class="search-type">
							<span class="dropdown-title">
								Type <i class="icon-chevron-down"></i>
							</span>
							<div class="dropdown">
								<a class="dd-toggle"><i class="icon-closeX"></i></a>
								<?php IDX_Panel::get('Type', array('toggle' => false))->display(); ?>
								<button type="submit" class="extra-submit buttonstyle"><?=$buttonText; ?></button>
							</div>
						</li>
						<li class="search-rooms">
							<span class="dropdown-title">
								<span class="for-mobile">Rooms </span>
								<span class="for-above-mobile">Rooms</span>
								<i class="icon-chevron-down"></i>
							</span>
							<div class="dropdown">
								<a class="dd-toggle"><i class="icon-closeX"></i></a>
								<?php IDX_Panel::get('Rooms', array('toggle' => false))->display(); ?>
								<button type="submit" class="extra-submit buttonstyle"><?=$buttonText; ?></button>
							</div>
						</li>
						<li class="search-submit">
							<button class="search-button" type="submit">
								<i class="icon-searchGlass"></i>
							</button>
						</li>
						<?php if (empty($isSaved)) { ?>
							<li class="search-save">
								<?php if (!empty($canSave)) { ?>
									<button id="save-link" class="save-button" type="button" title="Save this Search">
										<i class="icon-searchSave"></i>
									</button>
								<?php } else { ?>
									<button class="save-button" type="button" title="Please narrow your search to less than <?=Format::number($maxSave); ?> results to save.">
										<i class="icon-searchSave"></i>
										<span class="badge">!</span>
									</button>
								<?php } ?>
							</li>
						<?php } ?>
					</ul>
				</nav>
			</div>
			<?php

				// Current search criteria
				if ($page->info('name') !== 'search_map') {
					echo '<div class="search-criteria">' . PHP_EOL;
					if (!empty($idx_tags) && is_array($idx_tags)) {
						foreach ($idx_tags as $tag) {
							echo '<a class="buttonstyle mini icon-close" data-idx-tag=\'' . json_encode($tag->getField()) . '\'>';
							echo Format::htmlspecialchars($tag->getTitle());
							echo '</a>' . PHP_EOL;
						}
					}
					echo '</div>';
				}

				// Advanced search options
				if ($this->config('advanced') && !empty($panels)) {

					// Toggle advanced panels
					echo '<a class="show-advanced" data-text="More Options">' . PHP_EOL;
					echo '<span class="inner-text">' . ($show_advanced ? 'Less Options' : 'More Options') . '</span>' . PHP_EOL;
					echo '<span class="inner-icon"><i class="icon-chevron-down"></i></span>' . PHP_EOL;
					echo '</a>' . PHP_EOL;

					// Current search criteria
					echo '<div class="search-criteria"></div>' . PHP_EOL;

					// Advanced search panels
					echo '<div class="advanced-options' . ($show_advanced ? '' : ' hidden') . '">';
					foreach ($panels as $panel) {
						$id = $panel->getID();
						if ($id === 'city') echo '<fieldset>';
						$panel->display();
						if ($id === 'city') echo '</fieldset>';
					}
					echo '<div class="clearfix"></div>';
					echo '<button type="submit" class="extra-submit buttonstyle">Update Search Results</button>';
					echo '</div>';
				}

				// Include map search tools
				if ($_REQUEST['snippet'] || in_array($_GET['load_page'], array('search', 'search_map'))) {

					// Map panels
					$polygon = IDX_Panel::get('Polygon');
					$radius = IDX_Panel::get('Radius');
					$bounds = IDX_Panel::get('Bounds');

					// Map tools (these are re-position via JS)
					echo '<div id="map-draw-controls" class="hidden">';
					echo '<div id="field-polygon">' . $polygon->getMarkup() . '</div>';
					echo '<div id="field-radius">' . $radius->getMarkup() . '</div>';
					echo '<div id="field-bounds">' . $bounds->getMarkup() . '</div>';
					echo '<div class="refine-btn" title="Refine Search"><i class="icon-search"></i></div>';
					echo '</div>';

				}

				// Edit search controls
				if (!empty($saved_search) && !empty($_REQUEST['edit_search'])) {

					// $_REQUEST over-ride
					$saved_search['title'] = $_REQUEST['search_title'] ?: $saved_search['title'];
					$saved_search['frequency'] = $_REQUEST['frequency'] ?: $saved_search['frequency'];

					// Edit controls
					echo '<div class="row">'
						. '<div class="field x6">'
							. '<label>Search Title:</label>'
							. '<div class="details">'
								. '<input class="x12" name="search_title" value="' . htmlspecialchars($saved_search['title']) . '" required>'
							. '</div>'
						. '</div>'
						.'<div class="field x6 last">'
							. '<label>Update Frequency:</label>'
							. '<div class="details">'
								. '<select name="frequency" class="x12">'
									. '<option value="never"' . ($saved_search['frequency'] == 'never' ? ' selected' : '') . '>Never</option>'
									. '<option value="immediately"' . ($saved_search['frequency'] == 'immediately' ? ' selected' : '') . '>Immediately</option>'
									. '<option value="daily"' . ($saved_search['frequency'] == 'daily' ? ' selected' : '') . '>Daily</option>'
									. '<option value="weekly"' . (empty($saved_search['frequency']) || $saved_search['frequency'] == 'weekly' ? ' selected' : '') . '>Weekly</option>'
									. '<option value="monthly"' . ($saved_search['frequency'] == 'monthly' ? ' selected' : '') . '>Monthly</option>'
								. '</select>'
							. '</div>'
						. '</div>'
					. '</div>';

				}

			?>
		</form>
	</div>
</div>
<div class="clearfix"></div>