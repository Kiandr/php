<?php $backend_user = Auth::get(); ?>
<?php if (!empty($_REQUEST['create_search']) && $backend_user->isValid() && !empty($lead)) { ?>

	<div id="results-message" class="msg">
		<p>
			<strong><?=$backend_user->info('first_name'); ?> <?=$backend_user->info('last_name'); ?>:</strong> Create Saved Search for <strong><?=$lead['first_name']; ?> <?=$lead['last_name']; ?></strong>.
			<?php if (!isset($_GET['auto_save']) && $search_results_count['total'] > 500) { ?>
				<br>Please narrow your search to less than 500 results to Save this Search
			<?php } else { ?>
				<a id="save-link" href="javascript:void(0);" rel="nofollow">Save this Search!</a>
			<?php } ?>
		</p>
	</div>

<?php } elseif (!empty($_REQUEST['edit_search']) && $backend_user->isValid() && !empty($lead)) { ?>

	<div id="results-message" class="msg">
		<p>
			<strong><?=$backend_user->info('first_name'); ?> <?=$backend_user->info('last_name'); ?>:</strong> Editing Saved Search for <strong><?=$lead['first_name']; ?> <?=$lead['last_name']; ?></strong>.
			<?php if (!isset($_GET['auto_save']) && $search_results_count['total'] > 500) { ?>
				<br>Please narrow your search to less than 500 results to Save this Search
			<?php } else { ?>
				<a href="#" id="edit_search">Done Editing</a>
			<?php } ?>
			<a href="<?=Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/searches/?id=' . $lead['id'] . '&delete=' . $saved_search['id']; ?>" onClick="return confirm('Are you sure you want to delete this saved search?');">Delete Search</a>
		</p>
	</div>

<?php } elseif (!empty($saved_search)) { ?>

	<?php if (!empty($_REQUEST['edit_search'])) { ?>
		<div id="results-message" class="msg">
			<p>
				You are editing your <strong>"<?=htmlspecialchars($saved_search['title']); ?>"</strong> saved search.
				<?php if (!isset($_GET['auto_save']) && $search_results_count['total'] > 500) { ?>
					<br>Please narrow your search to less than 500 results to Save this Search
				<?php } else { ?>
					<a href="#" id="edit_search">Done Editing</a>
				<?php } ?>
			</p>
		</div>

	<?php } else { ?>
		<div id="results-message" class="msg">
			<p>
				You are viewing your <strong>"<?=htmlspecialchars($saved_search['title']); ?>"</strong> saved search.
				<a href="?edit_search=true&saved_search_id=<?=$saved_search['id']; ?>">Edit Search</a>
			</p>
		</div>
	<?php } ?>

<?php } elseif (!empty($_REQUEST['save_prompt'])) { ?>

	<?php if (!isset($_GET['auto_save']) && $search_results_count['total'] > 500) { ?>
		<div id="results-message" class="msg caution mini">
			<p>Please narrow your search to less than 500 results to Save this Search</p>
		</div>
	<?php } ?>

	<hgroup class="small">
		<h1><a href="#sidebar">Searching <em id="save-prompt"><?=$_REQUEST['save_prompt']; ?></em></a></h1>
		<?php if (isset($_GET['auto_save']) || $search_results_count['total'] < 499) { ?>
			<div class="btnset mini"><a class="btn strong" id="save-link" href="javascript:void(0);" rel="nofollow">Save Search</a></div>
		<?php } ?>
	</hgroup>

<?php } ?>