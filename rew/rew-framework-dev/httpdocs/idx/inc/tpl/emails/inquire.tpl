<p>An inquiry has been made for property <b><?=Lang::write('MLS_NUMBER'); ?><?=$listing['ListingMLS']; ?></b>.</p>
<?php if (!empty($inquire_type)) { ?>
	<p><b>Inquiry Type:</b> <?=Format::htmlspecialchars($inquire_type); ?></p>
<?php } ?>
<p><b>View Listing Details:</b> <a href="<?=$listing['url_details']; ?>" target="_blank"><?=$listing['url_details']; ?></a></p>
<p>The following information was collected from the user:</p>
<?=$user->formatUserInfo(); ?>