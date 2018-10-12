<?php

if ($reportsAuth->canConnectToAnalytics($authuser)) {
	if (empty($connected)) {
	    $action = [
	        'href' => Settings::getInstance()->URLS['URL_BACKEND'] . 'agents/agent/networks/?id='. $authuser->info('id'),
	        'name' => 'Connect'
	    ];
	} else {
	    $action = [
	        'href' => Settings::getInstance()->URLS['URL_BACKEND'] . 'agents/agent/networks/?id=' . $authuser->info('id'),
	        'name' => 'Settings'
	    ];
	}
}

// Render report summary header (menu/title/preview)
echo $this->view->render('inc/tpl/partials/report/summary.tpl.php', [
    'title' => __('Google Analytics'),
    'authuser' => $authuser,
    'reportsAuth' => $reportsAuth,
    'action' => $action
]);
?>

<div class="block">

<?php if (empty($connected)) { ?>

<p><?= __('To use this feature you must first connect to your Google Analytics account.'); ?></p>

<?php  } else { ?>
<p><?= __('Please be aware that these features are dependent on the operation and cooperation of third party services. As such, Real Estate Webmasters can\'t guarantee continuous availability of these features.'); ?></p>
<div id="ga-form" class="-marB">
	<div class="field">
		<label class="field__label"><?= __('Google Analytics Profile'); ?> <em class="required">*</em></label>
		<?php if (!empty($profiles)) : ?>
		<select class="w1/1" id="ga_profile" name="ga_profile" required>
			<?php foreach ($profiles as $profile) : ?>
			<option value="<?=$profile['id']; ?>">
			<?=$profile['title']; ?>
			</option>
			<?php endforeach; ?>
		</select>
		<?php else : ?>
		<p><?= __('No Profiles Available'); ?></p>
		<?php endif; ?>
	</div>
	<div class="field">
		<label class="field__label"><?= __('Segment Filter'); ?> <em class="required">*</em></label>
		<?php if (!empty($segments)) : ?>
		<select class="w1/1" id="ga_segment" name="ga_segment" required>
			<?php foreach ($segments as $segment) : ?>
			<option value="<?=$segment['id']; ?>">
			<?=$segment['title']; ?>
			</option>
			<?php endforeach; ?>
		</select>
		<?php else : ?>
		<p><?= __('No Segments Available'); ?></p>
		<?php endif; ?>
	</div>
	<div class="field">
		<label class="field__label"><?= __('Start Date'); ?> <em class="required">*</em></label>
		<input class="w1/1" id="date_start" name="date_start" value="<?=htmlspecialchars($date_start); ?>" required>
	</div>
	<div class="field">
		<label class="field__label"><?= __('End Date'); ?> <em class="required">*</em></label>
		<input class="w1/1" id="date_end" name="date_end" value="<?=htmlspecialchars($date_end); ?>" required>
	</div>
	<button id="ga-load" type="button" class="btn btn--positive"><?= __('Load'); ?></button>
</div>
<div id="ga-loader" class="hidden"> <span class="img"><img src="<?=URL_BACKEND_IMAGES; ?>loading.gif"></span> <span class="text"><?= __('Loading'); ?>...</span> </div>
<div id="ga-data" class="hidden">
	<div id="dashboard_main" class="analytics">
		<div id="leads_dashboard_summary" class="analytics__summary cols">
            <div style="overflow: hidden;">
    			<div class="-pad col w1/3" id="ga-visits"> <span style="color: #0077cc;"><?=__('Visits'); ?></span> <span class="group -R count">0</span> </div>
    			<div class="-pad col w1/3" id="ga-pageviewsPerVisit"> <span style="color: #9966cc;"><?=__('Pages/Visit'); ?></span> <span class="group -R count">0</span> </div>
    			<div class="-pad col w1/3" id="ga-percentNewVisits"> <span style="color: #ff6600;"><?=__('% New Visits'); ?></span> <span class="group -R count">0</span> </div>
            </div>
            <div style="overflow: hidden;">
    			<div class="-pad col w1/3" id="ga-pageviews"> <span style="color: #0077cc;"><?=__('Pageviews'); ?></span> <span class="group -R count">0</span> </div>
    			<div class="-pad col w1/3" id="ga-avgTimeOnSite"> <span style="color: #9966cc;"><?=__('Avg. Time on Site'); ?></span> <span class="group -R count">0</span> </div>
    			<div class="-pad col w1/3" id="ga-visitBounceRate"> <span style="color: #ff6600;"><?=__('Bounce Rate'); ?></span> <span class="group -R count">0</span> </div>
            </div>
		</div>
		<div class="w1/1">
			<div>
				<div id="ga-visitors">
                    <h2>Report Chart</h2>
					<div class="chart hidden"></div>
				</div>
			</div>
		</div>
	</div>
	<div id="dashboard_cols" class="cols">
		<div class="col w1/3">
            <h2><?= __('Top Referring Sites'); ?></h2>
            <div id="ga-referers">
                <div class="table hidden"></div>
            </div>
		</div>
		<div class="col w1/3">
            <h2><?= __('Top Referring Keywords'); ?></h2>
            <div id="ga-keywords">
                <div class="table hidden"></div>
            </div>
		</div>
		<div class="col w1/3">
            <h2><?= __('Top Search Engines'); ?></h2>
            <div id="ga-sources">
                <div class="table hidden"></div>
            </div>
		</div>
	</div>
	<div class="clear"></div>
</div>
<?php } ?>
</div>


</div>