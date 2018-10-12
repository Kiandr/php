<div class="bearer-of-criteria" data-title="<?= Format::htmlspecialchars($search_title); ?>" data-criteria="<?= Format::htmlspecialchars(json_encode($search_criteria)); ?>" data-count="<?= ((int) $results['count']); ?>"></div>

<h4>Matching...</h4>
<ul class="uk-list uk-list-space">
    <?php foreach ($searches as $key => $search) { ?>
    <li>
        <span class="accent"><?= Format::number($results[$key]); ?></span> <?= Format::htmlspecialchars($search['label']); ?>
        <a href="<?= Format::htmlspecialchars(Settings::getInstance()->SETTINGS['URL_IDX_SEARCH']); ?>?<?= Format::htmlspecialchars(http_build_query(array_merge($search_criteria, array('refine' => 'true'), array_filter(array($key => $search['value']))))); ?>"><i class="uk-float-right uk-icon-angle-right accent" aria-hidden="true"></i></a>
    </li>
    <?php } ?>
</ul>

<?php // Tags are not set to be hidden
    if ($this->config('hideTags') !== true) {
    // Search search tags
    $idx_tags = IDX_Panel::tags();
?>

<div class="idx-filters uk-hidden">
    <?php foreach ($idx_tags as $tag) { ?>
        <?php if (in_array($tag->getField(), $untaggable_fields)) continue; ?>
        <button class="uk-button uk-button-tertiary uk-margin-small-right idx-filter-remove js-idx-filter-remove-trigger" data-live-update="true" data-idx-tag="<?= Format::htmlspecialchars(json_encode($tag->getField())); ?>"><?= Format::htmlspecialchars($tag->getTitle()); ?></button>
  <?php } ?>
</div>

<?php } ?>
