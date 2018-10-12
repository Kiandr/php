<div class="fw-idx-filter-container<?= !$show_advanced ? ' uk-hidden' : ''; ?>" data-ajax-url="<?= Format::htmlspecialchars($ajax_url); ?>" data-ajax-current-request="<?= Format::htmlspecialchars($current_request); ?>">
    <div class="fw fw-idx-filter">
        <div class="uk-container uk-container-center">
            <h3 class="close-filter"><a class="js-advanced-search-trigger search-toggle uk-margin-right">Close <i class="uk-icon uk-icon-remove"></i></a></h3>
            <div class="uk-grid uk-grid-collapse uk-margin-top" id="sub-quicksearch">
                <div class="uk-width-1-1">
                    <div class="uk-grid top-grid">
                        <div class="uk-width-1-1 uk-width-large-3-4">
                            <form class="uk-form idx-search idx-search-advanced" action="<?= $this->getPage()->info('app') == 'idx-map' ? Format::htmlspecialchars(Settings::getInstance()->URLS['URL_IDX_MAP']) : Format::htmlspecialchars(Settings::getInstance()->SETTINGS['URL_IDX_SEARCH']); ?>">
                                <input type="hidden" name="feed" value="<?= (Settings::getInstance()->IDX_FEED == 'cms' ? Settings::getInstance()->IDX_FEED_DEFAULT : Settings::getInstance()->IDX_FEED); ?>" />
                                <?php if ($_REQUEST['saved_search_id']) { ?>
                                    <input type="hidden" name="refine" value="true">
                                <?php } ?>
                                <?php
                                // Mirror Primary Quick Search's Values in Advanced Form
                                $mirror_fields = $this->getPage()->getSkin()->getQsMirroredFields();
                                if (!empty($mirror_fields)) {
                                    foreach ($mirror_fields as $field) {
                                        echo '<input type="hidden" name="' . $field . '" value="">';
                                    }
                                }
                                ?>
                                <?php foreach ($displayPanels as $panelGroup => $panels) { ?>
                                    <div class="idx-filter-col idx-filter-col-<?= $panelGroup; ?>">
                                        <div class="uk-grid">
                                            <div class="uk-width-1-1 uk-width-large-1-4">
                                                <h4>
                                                    <a class="js-panel-collapse-trigger" data-group="<?= $panelGroup; ?>">
                                                        <?= Format::htmlspecialchars($groupLabels[$panelGroup]); ?>
                                                        <i class="uk-icon-angle-down"></i>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div class="uk-width-1-1 uk-width-large-3-4">
                                                <div class="uk-grid uk-grid-small toggle-panel" data-group="<?= $panelGroup; ?>">
                                                    <?php foreach ($panels as $panel) { ?>
                                                        <?php $panel->display(); ?>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </form>
                            <?php // Tags are not set to be hidden
                            if ($this->config('hideTags') !== true) {
                                // Search search tags
                                $idx_tags = IDX_Panel::tags();
                                ?>
                                <div class="uk-grid bottom-grid">
                                    <div class="uk-width-1-1">
                                        <div class="filter-tags live">
                                            <h4 class="uk-float-left adv-search-h4">Remove Filters</h4>
                                            <div class="idx-filters">
                                                <?php foreach ($idx_tags as $tag) { ?>
                                                    <button class="uk-button uk-button-tertiary idx-filter-remove uk-margin-small-right js-idx-filter-remove-trigger" data-live-update="true" data-idx-tag="<?= Format::htmlspecialchars(json_encode($tag->getField())); ?>"><?= Format::htmlspecialchars($tag->getTitle()); ?></button>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="uk-width-1-1 uk-width-large-1-4 filter-update-col">
                            <div class="uk-grid">
                                <div class="uk-width-1-1 uk-width-medium-1-3 uk-width-large-1-1">
                                    <div class="adv-search-panel" data-ajax-url="<?= Format::htmlspecialchars(Settings::getInstance()->SETTINGS['URL_IDX_AJAX']); ?>html.php?module=<?= Format::htmlspecialchars($this->getId()); ?>&options[controller]=ajax-count.php&options[template]=ajax-count.tpl.php">
                                        <?php (new Module($this->getId(), array('controller' => 'ajax-count.php', 'template' => 'ajax-count.tpl.php')))->display(); ?>
                                    </div>
                                    <button class="uk-button uk-width-1-1" data-submit-form=".idx-search:first" data-lang-updating-results="Updating Results">Update Results</button>
                                </div>
                                <div class="uk-width-1-1 uk-width-medium-1-3 uk-width-large-1-1 feed-switcher">
                                    <?php
                                    $this->getPage()->addContainer('idx-feeds')->addModule('idx-feeds', array(
                                        'template' => 'idx-search.tpl'
                                    ))->display();
                                    ?>
                                </div>
                                <div class="uk-width-1-1 uk-width-medium-1-3 uk-width-large-1-1">
                                    <?php
                                    $this->getPage()->addContainer('user-info')->addModule('user-info', array(
                                        'template' => 'search.tpl.php',
                                        'user' => $user
                                    ))->display();
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
