<?php

// Build Title
$title = (!empty($show_form))
    ? __('Generate Listing Report')
    : __('Listing Report for ') . Format::htmlspecialchars(implode(', ', array_filter(array($listing['Address'], $listing['AddressCity'], $listing['AddressState'])))) . ' ' . Format::htmlspecialchars($listing['AddressZipCode']) . ' (MLS&reg; #' . Format::htmlspecialchars($listing['ListingMLS']) . ')';

// Render report summary header (menu/title/preview)
echo $this->view->render('inc/tpl/partials/report/summary.tpl.php', [
    'title' => $title,
    'authuser' => $authuser,
    'reportsAuth' => $reportsAuth
]);
?>
<form method="get">

<div class="block">

	<?php if (!empty($show_form)) { ?>
	<?php

				// Multi-IDX
				if (!empty(Settings::getInstance()->IDX_FEEDS)) {
					echo '<div class="field">';
					echo '<label class="field__label">' . __('Pick a Feed:') .'</label>';
					echo '<select class="w1/1" name="feed">';
					echo '<option value="">-- ' . __('Choose an IDX Feed') . ' --</option>';
					foreach (Settings::getInstance()->IDX_FEEDS as $feed => $settings) {
						echo '<option ' . (Settings::getInstance()->IDX_FEED === $feed ? 'selected' : '') . ' value="' . $feed . '">' . $settings['title'] . '</option>';
					}
					echo '</select>';
					echo '</div>';
					echo '<div class="field -marB8">';
				} else {
					echo '<div class="field">';
				}

			?>
	<label class="field__label"><?= __('Find a Listing:'); ?></label>
	<input class="w1/1 autocomplete listing" name="mls" value="<?=htmlspecialchars($_POST['mls']); ?>" placeholder="<?= __('Enter MLS&reg; Number or Street Address'); ?>" required>
	</div>
	<?php if (!empty($show_form)) { ?>
	<div class="btns">
		<button class="btn group_action" type="submit"><?= __('Generate Report'); ?></button>
	</div>
	<?php } else { ?>
	<div class="btns"> <a class="btn" href="?"><?= __('Back'); ?></a> </div>
	<?php } ?>
	<?php } else { ?>
	<input type="hidden" name="feed" value="<?=$idx->getName(); ?>">
	<input type="hidden" name="mls" value="<?=Format::htmlspecialchars($listing['ListingMLS']); ?>">
	<section id="listing-filters" class="boxed hidden">
		<div class="field">
			<label class="field__label"><?= __('Filter by Date'); ?></label>
			<select class="w1/1" id="select-filter">
				<?php

						// Date Filters
						$selected = false;
						foreach ($ranges as $range) {
							// Select Range
							if (!empty($start) && !empty($end)) {
								$value = date('Y-m-d', $start) . '|' . date('Y-m-d', $end);
								if ($range['value'] == $value) {
									$range['selected'] = true;
									$selected = true;
								} elseif (!empty($selected)) {
									$range['selected'] = false;
								}
							}
							// Option
							echo '<option value="' . $range['value'] . '"' . (!empty($range['selected']) ? ' selected' : '') . '>' . $range['title'] . '</option>';
						}

						?>
			</select>
		</div>
        <div class="cols -marB">
    		<div class="field col w1/2">
    			<label class="field__label"><?= __('Start Date'); ?></label>
    			<input class="w1/1" id="date_start" name="start" placeholder="<?= __('Start Date'); ?>" value="<?=(!empty($start) ? date('Y-m-d', $start) : ''); ?>"<?=(!empty($sql_date) ? ' required' : ''); ?>>
    		</div>
    		<div class="field col w1/2">
    			<label class="field__label"><?= __('End Date<'); ?></label>
    			<input class="w1/1" id="date_end" name="end" placeholder="<?= __('End Date'); ?>" value="<?=(!empty($end) ? date('Y-m-d', $end) : ''); ?>"<?=(!empty($sql_date) ? ' required' : ''); ?>>
    		</div>
        </div>
        <button id="btn-update" type="submit" class="btn"><?= __('Update'); ?></button>
	</section>
	<section id="listing-report">
		<?php

				// Generate Report
				if (!empty($report)) {

					// AJAX Request
					if (isset($_GET['ajax'])) {

						// Erase All Previous Output
						if (ob_get_length() > 0) ob_end_clean();

						// Capture Output
						ob_start();

					}

			?>
		<div style="margin-bottom: 16px;">
			<h4 class="item_content_title"><a href="<?=$listing['url_details']; ?>" target="_blank">
				<?=$listing['Address']; ?>
				</a></h4>
			<div class="item_content_additional"> <strong>$
				<?=Format::number($listing['ListingPrice']); ?>
				</strong> -
				<?=ucwords(strtolower($listing['AddressCity'])); ?>
				,
				<?=ucwords(strtolower($listing['AddressState'])); ?>
				<p style="color: #808E9C;">
					<?=Format::truncate($listing['ListingRemarks'], 500); ?>
				</p>
			</div>
			<div class="item_content_thumb" style="width: 200px; height: 125px; margin-bottom: 16px;">
				<?php if(!empty($listing['ListingImage'])) { ?>
				<img src="<?=Format::thumbUrl($listing['ListingImage'], '200x125'); ?>" alt="">
				<?php } else { ?>
				<img src="/thumbs/200x125/uploads/listings/na.png" alt="">
				<?php } ?>
			</div>
			<div class="actions compact"> <a class="btn view" href="<?=$listing['url_details']; ?>" target="_blank"><?= __('View Details'); ?></a> </div>
		</div>
		<?php

					// Total Page Views
					$total_views = 0;
					foreach ($pages as $views) {
						$total_views += array_sum($views);
					}

					// Total Inquiries
					$total_forms = 0;
					foreach ($forms as $views) {
						$total_forms += array_sum($views);
					}

				?>
		<section id="leads_dashboard_summary" style="border-top: 1px solid #CCC; padding: 10px 0 10px 0;">
			<div class="-pad">
                <strong style="color: #0077cc"><?= __('Views'); ?></strong>
                <span class="R -padH16">
				    <?=Format::number($total_views); ?>
				</span>
            </div>
			<div class="-pad">
                <strong style="color: #019700"><?= __('Inquiries'); ?></strong>
                <span class="R -padH16">
				    <?=Format::number($total_forms); ?>
				</span>
            </div>
			<div class="-pad">
                <strong style="color: #9966cc"><?= __(Favourites); ?></strong>
                <span class="R -padH16">
				    <?=Format::number(array_sum($favorites)); ?>
				</span>
            </div>
			<div class="-pad">
                <strong style="color: #ff6600"><?= __('Recommended'); ?></strong>
                <span class="R -padH16">
				    <?=Format::number(array_sum($recommended)); ?>
				</span>
            </div>
		</section>
		<?php

					// Activity Chart
					echo '<section class="chart-wrapper" style="overflow: scroll;">';
					echo '<div class="col w8 p1" id="activity-chart" style="height: 300px;"></div>';
					echo '<div class="col w4 p9" id="activity-pie" style="height: 300px;"></div>';
					echo '</section>';

					// Viewed Pages
					if (!empty($pages)) {

						// Get Total
						$total = 0;
						array_walk($pages, function ($page) use (&$total) {
							$total += array_sum($page);
						});

						// Display Table
                        echo '<div class="table__wrap">';
						echo '<table class="item_content_summaries table">';
						echo '<thead>';
						echo '<tr>';
						echo '<th width="50%">' . __('Viewed Pages') . '</th>';
						echo '<th width="20%">' . __('Visits') . '</th>';
						echo '<th width="30%" style="text-align: right;">' . __('% Visits') . '</th>';
						echo '</tr>';
						echo '</thead>';
						echo '<tbody>';
						foreach ($pages as $url => $views) {
							$percent = (array_sum($views) / $total) * 100;
							echo '<tr style="vertical-align: middle;">';
							echo '<td><a href="' . $listing[$url] . '" target="_blank" class="item_content_title">' . ucwords(str_replace('url_', '', $url)) . '</a></td>';
							echo '<td>' . array_sum($views) . '</td>';
							echo '<td><div class="scaleline" style="margin: 0 10px 0 0;"><span class="line" style="width: ' . $percent . '%;"></span>&nbsp;<div class="potential" style="text-align: right;">' . Format::number($percent, 1) . '%</div></div></td>';
							echo '</tr>';
						}
						echo '</tbody>';
						echo '<tfoot>';
						echo '<tr>';
						echo '<th colspan="2" style="text-align: right; padding-right: 10px;">'. __('Total') . ':</th>';
						echo '<th style="text-align: right;">' . Format::number($total) . '</th>';
						echo '</tr>';
						echo '</tfoot>';
						echo '</table>';
                        echo '</div>';
					}

					// Referring Pages
					if (!empty($referers)) {
                        echo '<div class="table__wrap" style="border-top: 0;">';
						echo '<table class="item_content_summaries table">';
						echo '<thead>';
						echo '<tr>';
						echo '<th width="50%">' . __('Referring Pages') . '</th>';
						echo '<th width="20%">' . __('Referrals') . '</th>';
						echo '<th width="30%" style="text-align: right;">' . __('% Referrals') . '</th>';
						echo '</tr>';
						echo '</thead>';
						echo '<tbody>';
						foreach ($referers as $referer => $total) {
							$percent = ($total / array_sum($referers)) * 100;
							echo '<tr style="vertical-align: middle;">';
							if (!empty($referer)) {
								echo '<td class="item_content_title">' . $referer . '</td>';
							} else {
								echo '<td class="item_content_title"><i>(' . __('Unknown Referer') . ')</i></td>';
							}
							echo '<td>' . $total . '</td>';
							echo '<td><div class="scaleline" style="margin: 0 10px 0 0;"><span class="line" style="width: ' . $percent . '%;"></span>&nbsp;<div class="potential" style="text-align: right;">' . Format::number($percent, 1) . '%</div></div></td>';
							echo '</tr>';
						}
						echo '</tbody>';
						echo '<tfoot>';
						echo '<tr>';
						echo '<th colspan="2" style="text-align: right; padding-right: 10px;">' . __('Total') . ':</th>';
						echo '<th style="text-align: right;">' . Format::number(array_sum($referers)) . '</th>';
						echo '</tr>';
						echo '</tfoot>';
						echo '</table>';
                        echo '</div>';
					}

					// AJAX Request
					if (isset($_GET['ajax'])) {

						// Send as JSON
						header('Content-type: application/json');

						// Return JSON
						die(json_encode(array(
							'html'		=> ob_get_clean(),
							'minDate'	=> intval($minDate),
							'maxDate'	=> intval($maxDate),
							'series'	=> $series,
							'pie'		=> $pie
						)));

					}

				}

			?>
	    </section>
	<?php } ?>
	</section>

</div>

</form>
