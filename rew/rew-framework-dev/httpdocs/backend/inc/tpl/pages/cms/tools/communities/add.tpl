<form id="idx-builder-form" action="?submit" method="post" class="rew_check">
    <input type="hidden" name="feed" value="<?=Settings::getInstance()->IDX_FEED;?>">

    <div class="bar">
        <div class="bar__title"><?= __('Add Featured Community'); ?></div>
        <div class="bar__actions">
            <a class="bar__action" href="/backend/cms/tools/communities/">
                <svg class="icon">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use>
                </svg>
            </a>
        </div>
    </div>

    <div class="btns btns--stickyB">
        <span class="R">
            <button class="btn btn--positive" type="submit">
                <svg class="icon icon-check mar0">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use>
                </svg>
                <?= __('Save'); ?>
            </button>
        </span>
    </div>

    <div class="block">

        <div class="field">
            <label class="field__label"><?= __('Community Title'); ?> <em class="required">*</em></label>
            <input class="w1/1" type="text" name="title" value="<?=Format::htmlspecialchars($_POST['title']); ?>" autofocus required>
        </div>

        <div class="field">
            <label class="field__label"><?= __('Sub-Title'); ?></label>
            <input class="w1/1" type="text" name="subtitle" id="subtitle" value="<?=Format::htmlspecialchars($_POST['subtitle']); ?>" maxlength="100">
            <span class="text--small text--mute">
              <span id="subtitleCount"></span>/<span id="subtitleMax"></span> characters remaining.
            </span>
        </div>

        <div class="field">
            <label class="field__label"><?= __('Community Description'); ?></label>
            <?php

                // Community description input
                echo sprintf('<textarea class="w1/1" rows="6" name="description"%s>%s</textarea>',
                    $char_limit ? sprintf(' maxlength="%s"', $char_limit) : '',
                    Format::htmlspecialchars($_POST['description'])
                );

            ?>
        </div>

        <div class="field">
            <label class="field__label"><?= __('Snippet Name'); ?> <em class="required">*</em></label>
            <input class="w1/1" type="text" name="snippet" value="<?=Format::htmlspecialchars($_POST['snippet']); ?>" data-slugify required>
        </div>

        <?php

            // Community tags
            if (!empty($can_tag)) {
                echo '<div class="field">';
                echo '<label class="field__label">' . __('Tags &amp; Keywords') . '</label>';
                echo '<select class="w1/1" id="tags" name="tags[]" multiple>';
                if (!empty($community_tags) && is_array($community_tags)) {
                    foreach ($community_tags as $community_tag) {
                        $selected = is_array($_POST['tags']) && in_array($community_tag, $_POST['tags']) ? ' selected' : '';
                        echo '<option value="' . Format::htmlspecialchars($community_tag) . '"' . $selected . '>';
                        echo Format::htmlspecialchars($community_tag);
                        echo '</option>';
                    }
                }
                echo '</select>';
                echo '</div>';
            }

            // Available CMS pages
            if (!empty($page_select)) {
                echo '<div class="field">';
                echo '<label class="field__label">' . __('Community Page') . '</label>';
                echo '<select class="w1/1" name="page_id" data-selectize>';
                echo '<option value="">-- ' . __('Select a Page') . ' --</option>';

                // Top level pages
                if (!empty($cms_pages) && is_array($cms_pages)) {
                    echo '<optgroup label="Top Level Pages">';
                    foreach ($cms_pages as $cms_page) {
                        $selected = $_POST['page_id'] == $cms_page['page_id'] ? ' selected' : '';
                        echo '<option value="' . $cms_page['page_id'] . '"' . $selected . '>';
                        echo Format::htmlspecialchars($cms_page['link_name']);
                        echo '</option>';
                    }
                    echo '</optgroup>';
                }

                // CMS sub-pages
                if (!empty($cms_subpages) && is_array($cms_subpages)) {
                    foreach ($cms_subpages as $optgroup => $subpages) {
                        echo '<optgroup label="' . Format::htmlspecialchars($optgroup) . '">';
                        foreach ($subpages as $cms_subpage) {
                            $selected = $_POST['page_id'] == $cms_subpage['page_id'] ? ' selected' : '';
                            echo '<option value="' . $cms_subpage['page_id'] . '"' . $selected . '>';
                            echo Format::htmlspecialchars($cms_subpage['link_name']);
                            echo '</option>';
                        }
                        echo '<optgroup>';
                    }
                }
                echo '</select>';
                echo '</div>';

            }

        ?>

        <?php if (!empty($has_video_link_feature)) { ?>
            <div class="field">
                <label class="field__label"><?= __('Video Link'); ?></label>
                <input class="w1/1" name="video_link" value="<?=Format::htmlspecialchars($_POST['video_link']); ?>">
            </div>
        <?php } ?>

        <div class="field">
            <label class="field__label"><?= __('Is Enabled'); ?></label>
            <label class="toggle">
                <input type="radio" name="is_enabled" value="Y"<?=($_POST['is_enabled'] != 'N') ? ' checked' : ''; ?>>
                <span class="toggle__label"><?= __('Yes'); ?></span>
            </label>
            <label class="toggle">
                <input type="radio" name="is_enabled" value="N"<?=($_POST['is_enabled'] == 'N') ? ' checked' : ''; ?>>
                <span class="toggle__label"><?= __('No'); ?></span>
            </label>
        </div>
        <h3 class="panel__hd text"><?= __('Statistics Heading &amp; Labels'); ?></h2>
        <?php if ($nonelite_headings) { ?>

            <div class="field">
                <label class="field__label"><?= __('Statistics Heading'); ?></label>
                <input class="w1/1" type="text" name="stats_heading" value="<?=Format::htmlspecialchars($_POST['stats_heading']); ?>">
                <label class="hint"><?= __('(Default: Real Estate Statistics)'); ?></label>
            </div>

        <?php } ?>

        <div class="field">
            <label class="field__label"><?= __('Total Listings'); ?></label>
            <input class="w1/1" type="text" name="stats_total" value="<?=Format::htmlspecialchars($_POST['stats_total']); ?>">
        </div>

        <div class="field">
            <label class="field__label"><?= __('Average Price'); ?></label>
            <input class="w1/1" type="text" name="stats_average" value="<?=Format::htmlspecialchars($_POST['stats_average']); ?>">
        </div>

        <?php if ($nonelite_headings) { ?>

            <div class="field">
                <label class="field__label"><?= __('Highest Listing Price'); ?></label>
                <input class="w1/1" type="text" name="stats_highest" value="<?=Format::htmlspecialchars($_POST['stats_highest']); ?>">
            </div>

            <div class="field">
                <label class="field__label"><?= __('Lowest Listing Price'); ?></label>
                <input class="w1/1" type="text" name="stats_lowest" value="<?=Format::htmlspecialchars($_POST['stats_lowest']); ?>">
            </div>

		<?php } ?>

        <?php if (!empty($anchor_links)) { ?>

            <h3 class="panel__hd"><?= __('Anchor Links'); ?></h3>

            <div class="field">
                <label class="field__label"><?= __('Left Anchor Text'); ?></label>
                <input class="w1/1" type="text" name="anchor_one_text" value="<?=Format::htmlspecialchars($_POST['anchor_one_text']); ?>">
                <label class="hint"><?= __('(Default: Community Summary)'); ?></label>
            </div>

            <div class="field">
                <label class="field__label"><?= __('Right Anchor Text'); ?></label>
                <input class="w1/1" type="text" name="anchor_two_text" value="<?=Format::htmlspecialchars($_POST['anchor_two_text']); ?>">
                <label class="hint"><?= __('(Default: Homes for Sale)'); ?></label>
            </div>

            <div class="field">
                <label class="field__label"><?= __('Left Anchor Link'); ?></label>
                <input class="w1/1" type="text" name="anchor_one_link" value="<?=Format::htmlspecialchars($_POST['anchor_one_link']); ?>">
                <label class="hint"><?= __('(Default: #community-summary)'); ?></label>
            </div>

            <div class="field">
                <label class="field__label"><?= __('Right Anchor Link'); ?></label>
                <input class="w1/1" type="text" name="anchor_two_link" value="<?=Format::htmlspecialchars($_POST['anchor_two_link']); ?>">
                <label class="hint"><?= __('(Default: #homes-for-sale)'); ?></label>
            </div>

        <?php } ?>

        <h3 class="panel__hd">MLS® Listings</h3>

        <div class="field" id="search_criteria_toggle">
            <div class="-marB8">
                <label class="toggle">
                    <input type="radio" name="search_criteria" value="true"<?=(empty($_POST['idx_snippet'])) ? ' checked' : ''; ?>>
                    <span class="toggle__label"><?= __('Use Search Criteria'); ?></span>
                </label>
            </div>
            <div class="-marB8">
                <label class="toggle">
                    <input type="radio" name="search_criteria" value="false"<?=(!empty($_POST['idx_snippet'])) ? ' checked' : ''; ?>>
                    <span class="toggle__label"><?= __('IDX Snippet'); ?></label>
                </label>
            </div>
            <p class="text--mute"><?= __('Choose whether community will use an existing IDX snippet\'s search criteria or the search criteria here.'); ?></p>
        </div>

        <div id="idx_snippet_panel" class="<?=(empty($_POST['idx_snippet'])) ? 'hidden' : ''; ?>">
            <h3><?= __('IDX Snippet'); ?></h3>
            <div class="field">
                <label class="field__label">IDX Snippet</label>
                <select class="w1/1" name="idx_snippet">
                    <option value="" <?=(empty($_POST['idx_snippet']) ? ' selected' : ''); ?>><?= __('Select IDX Snippet'); ?>...</option>
                    <?php foreach ($idx_snippets as $idx_snippet) { ?>
                        <option value="<?=$idx_snippet['id']; ?>" <?=($_POST['idx_snippet'] == $idx_snippet['id'] ? ' selected' : ''); ?>>
                            <?=Format::htmlspecialchars($idx_snippet['name']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div id="search_criteria_panel" class="<?=(!empty($_POST['idx_snippet'])) ? 'hidden' : ''; ?>">
            <?php

                // Render IDX builder search panels
                echo $this->view->render('::partials/idx/builder', [
                    'builder' => $builder,
                    'page' => $page
                ]);

            ?>
        </div>

        <h3 class="panel__hd"><?= __('Community Photos'); ?></h3>
        <div class="field text--mute">
            <p><?= __('Uploads must be in gif, jpeg, or png format and under 4 MB in size. Drag and drop photos to re-arrange.'); ?></p>
            <p><?= __('Recommended Dimensions: 416 pixels by 284 pixels'); ?></p>
        </div>
        <div class="field">
            <div data-uploader='<?=json_encode([
                'inputName' => 'uploads[]',
                'extraParams' => ['type' => 'community']
            ]); ?>'>
                <?php if (!empty($uploads)) { ?>
                <div class="file-manager">
                    <ul>
                        <?php foreach ($uploads as $upload) { ?>
                        <li>
                            <div class="wrap"> <img src="/thumbs/95x95/uploads/<?=$upload['file']; ?>" border="0">
                                <input type="hidden" name="uploads[]" value="<?=$upload['id']; ?>">
                            </div>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
                <?php } ?>
            </div>
        </div>

    </div>
</form>
