<form id="bdx-builder-form" action="?submit" method="post">

    <input type="hidden" name="id" value="<?=$snippet['name']; ?>">

    <div class="hidden" id="cities" data-value="<?=json_encode($criteria['search']['City']);?>"></div>

    <section>

        <div class="bar">
            <span>#<?=$snippet['name']; ?>#</span>
        </div>

        <div class="btns btns--stickyB">
            <span class="R">
                <button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xlink:href="/backend/img/icos.svg#icon-check"/></svg> Save</button>
                <a class="btn copy" href="<?=URL_BACKEND; ?>cms/snippets/copy/?id=<?=$snippet['name']; ?>">Copy</a>
            </span>
        </div>

        <section class="block">

            <div class="field">
                <?php if (!empty($states)) { ?>
                    <?php if (count($states) > 1) { ?>
                        <select name="state" required class="w1/1">
                            <option value="">Select a State</option>
                            <?php foreach ($states as $state) { ?>
                                <option value="<?=$state;?>"<?=($criteria['state'] == $state ? 'selected="selected"' : '');?>><?=$statesUSA[$state];?></option>
                            <?php } ?>
                        </select>
                    <?php } else { ?>
                        <input type="hidden" name="state" value="<?=$states[0];?>">
                    <?php } ?>
                <?php } else { ?>
                    <select name="state" required class="w1/1">
                        <option value="">Select a State</option>
                            <?php foreach($statesUSA as $key => $val) { ?>
                                <option value="<?=$key;?>"<?=($criteria['state'] == $key ? 'selected="selected"' : '');?>><?=$val;?></option>
                            <?php } ?>
                    </select>
                <?php } ?>
            </div>

            <div id="bdx-panels-container">

                <h2>Add Search Criteria</h2>

                <div id="bdx-builder-panels">
                    <?php foreach ($bdx_panels as $name => $field) { ?>

                        <div class="field">
                            <?php if (!empty($field['title'])) { ?>
                                <label class="field__label"><?=htmlspecialchars($field['title']);?></label>
                            <?php } ?>

                            <div class="details panel-<?=$name;?>">

                                <?php $current = !empty($criteria['search'][$name]) ? $criteria['search'][$name] : false; ?>

                                <?php $options = $field['options']; ?>
                                <?php if (!empty($options)) { ?>
                                    <?php if($field['multiple']) { ?>
                                        <?php if (is_array($options)) { ?>
                                            <?php foreach ($options as $option) { ?>
                                                <?php $checked = ($current == $option['value'] || is_array($current) && in_array($option['value'], $current)) ? ' checked' : ''; ?>
                                                <label><input type="checkbox" value="<?=$option['value'];?>" <?=$checked;?> name="search[<?=htmlspecialchars($name);?>][]"> <?=$option['title'];?></label>
                                            <?php } ?>
                                        <?php } elseif (is_string($options)) {
                                                $filter = is_callable($field['filter']) ? $field['filter'] : false;
                                                $options = $db_bdx->query($options);
                                                while ($option = $options->fetch()) {
                                                    if (!empty($filter)) $option = $filter($option);
                                                    $checked = ($current == $option['value'] || is_array($current) && in_array($option['value'], $current)) ? ' checked' : ''; ?>
                                                    <label><input type="checkbox" value="<?=$option['value'];?>" <?=$checked;?> name="search[<?=htmlspecialchars($name);?>][]"> <?=$option['title'];?></label>
                                                <?php } ?>
                                            <?php } ?>
                                    <?php } else { ?>
                                        <select name="search[<?=htmlspecialchars($name);?>]" class="w1/1">
                                            <?php $placeholder = $field['placeholder']; ?>
                                            <?php if (!empty($placeholder) && is_string($placeholder)) { ?>
                                                <option value=""><?=htmlspecialchars($placeholder);?></option>
                                            <?php } ?>
                                            <?php if (is_array($options)) { ?>
                                                <?php foreach ($options as $option) { ?>
                                                    <option value="<?=htmlspecialchars($option['value']);?>"<?=($current == $option['value'] || is_array($current) && in_array($option['value'], $current)) ? ' selected' : '';?>><?=htmlspecialchars($option['title']);?></option>
                                                <?php } ?>
                                            <?php } elseif (is_string($options)) {
                                                $filter = is_callable($field['filter']) ? $field['filter'] : false;
                                                $options = $app->db_bdx->query($options);
                                                while ($option = $options->fetch()) {
                                                    if (!empty($filter)) $option = $filter($option);
                                                    $selected = ($current == $option['value'] || is_array($current) && in_array($option['value'], $current)) ? ' selected' : ''; ?>
                                                    <option value="<?=htmlspecialchars($option['value']);?>"<?=$selected;?>><?=htmlspecialchars($option['title']);?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    <?php } ?>
                                <?php } else {
                                    $placeholder = !empty($field['placeholder']) && is_string($field['placeholder']) ? ' placeholder="' . htmlspecialchars($field['placeholder']) . '"' : false; ?>
                                    <input class="w1/1" type="text" <?=(!empty($field['autocomplete']) ? 'class="bdx-autocomplete"' : '');?> name="search[<?=htmlspecialchars($name);?>]" value="<?=is_array($current) ? htmlspecialchars(implode(',', $current)) : htmlspecialchars($current);?>" <?=$placeholder;?>>
                                <?php } ?>
                            </div>

                        </div>
                    <?php } ?>
                </div>
            </div>

            <div>


                    <h2>Snippet Settings</h2>

                    <div class="field">
                        <label class="field__label"><?=tpl_lang('LBL_FORM_CMS_SNIP_NAME'); ?> <em class="required">*</em></label>
                        <input class="search_input w1/1" name="snippet_id" id="snippet_id" value="<?=htmlspecialchars($criteria['snippet_id']); ?>" required>
                        <p class="tip"><?=tpl_lang('DESC_FORM_CMS_SNIP_NAME'); ?></p>
                    </div>

                    <div class="field">
                        <label class="field__label">Snippet Title <em class="required">*</em></label>
                        <input class="search_input w1/1" name="snippet_title" id="snippet_title" value="<?=htmlspecialchars($criteria['snippet_title']); ?>" required>
                        <p class="tip">The snippet title will be included in the page's heading.</p>
                    </div>


                    <div class="field">
                        <label class="field__label">Results per Page <em class="required">*</em></label>
                        <input class="w1/1" type="number" name="search[page_limit]" value="<?=$criteria['search']['page_limit']; ?>" min="1" max="48">
                    </div>

                    <div class="field">
                        <label class="field__label">Sort Results By</label>
                        <select name="search[sort_by]" class="w1/1">
                            <?php // Community Sort Options ?>
                            <?php foreach ($sort_options['community'] as $option) { ?>
                                <option class="community-sort<?=($criteria['snippet_mode'] != 'communities' ? ' hidden' : '');?>"<?=($criteria['snippet_mode'] != 'communities' ? ' disabled="disabled"' : '');?> value="<?=$option['value']; ?>"<?=($criteria['search']['sort_by'] == $option['value']) ? ' selected' : ''; ?>><?=$option['title']; ?></option>
                            <?php } ?>
                            <?php // Home Sort Options ?>
                            <?php foreach ($sort_options['home'] as $option) { ?>
                                <option class="home-sort<?=($criteria['snippet_mode'] == 'communities' ? ' hidden' : '');?>"<?=($criteria['snippet_mode'] == 'communities' ? ' disabled="disabled"' : '');?> value="<?=$option['value']; ?>"<?=($criteria['search']['sort_by'] == $option['value']) ? ' selected' : ''; ?>><?=$option['title']; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="field">
                        <label class="field__label">Snippet Mode</label>
                        <div>
                            <input type="radio" name="snippet_mode" id="snippet_mode_communities" value="communities"<?=($criteria['snippet_mode'] == 'communities') ? ' checked' : ''; ?>>
                            <label class="boolean" for="snippet_mode_communities">Communities</label>
                            <input type="radio" name="snippet_mode" id="snippet_mode_homes" value="homes"<?=($criteria['snippet_mode'] != 'communities') ? ' checked' : ''; ?>>
                            <label class="boolean" for="snippet_mode_homes">Homes</label>
                        </div>
                    </div>


                <div class="field">
                    <h2>Pages Used On</h2>
                    <?php if (!empty($snippet['pages'])) { ?>
                        <ul class="checklist">
                            <?php foreach ($snippet['pages'] as $pg) { ?>
                                <li>
                                    <div class="item_content_ico ico ico-page"></div>
                                    <a href="<?=$pg['href']; ?>"><?=$pg['text']; ?></a>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } else { ?>
                        <div class="field">
                            <p>This snippet is currently not being used on any pages.</p>
                        </div>
                    <?php } ?>
                </div>

            </div>

    </section>

</form>