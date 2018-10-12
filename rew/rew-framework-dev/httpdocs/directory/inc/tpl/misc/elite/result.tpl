<article class="uk-width-xsmall-1-1 uk-width-small-1-2 uk-width-large-1-3 uk-margin-bottom directory-listing<?=($entry['featured'] == 'Y') ? ' featured' : ''; ?>">
	<div class="body">
		<div class="photo"><a href="<?=$entry['url_details']; ?>" class="business-thumb uk-width-*"><img src="/thumbs/190x100/<?=$entry['image']; ?>" alt="" class="uk-width-*"></a></div>
		<div class="details">
			<h4 class="name"><a href="<?=$entry['url_details']; ?>"><?=htmlspecialchars($entry['business_name']); ?></a></h4>
			<?php if (!empty($entry['phone'])) { ?>
				<p class="val phone"><?=htmlspecialchars($entry['phone']); ?></p>
			<?php } ?>
			<?php if (!empty($entry['address'])) { ?>
				<p class="val address"><?=htmlspecialchars($entry['address']); ?></p>
			<?php } ?>
			<?php if (!empty($entry['description'])) { ?>
				<p class="description"><?=Format::truncate(strip_tags($entry['description']), 125, '&hellip;', false); ?></p>
			<?php } ?>
			<a class="btn strong" href="<?=$entry['url_details']; ?>">View Details <i class="uk-icon uk-icon-chevron-right uk-icon-justify"></i></a>
		</div>
	</div>
</article>