<?php

// Render report summary header (menu/title/preview)
echo $this->view->render('inc/tpl/partials/report/summary.tpl.php', [
    'title' => __('REW Dialer Report'),
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
    <div class="field col w1/2 <?=(empty($sql_date) ? ' hidden' : ''); ?>">
    	<label class="field__label"><?= __('Start Date'); ?> <em class="required">*</em></label>
    	<input class="w1/1" id="date_start" name="start" value="<?=(!empty($start) ? date('Y-m-d', $start) : 'all'); ?>"<?=(!empty($sql_date) ? ' required' : ''); ?>>
    </div>
    <div class="field col w1/2 <?=(empty($sql_date) ? ' hidden' : ''); ?>">
    	<label class="field__label"><?= __('End Date'); ?> <em class="required">*</em></label>
    	<input class="w1/1" id="date_end" name="end" value="<?=(!empty($end) ? date('Y-m-d', $end) : 'all'); ?>"<?=(!empty($sql_date) ? ' required' : ''); ?>>
    </div>
</div>
<button class="btn" id="btn-update" type="submit"><?= __('Generate'); ?></button>
</section>
<?php } ?>
<section id="agent-report" class="marT">
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
    <table class="dialer-report item_content_summaries table">
    	<thead>
    		<tr>
    			<th>&nbsp;</th>
    			<th style="font-size: 16px;" colspan="5"><?= __('REW Dialer Calls'); ?></th>
    		</tr>
    		<tr>
    			<th>&nbsp;</th>
    			<th style="text-align: center; border-left: 1px solid #ccc;"> <a href="?<?=http_build_query(array_merge($query_string, array('order' => 'total', 'sort' => ($_GET['order'] == 'total' && $_GET['sort'] == 'DESC' ? 'ASC' : 'DESC')))); ?>"> <?= __('Total'); ?>
    				<?=($_GET['order'] == 'total') ? '<span class="ico mini ' . ($_GET['sort'] == 'DESC' ? 'ico_sort_desc' : 'ico_sort_asc') . '"></span>' : ''; ?>
    				</a> </th>
    			<th style="text-align: center; border-left: 1px solid #ccc;"> <a href="?<?=http_build_query(array_merge($query_string, array('order' => 'contacted', 'sort' => ($_GET['order'] == 'contacted' && $_GET['sort'] == 'DESC' ? 'ASC' : 'DESC')))); ?>"> <?= __('Contacted'); ?>
    				<?=($_GET['order'] == 'contacted') ? '<span class="ico mini ' . ($_GET['sort'] == 'DESC' ? 'ico_sort_desc' : 'ico_sort_asc') . '"></span>' : ''; ?>
    				</a> </th>
    			<th style="text-align: center; border-left: 1px solid #ccc;"> <a href="?<?=http_build_query(array_merge($query_string, array('order' => 'attempted', 'sort' => ($_GET['order'] == 'attempted' && $_GET['sort'] == 'DESC' ? 'ASC' : 'DESC')))); ?>"> <?= __('Attempted'); ?>
    				<?=($_GET['order'] == 'attempted') ? '<span class="ico mini ' . ($_GET['sort'] == 'DESC' ? 'ico_sort_desc' : 'ico_sort_asc') . '"></span>' : ''; ?>
    				</a> </th>
    			<th style="text-align: center; border-left: 1px solid #ccc;"> <a href="?<?=http_build_query(array_merge($query_string, array('order' => 'voicemail', 'sort' => ($_GET['order'] == 'voicemail' && $_GET['sort'] == 'DESC' ? 'ASC' : 'DESC')))); ?>"> <?= __('Voicemail'); ?>
    				<?=($_GET['order'] == 'voicemail') ? '<span class="ico mini ' . ($_GET['sort'] == 'DESC' ? 'ico_sort_desc' : 'ico_sort_asc') . '"></span>' : ''; ?>
    				</a> </th>
    			<th style="text-align: center; border-left: 1px solid #ccc;"> <a href="?<?=http_build_query(array_merge($query_string, array('order' => 'invalid', 'sort' => ($_GET['order'] == 'invalid' && $_GET['sort'] == 'DESC' ? 'ASC' : 'DESC')))); ?>"> <?= __('Bad Number'); ?>
    				<?=($_GET['order'] == 'invalid') ? '<span class="ico mini ' . ($_GET['sort'] == 'DESC' ? 'ico_sort_desc' : 'ico_sort_asc') . '"></span>' : ''; ?>
    				</a> </th>
    		</tr>
    	</thead>
    	<tbody>
    		<?php foreach ($agents as $agent) { ?>
    		<tr>
    			<td><h4 class="item_content_title"><a href="<?=$agent['url']; ?>">
    					<?=Format::htmlspecialchars($agent['name']); ?>
    					</a></h4></td>
    			<td style="text-align: center; border-left: 1px solid #ccc;"><?php if (!empty($agent['total'])) { ?>
    				<?=Format::number($agent['total']); ?>
    				<?php } ?></td>
    			<td style="text-align: center; border-left: 1px solid #ccc;"><?php if (!empty($agent['contacted'])) { ?>
    				<?=Format::number($agent['contacted']); ?>

    				<!-- <span style="color: #808E9C;">(<?=Format::number(($agent['contacted'] / $agent['total']) * 100, 2); ?>%)</span> -->

    				<?php } ?></td>
    			<td style="text-align: center; border-left: 1px solid #ccc;"><?php if (!empty($agent['attempted'])) { ?>
    				<?=Format::number($agent['attempted']); ?>

    				<!-- <span style="color: #808E9C;">(<?=Format::number(($agent['attempted'] / $agent['total']) * 100, 2); ?>%)</span> -->

    				<?php } ?></td>
    			<td style="text-align: center; border-left: 1px solid #ccc;"><?php if (!empty($agent['voicemail'])) { ?>
    				<?=Format::number($agent['voicemail']); ?>

    				<!-- <span style="color: #808E9C;">(<?=Format::number(($agent['voicemail'] / $agent['total']) * 100, 2); ?>%)</span> -->

    				<?php } ?></td>
    			<td style="text-align: center; border-left: 1px solid #ccc;"><?php if (!empty($agent['invalid'])) { ?>
    				<?=Format::number($agent['invalid']); ?>

    				<!-- <span style="color: #808E9C;">(<?=Format::number(($agent['invalid'] / $agent['total']) * 100, 2); ?>%)</span> -->

    				<?php } ?></td>
    		</tr>
    		<?php } ?>
    	</tbody>
    </table>
</div>
<?php

					// AJAX Request
					if (isset($_GET['ajax'])) {

						// Return HTML Response
						die(ob_get_clean());

					}

				}

			?>

</div>

</form>