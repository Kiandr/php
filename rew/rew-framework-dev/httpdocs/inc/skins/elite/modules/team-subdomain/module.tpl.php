<?php if (!empty($team)) {

	if (!empty($this->config('feature_image'))) $img  = $this->config('feature_image');
	if (!empty($img) && file_exists(DIR_FEATURED_IMAGES . $img)) {
		$img = URL_FEATURED_IMAGES . $img;
	}
?>
<div class="uk-cover-background <?= (!empty($img) ? 'team-subdomain-header-wrapper' : ''); ?>" style="<?= (!empty($img) ? "background-image: url('" . $img . "');" : ""); ?>">
    <div id="team-subdomain-header" class="uk-container uk-container-center<?= (empty($img) ? ' uk-margin-top' : ''); ?>">
        <div class="uk-grid uk-grid-small">
            <div class="uk-width-xsmall-1-1 uk-width-medium-1-6 team-image-container">
                <?php if (!empty($team['image'])) { ?>
                    <img class="uk-border-rounded"
                         src="/thumbs/194x230/uploads/teams/<?= Format::htmlspecialchars($team['image']); ?>"
                         alt="<?= Format::htmlspecialchars($team['name']); ?>">
                <?php } else if ($primaryAgent && !empty($primaryAgent['image'])) { ?>
                    <img class="uk-border-rounded"
                         src="/thumbs/194x230/uploads/agents/<?= Format::htmlspecialchars($primaryAgent['image']); ?>"
                         alt="<?= Format::htmlspecialchars($team['name']); ?>">
                <?php }?>
            </div>
            <div class="uk-width-xsmall-1-1 uk-width-medium-5-6">
                <h1><?= Format::htmlspecialchars($team['name']); ?></h1>
                <?php if ($this->config('homepage') && !empty($team['description'])) { ?>
                    <div>
                        <p class="truncate" data-truncate='{"count":200, "btnClass": "uk-button uk-button-primary"}'><?= $team['description']; ?></p>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?= (empty($img) ? '<hr class="uk-article-divider">' : ''); ?>
    </div>
</div>
<?php } ?>
