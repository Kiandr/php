<?php
// Render report summary header (menu/title/preview)
echo $this->view->render('inc/tpl/partials/report/summary.tpl.php', [
    'title' => __('Agent Response Report'),
    'authuser' => $authuser,
    'reportsAuth' => $reportsAuth
]);
?>
<form method="get">

    <div class="block">

	<?php if (!empty($show_form)) { ?>
	<input type="hidden" name="order" value="<?=Format::htmlspecialchars($_GET['order']); ?>">
	<input type="hidden" name="sort" value="<?=Format::htmlspecialchars($_GET['sort']); ?>">
	<div class="field">
		<label class="field__label"><?= __('Filter by Date'); ?> <em class="required">*</em></label>
		<select id="select-filter" class="w1/1">
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
    	<div class="w1/2 col field <?=(empty($sql_date) ? ' hidden' : ''); ?>">
    		<label class="field__label"><?= __('Start Date'); ?> <em class="required">*</em></label>
    		<input class="w1/1" id="date_start" name="start" value="<?=(!empty($start) ? date('Y-m-d', $start) : 'all'); ?>"<?=(!empty($sql_date) ? ' required' : ''); ?>>
    	</div>
    	<div class="w1/2 col field <?=(empty($sql_date) ? ' hidden' : ''); ?>">
    		<label class="field__label"><?= __('End Date'); ?> <em class="required">*</em></label>
    		<input class="w1/1" id="date_end" name="end" value="<?=(!empty($end) ? date('Y-m-d', $end) : 'all'); ?>"<?=(!empty($sql_date) ? ' required' : ''); ?>>
    	</div>
    </div>
	<button id="btn-update" type="submit" class="btn -marB"><?= __('Generate'); ?></button>
	<?php } ?>
	<section id="agent-report">
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
        <div class="table__wrap">
    		<table class="item_content_summaries table">
    			<thead>
    				<tr>
    					<th>&nbsp;</th>
    					<th style="text-align: center; border-bottom: 1px solid #ccc; border-left: 1px solid #ccc;" colspan="5"><?= __('Assigned Leads'); ?></th>
    					<th style="text-align: center; border-bottom: 1px solid #ccc; border-left: 1px solid #ccc;" colspan="3"><?= __('First Response'); ?> <sup style="font-size: 10px; color: #0096d8;">1</sup></th>
    					<th style="text-align: center; border-bottom: 1px solid #ccc; border-left: 1px solid #ccc;" colspan="4"><?= __('Response Times'); ?></th>
    				</tr>
    				<tr>
    					<th>&nbsp;</th>
    					<th style="text-align: center; border-left: 1px solid #ccc;"> <a href="?<?=http_build_query(array_merge($query_string, array('order' => 'leads', 'sort' => ($_GET['order'] == 'leads' && $_GET['sort'] == 'DESC' ? 'ASC' : 'DESC')))); ?>"> <?= __('Total'); ?>
    						<?=($_GET['order'] == 'leads') ? '<span class="ico mini ' . ($_GET['sort'] == 'DESC' ? 'ico_sort_desc' : 'ico_sort_asc') . '"></span>' : ''; ?>
    						</a> </th>
    					<th style="text-align: center; border-left: 1px solid #ccc;"> <a href="?<?=http_build_query(array_merge($query_string, array('order' => 'accepted', 'sort' => ($_GET['order'] == 'accepted' && $_GET['sort'] == 'DESC' ? 'ASC' : 'DESC')))); ?>"> <?= __('Accepted'); ?>
    						<?=($_GET['order'] == 'accepted') ? '<span class="ico mini ' . ($_GET['sort'] == 'DESC' ? 'ico_sort_desc' : 'ico_sort_asc') . '"></span>' : ''; ?>
    						</a> </th>
    					<th style="text-align: center; border-left: 1px solid #ccc;"> <a href="?<?=http_build_query(array_merge($query_string, array('order' => 'pending', 'sort' => ($_GET['order'] == 'pending' && $_GET['sort'] == 'DESC' ? 'ASC' : 'DESC')))); ?>"> <?= __('Pending'); ?>
    						<?=($_GET['order'] == 'pending') ? '<span class="ico mini ' . ($_GET['sort'] == 'DESC' ? 'ico_sort_desc' : 'ico_sort_asc') . '"></span>' : ''; ?>
    						</a> </th>
    					<th style="text-align: center; border-left: 1px solid #ccc;"> <a href="?<?=http_build_query(array_merge($query_string, array('order' => 'rejected', 'sort' => ($_GET['order'] == 'rejected' && $_GET['sort'] == 'DESC' ? 'ASC' : 'DESC')))); ?>"> <?= __('Rejected'); ?>
    						<?=($_GET['order'] == 'rejected') ? '<span class="ico mini ' . ($_GET['sort'] == 'DESC' ? 'ico_sort_desc' : 'ico_sort_asc') . '"></span>' : ''; ?>
    						</a> </th>
    					<th style="text-align: center; border-left: 1px solid #ccc;"> <a href="?<?=http_build_query(array_merge($query_string, array('order' => 'closed', 'sort' => ($_GET['order'] == 'closed' && $_GET['sort'] == 'DESC' ? 'ASC' : 'DESC')))); ?>"> <?= __('Closed'); ?>
    						<?=($_GET['order'] == 'closed') ? '<span class="ico mini ' . ($_GET['sort'] == 'DESC' ? 'ico_sort_desc' : 'ico_sort_asc') . '"></span>' : ''; ?>
    						</a> </th>
    					<th style="text-align: center; border-left: 1px solid #ccc;"> <a href="?<?=http_build_query(array_merge($query_string, array('order' => 'rate', 'sort' => ($_GET['order'] == 'rate' && $_GET['sort'] == 'DESC' ? 'ASC' : 'DESC')))); ?>"> <?= __('Rate'); ?>
    						<?=($_GET['order'] == 'rate') ? '<span class="ico mini ' . ($_GET['sort'] == 'DESC' ? 'ico_sort_desc' : 'ico_sort_asc') . '"></span>' : ''; ?>
    						</a> </th>
    					<th style="text-align: center; border-left: 1px solid #ccc;"> <a href="?<?=http_build_query(array_merge($query_string, array('order' => 'rate_emails', 'sort' => ($_GET['order'] == 'rate_emails' && $_GET['sort'] == 'DESC' ? 'ASC' : 'DESC')))); ?>"> <?= __('By Email'); ?>
    						<?=($_GET['order'] == 'rate_emails') ? '<span class="ico mini ' . ($_GET['sort'] == 'DESC' ? 'ico_sort_desc' : 'ico_sort_asc') . '"></span>' : ''; ?>
    						</a> </th>
    					<th style="text-align: center; border-left: 1px solid #ccc;"> <a href="?<?=http_build_query(array_merge($query_string, array('order' => 'rate_calls', 'sort' => ($_GET['order'] == 'rate_calls' && $_GET['sort'] == 'DESC' ? 'ASC' : 'DESC')))); ?>"> <?= __('By Phone'); ?>
    						<?=($_GET['order'] == 'rate_calls') ? '<span class="ico mini ' . ($_GET['sort'] == 'DESC' ? 'ico_sort_desc' : 'ico_sort_asc') . '"></span>' : ''; ?>
    						</a> </th>
    					<th style="text-align: center; border-left: 1px solid #ccc;"> <a href="?<?=http_build_query(array_merge($query_string, array('order' => 'min', 'sort' => ($_GET['order'] == 'min' && $_GET['sort'] == 'DESC' ? 'ASC' : 'DESC')))); ?>"> <?= __('Best'); ?>
    						<?=($_GET['order'] == 'min') ? '<span class="ico mini ' . ($_GET['sort'] == 'DESC' ? 'ico_sort_desc' : 'ico_sort_asc') . '"></span>' : ''; ?>
    						</a> </th>
    					<th style="text-align: center; border-left: 1px solid #ccc;"> <a href="?<?=http_build_query(array_merge($query_string, array('order' => 'avg', 'sort' => ($_GET['order'] == 'avg' && $_GET['sort'] == 'DESC' ? 'ASC' : 'DESC')))); ?>"> <?= __('Average'); ?>
    						<?=($_GET['order'] == 'avg') ? '<span class="ico mini ' . ($_GET['sort'] == 'DESC' ? 'ico_sort_desc' : 'ico_sort_asc') . '"></span>' : ''; ?>
    						</a> </th>
    					<th style="text-align: center; border-left: 1px solid #ccc;"> <a href="?<?=http_build_query(array_merge($query_string, array('order' => 'max', 'sort' => ($_GET['order'] == 'max' && $_GET['sort'] == 'DESC' ? 'ASC' : 'DESC')))); ?>"> <?= __('Worst'); ?>
    						<?=($_GET['order'] == 'max') ? '<span class="ico mini ' . ($_GET['sort'] == 'DESC' ? 'ico_sort_desc' : 'ico_sort_asc') . '"></span>' : ''; ?>
    						</a> </th>
    				</tr>
    			</thead>
    			<tbody>
    				<?php foreach ($agents as $agent) { ?>
    				<tr>
    					<td><h4 class="item_content_title"><a href="<?=$agent['url']; ?>">
    							<?=$agent['name']; ?>
    							</a></h4></td>
    					<td style="text-align: center; border-left: 1px solid #ccc;"><?php if (!empty($agent['leads'])  || !empty($agent['rejected'])) { ?>
    						<?=Format::number($agent['leads'] + $agent['rejected']); ?>
    						<?php } ?></td>
    					<td style="text-align: center; border-left: 1px solid #ccc;"><?php if (!empty($agent['accepted'])) { ?>
    						<?=Format::number($agent['accepted']); ?>

    						<!-- <span style="color: #808E9C;">(<?=Format::number(($agent['accepted'] / $agent['leads']) * 100, 2); ?>%)</span> -->

    						<?php } ?></td>
    					<td style="text-align: center; border-left: 1px solid #ccc;"><?php if (!empty($agent['pending'])) { ?>
    						<?=Format::number($agent['pending']); ?>

    						<!-- <span style="color: #808E9C;">(<?=Format::number(($agent['pending'] / $agent['leads']) * 100, 2); ?>%)</span> -->

    						<?php } ?></td>
    					<td style="text-align: center; border-left: 1px solid #ccc;"><?php if (!empty($agent['rejected'])) { ?>
    						<?=Format::number($agent['rejected']); ?>

    						<!-- <span style="color: #808E9C;">(<?=Format::number(($agent['rejected'] / $agent['leads']) * 100, 2); ?>%)</span> -->

    						<?php } ?></td>
    					<td style="text-align: center; border-left: 1px solid #ccc;"><?php if (!empty($agent['closed'])) { ?>
    						<?=Format::number($agent['closed']); ?>

    						<!-- <span style="color: #808E9C;">(<?=Format::number(($agent['closed'] / $agent['leads']) * 100, 2); ?>%)</span> -->

    						<?php } ?></td>
    					<td style="text-align: center; border-left: 1px solid #ccc;"><?php if (!empty($agent['emails']) || !empty($agent['calls'])) { ?>
    						<span style="color: #808E9C;">
    						<?=Format::number($agent['rate'], 2); ?>
    						%</span>
    						<?php } elseif (!empty($agent['leads'])) { ?>
    						<span style="color: #808E9C;">0%</span>
    						<?php } ?></td>
    					<td style="text-align: center; border-left: 1px solid #ccc;"><?php if (!empty($agent['emails'])) { ?>
    						<span style="color: #808E9C;">
    						<?=Format::number($agent['rate_emails'], 2); ?>
    						%</span>
    						<?php } ?></td>
    					<td style="text-align: center; border-left: 1px solid #ccc;"><?php if (!empty($agent['calls'])) { ?>
    						<span style="color: #808E9C;">
    						<?=Format::number($agent['rate_calls'], 2); ?>
    						%</span>
    						<?php } ?></td>
    					<td style="text-align: center; border-left: 1px solid #ccc;"><?=tpl_date($agent['min'], true); ?></td>
    					<td style="text-align: center; border-left: 1px solid #ccc;"><?=tpl_date($agent['avg'], true); ?></td>
    					<td style="text-align: center; border-left: 1px solid #ccc;"><?=tpl_date($agent['max'], true); ?></td>
    				</tr>
    				<?php } ?>
    			</tbody>
    		</table>
        </div>
		<p><sup style="color: #0096d8;">1</sup> <?= __('First Response is based off first Call or Email since Lead was Assigned to Agent. Campaign Emails &amp; Auto-Responders are not included in this report.'); ?></p>
		<?php

					// AJAX Request
					if (isset($_GET['ajax'])) {

						// Return HTML Response
						die(ob_get_clean());

					}

				}

			?>
	</section>

    </div>

</form>