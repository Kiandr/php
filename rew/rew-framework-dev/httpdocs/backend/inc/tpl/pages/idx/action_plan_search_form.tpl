<fieldset class="colset" id="ap-idx-switcher-wrap">

	<?php
	// IDX Feed Switcher
	if (!empty(Settings::getInstance()->IDX_FEEDS)) {
		// URL Vars to Retain
		$gets = $_GET;
		unset($gets['feed']);
		unset($gets['page']);

		echo '<h2>' . __('Search Area') . '</h2>';
		echo '<div id="ap-idx-switcher">';
			echo '<ul>';
			foreach (Settings::getInstance()->IDX_FEEDS as $feed => $settings) {
				echo '<li ' . ($_GET['feed'] == $feed ? 'class="current"' : '') . '>'
					. '<a href="?' . http_build_query($gets) . '&feed=' . urlencode($feed) . '">'
						. Format::ucnames($settings['title'])
					. '</a>'
				. '</li>';
			}
			echo '</ul>';
		echo '</div>';

		unset($gets);
	}
	?>

</fieldset>

<form id="searchForm" method="get">

	<label id="ap-search-result-count" class="warning hint"></label>

	<input type="hidden" name="split" value="<?=Format::htmlspecialchars($_POST['split']); ?>">

	<input type="hidden" name="lead_id" value="<?=Format::htmlspecialchars($_GET['lead_id']); ?>">
	<input type="hidden" name="post_task" value="<?=Format::htmlspecialchars($_GET['post_task']); ?>">
	<input type="hidden" name="popup" value="<?=Format::htmlspecialchars($_GET['popup']); ?>">
	<input type="hidden" name="feed" value="<?=Format::htmlspecialchars($_GET['feed']); ?>">

    <?php

        // Render IDX builder search panels
        echo $this->view->render('::partials/idx/builder', [
            'builder' => $builder,
            'page' => $page
        ]);

    ?>

	<section>
		<div class="field">
				<?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING'])) { ?>
					<div id="idx-builder-map"></div>
				<?php } ?>
				<div class="field">
						<label class="field__label"><?= __('Sort Results By'); ?></label>
						<select class="w1/1" name="sort_by">
							<?php foreach ($builder->getSortOptions() as $option) { ?>
								<option value="<?=$option['value']; ?>"<?=($_POST['sort_by'] == $option['value']) ? ' selected' : ''; ?>><?=$option['title']; ?></option>
							<?php } ?>
						</select>
				</div>
		</div>
	</section>

</form>
