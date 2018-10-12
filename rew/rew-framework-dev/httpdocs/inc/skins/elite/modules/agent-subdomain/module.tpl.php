<?php if (!empty($agent)) {

	if (!empty($this->config('feature_image'))) $img  = $this->config('feature_image');
	if (!empty($img) && file_exists(DIR_FEATURED_IMAGES . $img)) {
		$img = URL_FEATURED_IMAGES . $img;
	}
?>
<div class="uk-cover-background <?= (!empty($img) ? 'agent-subdomain-header-wrapper' : ''); ?>" style="<?= (!empty($img) ? "background-image: url('" . $img . "');" : ""); ?>">
    <div id="agent-subdomain-header" class="uk-container uk-container-center<?= (empty($img) ? ' uk-margin-top' : ''); ?>">
        <div class="uk-grid uk-grid-small">
            <div class="uk-width-xsmall-1-1 uk-width-medium-1-6 agent-image-container">
                <?php if (!empty($agent['image'])) { ?>
                    <img class="uk-border-rounded"
                         src="/thumbs/194x230/uploads/agents/<?= Format::htmlspecialchars($agent['image']); ?>"
                         alt="<?= Format::htmlspecialchars($agent['name']); ?>">
                <?php } ?>
            </div>
            <div class="uk-width-xsmall-1-1 uk-width-medium-5-6">
                <h1><?= Format::htmlspecialchars($agent['name']); ?></h1>

                <ul class="uk-list">
                    <?= implode('', array_filter(array(
                        (!empty($agent['cell_phone']) ? '<li>Cell: <a href="tel:' . Format::htmlspecialchars($agent['cell_phone']) . '">' . Format::htmlspecialchars($agent['cell_phone']) . '</a></li>' : NULL),
                        (!empty($agent['office_phone']) ? '<li>Office: <a href="tel:' . Format::htmlspecialchars($agent['office_phone']) . '">' . Format::htmlspecialchars($agent['office_phone']) . '</a></li>' : NULL),
                    ))); ?>
                </ul>
                <?php if ($this->config('homepage') && !empty($agent['remarks'])) { ?>
                    <div>
                        <p class="truncate" data-truncate='{"count":200, "btnClass": "uk-button uk-button-primary"}'><?= $agent['remarks']; ?></p>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?= (empty($img) ? '<hr class="uk-article-divider">' : ''); ?>
    </div>
</div>
<?php } ?>
