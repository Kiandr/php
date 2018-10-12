<form id="idx-builder-form" action="?submit" method="post" class="rew_check">
    <input type="hidden" name="feed" value="<?=Format::htmlspecialchars(Settings::getInstance()->IDX_FEED); ?>">
    <input type="hidden" name="split" value="<?=Format::htmlspecialchars($search['split']); ?>">
    <input type="hidden" name="search_id" value="<?=$search['id']; ?>">
    <input type="hidden" name="map[longitude]" value="">
    <input type="hidden" name="map[latitude]" value="">
    <input type="hidden" name="map[zoom]" value="">

    <div class="bar">
        <div class="bar__title"><?=Format::htmlspecialchars($search['title']); ?></div>
        <div class="bar__actions">
            <a class="bar__action" href="/backend/idx/">
                <svg class="icon">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use>
                </svg>
            </a>
        </div>
    </div>

    <div class="block">
        <div class="field">
            <label class="field__label"><?= __('Search Title'); ?> <em class="required">*</em></label>
            <input class="w1/1" name="title" value="<?=Format::htmlspecialchars($search['title']); ?>" required>
            <p style="word-wrap: break-all; -ms-word-break: break-all; width: 100%; overflow: hidden;">
                <a href="<?=sprintf(Settings::getInstance()->SETTINGS['URL_IDX_SAVED_SEARCH'], md5($search['id'])); ?>" target="_blank">
                    <?=parse_url(sprintf(Settings::getInstance()->SETTINGS['URL_IDX_SAVED_SEARCH'], md5($search['id'])),PHP_URL_PATH); ?>
                </a>
            </p>
        </div>
        <?php

            // Render IDX builder search panels
            echo $this->view->render('::partials/idx/builder', [
                'builder' => $builder,
                'page' => $page
            ]);

        ?>
        <div class="block block--bg">
            <div class="kicker">
                <span class="ttl"><?= __('Settings'); ?></span>
            </div>
            <div class="cols">
                <div class="col w1/2 field">
                    <label class="field__label"><?= __('Results per Page'); ?> <em class="required">*</em></label>
                    <input class="w1/1" type="number" name="page_limit" value="<?=Format::htmlspecialchars($search['page_limit']); ?>" min="1" max="48">
                </div>
                <div class="col w1/2 field">
                    <label class="field__label"><?= __('Sort Results By'); ?></label>
                    <select class="w1/1" name="sort_by">
                        <?php foreach ($builder->getSortOptions() as $option) { ?>
                            <option value="<?=$option['value']; ?>"<?=($search['sort_by'] == $option['value']) ? ' selected' : ''; ?>><?=Format::htmlspecialchars($option['title']); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <?php if ($viewOptions = $builder->getViewOptions()) { ?>
                <div class="field">
                    <label class="field__label"><?= __('Default View'); ?></label>
                    <?php foreach ($viewOptions as $option) { ?>
                        <label class="toggle">
                            <input type="radio" name="view" value="<?=$option['value']; ?>"<?=($search['view'] == $option['value']) ? ' checked' : ''; ?>>
                            <span class="toggle__label"><?=Format::htmlspecialchars($option['title']); ?></span>
                        </label>
                    <?php } ?>
                </div>
            <?php } ?>
            <h3>
                <?= __('Map'); ?>
                <span class="R">
                    <label class="toggle">
                        <input type="radio" name="map[open]" value="1"<?=(!empty($_REQUEST['map']['open']) ? ' checked' : ''); ?>>
                        <span class="toggle__label"><?= __('Open'); ?></span>
                    </label>
                    <label class="toggle">
                        <input type="radio" name="map[open]" value="0"<?=(empty($_REQUEST['map']['open']) ? ' checked' : ''); ?>>
                        <span class="toggle__label"><?= __('Closed'); ?></span>
                    </label>
                </span>
            </h3>
            <?=(!empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING']) ? '<div id="idx-builder-map"></div>' : ''); ?>
            <?php

                // For Previously Saved Values
                if ($search['view'] == 'map') {
                    $search['view'] = 'grid';
                    $_REQUEST['map']['open'] = 1;
                }

            ?>
        </div>
    </div>

    <div class="btns btns--stickyB">
        <div class="R">
            <button class="btn btn--positive" type="submit">
                <svg class="icon icon-check mar0">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use>
                </svg>
                <?= __('Save'); ?>
            </button>
        </div>
    </div>

</form>
