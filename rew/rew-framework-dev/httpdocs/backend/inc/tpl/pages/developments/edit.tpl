<form action="?submit" method="post" class="rew_check">

    <input type="hidden" name="id" value="<?=Format::htmlspecialchars($development['id']); ?>">

    <div class="bar">
        <div class="bar__title">Edit <?=Format::htmlspecialchars($development['title']); ?></div>
        <div class="bar__actions">
            <a class="bar__action cancel" href="../">
                <svg class="icon">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"/>
                </svg>
            </a>
        </div>
    </div>

    <div class="btns btns--stickyB">
        <span class="R">
            <button class="btn btn--positive" type="submit">
                <svg class="icon">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use>
                </svg>
                Save
            </button>
        </span>
    </div>

    <div class="block">
        <div class="cols">
            <div class="field col w1/2">
                <label class="field__label">Development Title <em class="required">*</em></label>
                <input class="w1/1" name="title" value="<?=htmlspecialchars($development['title']); ?>" autofocus required>
            </div>

            <div class="field col w1/2">
                <label class="field__label">Sub-Title</label>
                <input class="w1/1" type="text" name="subtitle" value="<?=Format::htmlspecialchars($development['subtitle']); ?>">
            </div>
        </div>
        <div class="field">
            <label class="field__label">Development Link <em class="required">*</em></label>
            <input class="w1/1" name="link" value="<?=Format::slugify($development['link']); ?>" data-slugify required>
            <p class="text--mute">
                To be used as the development URL address. Use only lowercase alpha-numeric characters.
                <input class="w1/1" data-preview="input[name=link]" data-preview-value="/*.php" class="ui-state-highlight" value="" readonly>
            </p>
        </div>

        <div class="field">
            <label class="field__label">Development Description</label>
            <textarea class="w1/1" rows="6" name="description"><?=Format::htmlspecialchars($development['description']); ?></textarea>
        </div>
        <div class="cols">
            <div class="field col w3/4">
                <label class="field__label">Featured Community</label>
                <?php

                    // Display featured community input
                    $community_id = $development['community_id'];
                    echo '<select class="w1/1" name="community_id" data-selectize=\'' . json_encode([
                        'placeholder' => '-- No Community --',
                        'allowEmptyOption' => true
                    ]) . '\'>';
                    echo '<option value="">-- No Community --</option>' . PHP_EOL;
                    if (!empty($communities) && is_array($communities)) {
                        foreach ($communities as $community) {
                            echo sprintf(
                                '<option value="%1$s"%3$s>%2$s</option>',
                                Format::htmlspecialchars($community['id']),
                                Format::htmlspecialchars($community['title']),
                                ($community['id'] === $community_id ? ' selected' : '')
                            );
                        }
                    }
                    echo '</select>';

                ?>
            </div>

            <div class="field col w1/4">
                <label class="field__label">Tags &amp; Keywords</label>
                <?php

                    // Display tag input
                    $selected_tags = $development['tags'];
                    echo '<select class="w1/1" name="tags[]" multiple>';
                    if (!empty($tags) && is_array($tags)) {
                        foreach ($tags as $tag) {
                            echo sprintf(
                                '<option value="%2$s"%1$s>%2$s</option>',
                                in_array($tag, $selected_tags) ? ' selected' : '',
                                Format::htmlspecialchars($tag)
                            );
                        }
                    }
                    echo '</select>';

                ?>
            </div>
        </div>
        <div class="field">
            <label class="field__label">Is Enabled</label>
            <div>
                <label class="toggle">
                    <input type="radio" name="is_enabled" value="Y"<?=($development['is_enabled'] == 'Y') ? ' checked' : ''; ?>>
                    <span class="toggle__label">Yes</span>
                </label>
                <label class="toggle">
                    <input type="radio" name="is_enabled" value="N"<?=($development['is_enabled'] != 'Y') ? ' checked' : ''; ?>>
                    <span class="toggle__label">No</span>
                </label>
            </div>
        </div>

        <div class="field">
            <label class="field__label">Is Featured</label>
            <div>
                <label class="toggle">
                    <input<?=(($development['is_enabled'] != 'Y') ? ' disabled' : ''); ?> type="radio" name="is_featured" value="Y"<?=($development['is_featured'] == 'Y') ? ' checked' : ''; ?>>
                    <span class="toggle__label">Yes</span>
                </label>

                <label class="toggle">
                    <input<?=(($development['is_enabled'] != 'Y') ? ' disabled' : ''); ?> type="radio" name="is_featured" value="N"<?=($development['is_featured'] != 'Y') ? ' checked' : ''; ?>>
                    <span class="toggle__label">No</span>
                </label>
            </div>
        </div>
        <?php

        // Form fields
        $fieldsets = [
            ['heading' => 'Address / Location', 'fields' => [
                'address' => [
                    'label' => 'Street Address',
                    'type' => 'text'
                ],
                'city' => [

                    'label' => 'City',
                    'type' => 'text'
                ],
                'state' => [
                    'label' => 'State/Province',
                    'type' => 'text'
                ],
                'zip' => [
                    'label' => 'Zip/Postal Code',
                    'type' => 'text'
                ]
            ]],
            ['heading' => 'Development Info', 'fields' => [
                'website_url' => [
                    'label' => 'Website URL',
                    'placeholder' => 'http://',
                    'type' => 'url'
                ],
                'completion_status' => [
                    'label' => 'Completion Status',
                    'type' => 'text'
                ],
                'completion_date' => [
                    'label' => 'Completion Date',
                    'type' => 'text'
                ],
                'completion_is_partial' => [
                    'label' => 'Partial Completion',
                    'type' => 'toggle'
                ]
            ]],
            ['heading' => 'Building Information', 'fields' => [
                'num_units' => [
                    'label' => '# of Units',
                    'type' => 'number'
                ],
                'unit_min_price' => [
                    'label' => 'Unit Min. Price',
                    'type' => 'currency'
                ],
                'unit_max_price' => [
                    'label' => 'Unit Max. Price',
                    'type' => 'currency'
                ],
                'unit_styles' => [
                    'label' => 'Unit Styles',
                    'type' => 'text'
                ],
                'num_stories' => [
                    'label' => '# of Stories',
                    'type' => 'number'
                ]
            ]],
            ['heading' => 'Building Features', 'fields' => [
                'common_features' => [
                    'label' => 'Common Features',
                    'type' => 'text'
                ],
                'views' => [
                    'label' => 'Views Description',
                    'type' => 'text'
                ],
                'construction' => [
                    'label' => 'Construction',
                    'type' => 'text'
                ],
                'parking' => [
                    'label' => 'Parking',
                    'type' => 'text'
                ]
            ]],
            ['heading' => 'Meta Information', 'fields' => [
                'about_heading' => [
                    'label' => 'Description Heading',
                    'type' => 'text'
                ],
                'page_title' => [
                    'label' => tpl_lang('LBL_FORM_CMS_PAGE_TITLE'),
                    'hint' => tpl_lang('DESC_FORM_CMS_PAGE_TITLE'),
                    'type' => 'text'
                ],
                'meta_description' => [
                    'label' => tpl_lang('LBL_FORM_CMS_PAGE_DESCRIPTION'),
                    'hint' => tpl_lang('DESC_FORM_CMS_PAGE_DESCRIPTION'),
                    'type' => 'textarea'
                ]
            ]]
        ];

        foreach ($fieldsets as $fieldset) {
            echo '<div class="field">';
            if (!empty($fieldset['heading'])) {
                echo sprintf('<h2>%s</h2>', $fieldset['heading']);
            }
            if (!empty($fieldset['fields'])) {
                foreach ($fieldset['fields'] as $name => $field) {
                    $extra = '';
                    $input = $field['type'];
                    $value = Format::htmlspecialchars($development[$name]);
                    // Placeholder text
                    if (!empty($field['placeholder'])) {
                        $extra .= sprintf(' placeholder="%s"', $field['placeholder']);
                    }
                    // Currency input
                    if ($input === 'currency') {
                        $value = preg_replace('/[^0-9]/', '', $value);
                        $extra .= ' data-currency';
                        $input = 'text';
                    }
                    echo sprintf('<div class="field"><label class="field__label">%s%s</label>',
                        $field['className'],
                        $field['label']
                    );
                    if ($input === 'toggle') {
                        echo '<div>';
                        echo sprintf('<label class="toggle"><input type="radio" id="%1$s_Y" name="%1$s" value="Y"%2$s><span class="toggle__label"> Yes</span></label>', $name, $value == 'Y' ? ' checked' : '');
                        echo sprintf('<label class="toggle"><input type="radio" id="%1$s_N" name="%1$s" value="N"%2$s><span class="toggle__label"> No</span></label>', $name, $value != 'Y' ? ' checked' : '');
                        echo '</div>';
                    } elseif ($input === 'textarea') {
                        echo sprintf('<textarea class="w1/1" %s name="%s">%s</textarea>', $extra, $name, $value);
                    } else {
                        echo sprintf('<input class="w1/1" %s type="%s" name="%s" value="%s">', $extra, $input, $name, $value);
                    }
                    if (!empty($field['hint'])) {
                        echo sprintf('<p class="text--mute">%s</p>', $field['hint']);
                    }
                    echo '</div>';
                }
            }
            echo '</div>';
        }

        ?>
        <div class="field">
            <h3 class="panel__hd">MLS&reg; Listings</h3>
            <p class="text--mute">Configure IDX search criteria to find matching MLS&reg; listings to display.</p>
            <div id="search_criteria_toggle">
                <div>
                    <label class="toggle toggle--stacked" for="search_criteria_builder">
                        <input type="radio" name="search_criteria" id="search_criteria_builder" value="builder"<?=(!in_array($_REQUEST['search_criteria'], ['snippet', 'disabled']) ? ' checked' : ''); ?>>
                        <span class="toggle__label">Search Criteria</span>
                    </label>
                    <label class="toggle toggle--stacked" for="search_criteria_snippet">
                        <input type="radio" name="search_criteria" id="search_criteria_snippet" value="snippet"<?=($_REQUEST['search_criteria'] == 'snippet' ? ' checked' : ''); ?>>
                        <span class="toggle__label">IDX Snippet</span>
                    </label>
                    <label class="toggle toggle--stacked" for="search_criteria_disabled">
                        <input type="radio" name="search_criteria" id="search_criteria_disabled" value="disabled"<?=($_REQUEST['search_criteria'] == 'disabled' ? ' checked' : ''); ?>>
                        <span class="toggle__label">No Listings</span>
                    </label>
                </div>
            </div>
        </div>
        <div id="criteria-snippet" class="field<?=($_REQUEST['search_criteria'] == 'snippet' ? '' : ' hidden'); ?>">
            <div class="field">
                <label class="field__label">IDX Snippet</label>
                <select class="w1/1" name="idx_snippet_id">
                    <option value="">-- Select IDX Snippet --</option>
                    <?php

                        // Display list of available IDX snippets for use
                        if (!empty($idx_snippets) && is_array($idx_snippets)) {
                            $idx_snippet_id = $development['idx_snippet_id'];
                            foreach ($idx_snippets as $idx_snippet) {
                                echo sprintf(
                                    '<option value="%1$s"%3$s>#%2$s#</option>',
                                    Format::htmlspecialchars($idx_snippet['id']),
                                    Format::htmlspecialchars($idx_snippet['name']),
                                    $idx_snippet['id'] === $idx_snippet_id ? ' selected' : ''
                                );
                            }
                        }

                    ?>
                </select>
            </div>
        </div>
        <div id="criteria-builder" class="field<?=($_REQUEST['search_criteria'] == 'builder' ? '' : ' hidden'); ?>">
            <?php

                // Multi-IDX support
                $feeds = Settings::getInstance()->IDX_FEEDS;
                if (!empty($feeds)) {
                    echo '<div class="colset">';
                    echo '<h3 class="panel__hd">Choose IDX Feed</h3>';
                    echo '<div class="field">';
                    echo '<select class="w1/1" name="feed">';
                    echo '<option value="">-- Choose an IDX Feed --</option>';
                    foreach ($feeds as $feed => $settings) {
                        echo '<option ' . (Settings::getInstance()->IDX_FEED === $feed ? 'selected' : '') . ' value="' . $feed . '">' . $settings['title'] . '</option>';
                    }
                    echo '</select>';
                    echo '</div>';
                    echo '</div>';
                } else {
                    echo sprintf(
                        '<input type="hidden" name="feed" value="%s">',
                        Settings::getInstance()->IDX_FEED
                    );
                }

                // Render IDX builder search panels
                echo $this->view->render('::partials/idx/builder', [
                    'builder' => $builder,
                    'page' => $page
                ]);

            ?>
        </div>
        <h3 class="panel__hd">Photo Gallery</h3>
        <p class="text--mute">Uploads must be in gif, jpeg, or png format and under 4 MB in size. Drag and drop photos to re-arrange.</p>
        <div class="field">
            <div data-uploader='<?=json_encode([
                'extraParams' => [
                    'type' => 'development',
                    'row' => (int) $development['id']
                ]
            ]); ?>'>
                <?php if (!empty($uploads)) { ?>
                <div class="file-manager">
                    <ul>
                        <?php foreach ($uploads as $upload) { ?>
                        <li>
                            <div class="wrap">
                                <img src="/thumbs/95x95/uploads/<?=$upload['file']; ?>" border="0">
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
