<?php $className = $this->config('className') ?: false; ?>
<?php $buttonText = $this->config('button') ?: 'Search'; ?>
<div id="sub-quicksearch"<?=$className ? ' class="' . $className . '"' : ''; ?>>
	<div class="wrap">

		<h3 class="mobile-only">New Search <a class="search-toggle">X</a></h3>

		<?php

			// IDX feed switcher
			if ($this->config('advanced')) {
				$this->getPage()->addContainer('idx-feeds')->addModule('idx-feeds', array(
					'template' => 'idx-search.tpl'
				))->display();

			// Dynamic feed switch (see module.js.php)
			} elseif (!empty(Settings::getInstance()->IDX_FEEDS)) {
				echo '<ul class="feed-switcher">';
				foreach (Settings::getInstance()->IDX_FEEDS as $link => $feed) {

					// Skip feed agent subdomain control
					$commingled_feeds = array();
					$idx = Util_IDX::getIdx($link, false, false);
					if ($idx->isCommingled()) $commingled_feeds = $idx->getFeeds();
					if (
						Settings::getInstance()->SETTINGS['agent'] != 1
						&& !in_array($link, Settings::getInstance()->SETTINGS['agent_idxs'])
						&& (
							empty($commingled_feeds)
							|| array_intersect($commingled_feeds, Settings::getInstance()->SETTINGS['agent_idxs']) == array()
						)
					) {
						continue;
					}

					echo '<li' . (Settings::getInstance()->IDX_FEED === $link ? ' class="current"' : '') . '><a data-feed="' . $link . '">';
					echo Format::htmlspecialchars($feed['title']);
					echo '</a></li>';
				}
				echo '</ul>';

			}

		?>

		<form id="<?=$this->getUID() ; ?>" action="<?=Settings::getInstance()->SETTINGS['URL_IDX_SEARCH']; ?>" method="get" class="idx-search grid_12">

			<span class="search-icon icon-search"></span>

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

			<?php } elseif (!empty(Settings::getInstance()->IDX_FEEDS)) { ?>

				<!-- IDX Feed -->
				<input type="hidden" name="feed" value="<?=htmlspecialchars(Settings::getInstance()->IDX_FEED); ?>">

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
					<li>
						<span class="dropdown-title">Price <i class="icon-chevron-down"></i></span>
						<div class="dropdown">
							<a class="dd-toggle">X</a>
							<?php IDX_Panel::get('Price', array('toggle' => false))->display(); ?>
							<button type="submit" class="extra-submit buttonstyle"><?=$buttonText; ?></button>
						</div>
					</li>
					<li>
						<span class="dropdown-title">Type <i class="icon-chevron-down"></i></span>
						<div class="dropdown">
							<a class="dd-toggle">X</a>
							<?php IDX_Panel::get('Type', array('toggle' => false))->display(); ?>
							<button type="submit" class="extra-submit buttonstyle"><?=$buttonText; ?></button>
						</div>
					</li>
					<li>
						<span class="dropdown-title"><span class="for-mobile">Rooms </span><span class="for-above-mobile">Beds / Baths</span> <i class="icon-chevron-down"></i></span>
						<div class="dropdown">
							<a class="dd-toggle">X</a>
							<?php IDX_Panel::get('Rooms', array('toggle' => false))->display(); ?>
							<button type="submit" class="extra-submit buttonstyle"><?=$buttonText; ?></button>
						</div>
					</li>
					<li class="search-submit">
						<button class="search-button<?php /* icon-search */ ?>" type="submit"><?=$buttonText; ?></button>
					</li>
				</ul>
			</nav>

			<?php

				// Advanced search options
				if ($this->config('advanced') && !empty($panels)) {

					// Toggle advanced panels
					echo '<a class="show-advanced" data-text="More Options">' . PHP_EOL;
					echo '<span class="inner-text">' . ($show_advanced ? 'Less Options' : 'More Options') . '</span>' . PHP_EOL;
					echo '<span class="inner-icon"><i class="icon-chevron-down"></i></span>' . PHP_EOL;
					echo '</a>' . PHP_EOL;

					// Current search tags
					echo '<div class="search-criteria">' . PHP_EOL;
					if (!empty($idx_tags) && is_array($idx_tags)) {
						foreach ($idx_tags as $tag) {
							echo '<a class="buttonstyle mini icon-close" data-idx-tag=\'' . json_encode($tag->getField()) . '\'>';
							echo Format::htmlspecialchars($tag->getTitle());
							echo '</a>' . PHP_EOL;
						}
					}
					echo '</div>';

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