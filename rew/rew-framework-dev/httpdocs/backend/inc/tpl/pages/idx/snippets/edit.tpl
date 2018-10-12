<form id="idx-builder-form" action="?submit" method="post" class="rew_check">
    <input type="hidden" name="feed" value="<?=Format::htmlspecialchars(Settings::getInstance()->IDX_FEED); ?>">
    <input type="hidden" name="id" value="<?=Format::htmlspecialchars($snippet['name']); ?>">
    <input type="hidden" name="map[longitude]" value="">
    <input type="hidden" name="map[latitude]" value="">
    <input type="hidden" name="map[zoom]" value="">

    <div class="menu menu--drop menu--copy hidden" id="menu--ellipses" style="min-width: 0;">
        <ul class="menu__list">
            <li class="menu__item">
                <a class="menu__link" href="<?=URL_BACKEND; ?>cms/snippets/copy/?id=<?=urlencode($snippet['name']); ?><?=$subdomain->getPostLink(true); ?>">
                    <svg class="icon">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-copy"></use>
                    </svg>
                    <?= __('Copy'); ?>
                </a>
            </li>
        </ul>
    </div>

    <div class="bar">
        <span class="bar__title">#<?=Format::htmlspecialchars($snippet['name']); ?>#</span>
        <div class="bar__actions">
            <a class="bar__action" data-drop="#menu--ellipses">
                <svg class="icon">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-ellipses"></use>
                </svg>
            </a>
            <a class="bar__action timeline__back" href="<?=URL_BACKEND . 'cms/snippets/?filter=idx' . $subdomain->getPostLink(true); ?>">
                <svg class="icon icon-left-a mar0">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use>
                </svg>
            </a>
        </div>
    </div>

    <div class="block">
        <?php
            echo $this->view->render('::partials/subdomain/selector', [
                'subdomain' => $subdomain,
            ]);
        ?>
    </div>

    <div class="block">
        <div class="field">
            <label class="field__label"><?= __('Snippet Title'); ?> <em class="required">*</em></label>
            <input class="w1/1" name="snippet_title" id="snippet_title" value="<?=Format::htmlspecialchars($criteria['snippet_title']); ?>" required>
            <p class="tip"><?= __('The snippet title will be included in the page\'s heading.'); ?></p>
        </div>
        <div class="field">
            <label class="field__label"><?=tpl_lang('LBL_FORM_CMS_SNIP_NAME'); ?> <em class="required">*</em></label>
            <input class="w1/1" name="snippet_id" id="snippet_id" value="<?=Format::htmlspecialchars($snippet['name']); ?>" data-slugify required>
            <p class="tip"><?=tpl_lang('DESC_FORM_CMS_SNIP_NAME'); ?></p>
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
                    <input class="w1/1" type="number" name="page_limit" value="<?=Format::htmlspecialchars($criteria['page_limit']); ?>" min="1" max="48">
                </div>
                <div class="col w1/2 field">
                    <label class="field__label"><?= __('Sort Results By'); ?></label>
                    <select class="w1/1" name="sort_by">
                        <?php foreach ($builder->getSortOptions() as $option) { ?>
                            <option value="<?=$option['value']; ?>"<?=($criteria['sort_by'] == $option['value']) ? ' selected' : ''; ?>><?=Format::htmlspecialchars($option['title']); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <?php

                // For Previously Saved Values
                if ($criteria['view'] == 'map') {
                    $criteria['view'] = 'grid';
                    $_REQUEST['map']['open'] = 1;
                }

            ?>
            <?php if ($viewOptions = $builder->getViewOptions()) { ?>
                <div class="field">
                    <label class="field__label"><?= __('Default View'); ?></label>
                    <?php foreach ($viewOptions as $option) { ?>
                        <label class="toggle">
                            <input type="radio" name="view" value="<?=$option['value']; ?>"<?=($criteria['view'] == $option['value']) ? ' checked' : ''; ?>>
                            <span class="toggle__label"><?=$option['title']; ?></span>
                        </label>
                    <?php } ?>
                </div>
            <?php } ?>
            <h3>
                <?= __('Map'); ?>
                <span class="R">
                    <label class="toggle" for="map_on">
                        <input type="radio" name="map[open]" value="1"<?=(!empty($criteria['map']['open']) ? ' checked' : ''); ?>>
                        <span class="toggle__label"><?= __($mapLabels['open']); ?></span>
                    </label>
                    <label class="toggle" for="map_off">
                        <input type="radio" name="map[open]" value="0"<?=(empty($criteria['map']['open']) ? ' checked' : ''); ?>>
                        <san class="toggle__label"><?= __($mapLabels['closed']); ?></san>
                    </label>
                </span>
            </h3>
            <?=(!empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING']) ? '<div id="idx-builder-map"></div>' : ''); ?>
            <div class="cols">
                <div class="col w1/2 field">
                    <label class="field__label"><?= __('Show Price Range Links'); ?></label>
                    <label class="toggle">
                        <input type="radio" name="price_ranges" value="true"<?=($criteria['price_ranges'] == 'true') ? ' checked' : ''; ?>>
                        <span class="toggle__label"><?= __('Yes'); ?></span>
                    </label>
                    <label class="toggle">
                        <input type="radio" name="price_ranges" value="false"<?=($criteria['price_ranges'] == 'false') ? ' checked' : ''; ?>>
                        <span class="toggle__label"><?= __('No'); ?></span>
                    </label>
                    <p class="tip"><?= __('Automatically generate and display links for this snippet\'s price ranges.'); ?></p>
                </div>
                <?php if (Skin::hasFeature(Skin::HIDE_SEARCH_TAGS)) { ?>
                    <div class="col w1/2 field">
                        <label class="field__label"><?= __('Hide Search Tags'); ?></label>
                        <label class="toggle">
                            <input type="radio" name="hide_tags" value="true"<?=($criteria['hide_tags'] == 'true') ? ' checked' : ''; ?>>
                            <span class="toggle__label"><?= __('Yes'); ?></span>
                        </label>
                        <label class="toggle">
                            <input type="radio" name="hide_tags" value="false"<?=($criteria['hide_tags'] == 'false') ? ' checked' : ''; ?>>
                            <span class="toggle__label"><?= __('No'); ?></span>
                        </label>
                        <p class="tip"><?= __('Hides search criteria used to generate search results as tags.'); ?></p>
                    </div>
                <?php } ?>
            </div>

            <h3><?= __('Pages Used On'); ?></h3>
            <div class="field">
                <?php if (!empty($snippet['pages'])) { ?>
                    <ul class="checklist">
                        <?php foreach ($snippet['pages'] as $pg) { ?>
                            <li>
                                <div class="item_content_ico ico ico-page"></div>
                                <a href="<?=$pg['href']; ?>"><?=Format::htmlspecialchars($pg['text']); ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                    <p><?= __('This snippet is currently not being used on any pages.'); ?></p>
                <?php } ?>
            </div>

        </div>
    </div>

    <div class="btns btns--stickyB">
        <span class="R">
            <button class="btn btn--positive" type="submit">
                <svg class="icon icon-check mar0">
                    <use xlink:href="/backend/img/icos.svg#icon-check"/>
                </svg>
                <?= __('Save'); ?>
            </button>
            <a class="btn copy" href="<?=URL_BACKEND; ?>cms/snippets/copy/?id=<?=$snippet['name']; ?>"><?= __('Copy'); ?></a>
        </span>
    </div>

</form>
