<form action="?submit" enctype="multipart/form-data" method="post" class="rew_check">

    <div class="bar">
        <div class="bar__title"><?= __('Add New Team'); ?></div>
        <div class="bar__actions">
            <a class="bar__action timeline__back" href="javascript:void(0)" link="<?=URL_BACKEND;?>teams/">
                <svg class="icon">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use>
                </svg>
            </a>
        </div>
    </div>

    <div class="block">
        <div class="cols">
            <div class="field col w3/4">
                <label class="field__label"><?= __('Team Name'); ?> <em class="required">*</em></label>
                <input class="w1/1" id="group-name" type="text" name="name" value="<?=Format::htmlspecialchars($_POST['name']); ?>" required>
            </div>

            <div class="field col w1/4">
                <label class="field__label"><?= __('Label Color'); ?></label>
                <select class="w1/1" name="style">
                    <option value=""></option>
                    <?php foreach ($teamLabels as $label) { ?>
                        <option value="<?=$label; ?>"<?=($_POST['style'] == $label) ? ' selected': ''; ?>><?=$label; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="field">
            <label class="field__label"><?= __('Team Photo'); ?></label>
            <input class="w1/1" name="team_photo" type="file">
        </div>

        <div class="field">
            <label class="field__label"><?= __('Description'); ?></label>
            <textarea class="w1/1" rows="4" name="description"><?=Format::htmlspecialchars($_POST['description']); ?></textarea>
        </div>

        <?php if (!empty($can_assign)) { ?>
            <div class="field">
                <label class="field__label"><?= __('Primary Agent'); ?> <em class="required">*</em></label>
                <select class="w1/1" name="agent_id" id="assign-agent">
                    <option value="<?=$authuser->info('id'); ?>"><?= __('Select an Agent'); ?></option>
                    <?php foreach ($agents as $agent) { ?>
                        <option value="<?=$agent['id']; ?>"<?=($_POST['agent_id'] == $agent['id']) ? ' selected' : ''; ?>><?=Format::htmlspecialchars($agent['name']); ?></option>
                    <?php } ?>
                </select>
            </div>
        <?php } ?>

        <h3 class="panel__hd"><?= __('Primary Agent Settings'); ?></h3>
        <?php foreach ($permissionSets AS $permissionSet) { ?>
            <div class="field">
                <div class="divider">
                    <span class="divider__label divider__label--left"><?=htmlspecialchars($permissionSet['title']); ?></span>
                </div>
                <?php foreach ($permissionSet['permissions'] AS $permission) { ?>
                    <div class="field">
                        <label class="field__label"><?=$permission->getTitle(); ?></label>
                        <div>
                            <?php foreach ($permission->getValues() AS $key => $value) { ?>
                                <label class="toggle" for="<?=$permission->getColumn();?>_<?=$key; ?>_<?=$value['value'] ?: 0; ?>">
                                    <input
                                        type="radio"
                                        id="<?=$permission->getColumn();?>_<?=$key; ?>_<?=$value['value'] ?: 0; ?>"
                                        name="<?=$permission->getColumn();?>"
                                        value="<?=intval($value['value']); ?>"
                                        <?=(($_POST['permissions'][$permission->getKey()] & intval($value['value'])) || ($permission->use_default && $permission->getDefault() == $value['value'])) ? ' checked' : ''; ?>
                                    >
                                    <span class="toggle__label"> <?=$value['title']; ?></span>
                                </label>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if (!empty(Settings::getInstance()->MODULES['REW_TEAM_CMS'])) { ?>

			<h3 class="panel__hd"><?= __('Team Website Settings'); ?></h3>
            <div class="field">
                <label class="field__label"><?= __('Team Website Enabled'); ?></label>
                <div class="toggle">
                    <input id="settings_subdomain_true" type="radio" name="subdomain" value="true"<?=($_POST['subdomain'] == 'true') ? ' checked' : ''; ?>>
                    <label class="toggle__label" for="settings_subdomain_true">Yes</label>
                    <input id="settings_subdomain_false" type="radio" name="subdomain" value="false"<?=($_POST['subdomain'] != 'true') ? ' checked' : ''; ?>>
                    <label class="toggle__label" for="settings_subdomain_false">No</label>
                </div>
            </div>

            <div id="team-subdomain"<?=($_POST['subdomain'] == 'true') ? '' : ' class="hidden"'; ?>>
                <div class="field">
                    <input type="hidden" name="mls_boards" value="">
                    <label class="field__label"><?= __('Please provide the MLS board(s) which this team contains members of: *'); ?></label>
                    <div>
                        <?php foreach ($idx_feeds as $name => $title) { ?>
                            <?php $checked = isset($_POST['requested_feeds'][$name]); ?>


                                <label class="toggle -marB8" for="requested_feeds_<?=$name; ?>">
                                	<input type="checkbox" value="<?=$name; ?>" name="requested_feeds[<?=$name; ?>]" id="requested_feeds_<?=$name; ?>"<?=$checked ? ' checked' : ''; ?>> 									<span class="toggle__label"><?=$title; ?></span>
                                </label>
                                <input name="feeds_team[<?=$name; ?>]" placeholder="Agent ID Providing <?=strtoupper($name); ?>" value="<?=$_POST['feeds_team'][$name]; ?>"<?=$checked ? ' required' : ' class="hidden w1/1 -marB8"'; ?>>

                        <?php } ?>
                    </div>
                </div>
                <div class="field">
                    <label class="field__label"><?= __('Team Website IDXs'); ?></label>
                    <?php if (Settings::isREW()) { ?>
                        <p><?= __('This section is editable by REW staff only.  Check with our IDX team before enabling a specific feed for an agent website.'); ?></p>
                    <?php } else { ?>
                        <p><?= __('This section is editable by REW staff only.  Please contact customer support to enable IDX access for this agent website.'); ?></p>
                    <?php } ?>

                    <div>
                        <?php foreach ($idx_feeds as $name => $title) { ?>
                        <label class="toggle toggle--stacked" for="settings_feeds_<?=$name; ?>">
                            <input type="checkbox" <?=Settings::isREW() ? "" : "disabled"?> value="<?=$name; ?>" name="feeds[]" id="settings_feeds_<?=$name; ?>">
                            <span class="toggle__label"><?=$title; ?></span>
                        </label>
                        <?php } ?>
                    </div>
                </div>
                <div class="field">
                    <label class="field__label"><?=__('Team Website Add-ons'); ?></label>
                    <?php if (!empty($addons)) { ?>
                        <ul style="list-style: none; padding: 0;">
                            <?php foreach ($addons as $addon) { ?>
                                <li class="-marB8">
                                    <label>
                                        <input type="checkbox" <?=(isset($_POST['subdomain_addons']) && in_array($addon['db_val'], $_POST['subdomain_addons'])) ? ' checked' : ''; ?> value="<?=$addon['db_val']; ?>" name="subdomain_addons[]">
                                        <span class="toggle__label"><?=__($addon['title']); ?></span>
                                    </label>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } else { ?>
                        <span class="text--mute">No Add-ons Available</span>
                    <?php } ?>
                </div>
                <div class="field">
                    <label class="field__label"><?= __('Team Link'); ?> <em>*</em></label>
                    <input class="w1/1" type="text" name="subdomain_link" value="<?=htmlspecialchars($_POST['subdomain_link']); ?>" data-link="<?=sprintf(URL_AGENT_SITE, '*'); ?>">
                </div>
                <div class="field">
                    <label class="field__label"><?= __('Team Site'); ?></label>
                    <a href="javascript:void(0);" id="account-link">
                        <?=sprintf(URL_AGENT_SITE, $_POST['subdomain_link'] ? $_POST['cms_link'] : '*'); ?>
                    </a>
                </div>
            </div>
        <?php } ?>

        <div class="btns btns--stickyB">
            <span class="R">
                <button class="btn btn--positive" type="submit">
                    <svg class="icon icon-check mar0">
                        <use xlink:href="/backend/img/icos.svg#icon-check"></use>
                    </svg>
                    <?= __('Save'); ?>
                </button>
            </span>
        </div>

    </div>
</form>