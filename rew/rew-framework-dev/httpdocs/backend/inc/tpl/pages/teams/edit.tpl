<?php include('inc/tpl/app/menu-teams.tpl.php'); ?>
<div class="bar">
    <a class="bar__title" data-drop="#menu--filters" href="javascript:void(0);">
        <?= __('Edit Team'); ?>
        <svg class="icon icon-drop">
            <use xlink:href="/backend/img/icos.svg#icon-drop" xmlns:xlink="http://www.w3.org/1999/xlink"/>
        </svg>
    </a>
    <div class="bar__actions">
        <a class="bar__action timeline__back" href="<?=URL_BACKEND;?>teams?back">
            <svg class="icon">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"/>
            </svg>
        </a>
    </div>
</div>

<form action="?submit" enctype="multipart/form-data" method="post" class="rew_check">
    <input name="id" type="hidden" value="<?=urlencode($_GET['id']); ?>">
    <div class="block">
        <div class="cols">
            <div class="field col w3/4">
                <label class="field__label"><?= __('Team Name'); ?> <em class="required">*</em></label>
                <input class="w1/1" type="text" name="name" value="<?=Format::htmlspecialchars($team->info('name')); ?>" required>
            </div>

            <div class="field col w1/4">
                <label class="field__label"><?= __('Label Color'); ?></label>
                <select class="w1/1" name="style">
                    <option value=""></option>
                    <?php foreach ($teamLabels as $label) { ?>
                        <option value="<?=$label; ?>"<?=($team->info('style') == $label) ? ' selected': ''; ?>><?=$label; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <h3><?= __('Team Photo'); ?></h3>
        <div class="field">
            <input class="w1/1" name="team_photo" type="file" value="<?=htmlspecialchars($teams['image']); ?>">
        </div>

        <?php if (!empty($team['image'])) { ?>
            <div class="field">
                <input type="hidden" name="images" value="<?=$team['image']; ?>">
                <img src="/thumbs/200x200/uploads/teams/<?=urlencode($team['image']); ?>" alt=""><br />
                <a class="btn btn--ghost delete" href="?id=<?=$team['id']; ?>&deletePhoto" onclick="return confirm('<?= __('Are you sure you want to remove this photo?'); ?>')">
                    <svg class="icon icon-trash mar0">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-trash"></use>
                    </svg>
                </a>
            </div>
        <?php } ?>

        <div class="field">
            <label class="field__label"><?= __('Description'); ?></label>
            <textarea class="w1/1" rows="4" name="description"><?=Format::htmlspecialchars($team->info('description')); ?></textarea>
        </div>

        <?php if (!empty($can_assign)) { ?>
            <div class="field">
                <label class="field__label"><?= __('Primary Agent'); ?> <em class="required">*</em></label>
                <select class="w1/1" name="agent_id" id="assign-agent">
                    <?php foreach ($agents as $agent) { ?>
                        <option value="<?=$agent['id']; ?>"<?=($team->info('agent_id') == $agent['id']) ? ' selected' : ''; ?>><?=Format::htmlspecialchars($agent['name']); ?></option>
                    <?php } ?>
                </select>
            </div>
        <?php } ?>

        <h3 class="panel__hd"><?= __('Primary Agent Settings'); ?></h3>
        <?php foreach ($permissionSets AS $permissionSet) { ?>
            <div class="field">
                <span class="field__label"><?=htmlspecialchars($permissionSet['title']); ?></span>
                <?php foreach ($permissionSet['permissions'] AS $permission) { ?>
                    <div class="field">
                        <label class="field__label"><?=$permission->getTitle(); ?></label>
                        <div>
                            <?php foreach ($permission->getValues() AS $key => $value) { ?>
                                <label class="toggle" for="<?=$permission->getColumn(); ?>_<?=$key; ?>_<?=$value['value'] ?: 0; ?>">
                                    <input
                                        type="radio"
                                        id="<?=$permission->getColumn(); ?>_<?=$key; ?>_<?=$value['value'] ?: 0; ?>"
                                        name="<?=$permission->getColumn(); ?>"
                                        value="<?=intval($value['value']); ?>"
                                        <?=(($team_permissions[$permission->getKey()] & intval($value['value'])) || ($permission->use_default && $permission->getDefault() == $value['value'])) ? ' checked' : ''; ?>
                                    >
                                    <span class="toggle__label"> <?=$value['title']; ?></span>
                                </label>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if ($requiredToCancelSubdomains || $teamSubdomainAuth->canCreateSubdomains($authuser)) { ?>
        	<h3 class="panel__hd"><?= __('Team Subdomain Settings'); ?></h3>
            <div class="field">
                <label class="field__label"><?= __('Team Website Enabled'); ?></label>
                <div>
                    <label class="toggle" for="settings_subdomain_true">
                    	<input id="settings_subdomain_true" type="radio" name="subdomain" value="true"<?=($team->info('subdomain') == 'true') ? ' checked' : ''; ?>>
                    	<span class="toggle__label"><?= __('Yes'); ?></span>
                    </label>
                    <label class="toggle" for="settings_subdomain_false">
                    	<input id="settings_subdomain_false" type="radio" name="subdomain" value="false"<?=($team->info('subdomain') != 'true') ? ' checked' : ''; ?>>
                    	<span class="toggle__label"><?= __('No'); ?></span>
                    </label>
                </div>
            </div>
            <div id="team-subdomain"<?=($team->info('subdomain') == 'true') ? '' : ' class="hidden"'; ?>>
                <?php if ($team['subdomain'] !== 'true') { ?>
                <div class="field">
                    <label class="field__label"><?= __('Please provide the MLS board(s) which this team has access to: *'); ?></label>
                    <input type="hidden" name="mls_boards" value="">
                        <?php foreach ($idx_feeds as $name => $title) { ?>
                            <?php $checked = isset($_POST['requested_feed'][$name]); ?>

                                <label class="toggle -marB8" for="requested_feeds_<?=$name; ?>">
                                	<input type="checkbox" value="<?=$name; ?>" name="requested_feeds[<?=$name; ?>]" id="requested_feeds_<?=$name; ?>"<?=$checked ? ' checked' : ''; ?>>
									<span class="toggle__label"><?=$title; ?></span>
								</label>
								<input
									name="feeds_team[<?=$name; ?>]"
									placeholder="Agent ID Providing <?=strtoupper($name); ?>"
									value="<?=Format::htmlspecialchars($_POST['feeds_team'][$name]); ?>" <?=$checked ? ' required' : ''; ?>
									class="<?=$checked ? '' : 'hidden'; ?> w1/1 -marB8"
								>
                        <?php } ?>

                </div>
                <? } ?>
                <div class="field">
                    <label class="field__label"><?= __('Team Website IDXs'); ?></label>
                    <? if (Settings::isREW()) { ?>
                    <p><?= __('This section is editable by REW staff only.  Check with our IDX team before enabling a specific feed for a team website.'); ?></p>
                    <? } else { ?>
                    <p><?= __('This section is editable by REW staff only.  Please contact customer support to enable IDX access for this team website.'); ?></p>
                    <? } ?>

                    <?php foreach ($idx_feeds as $name => $title) { ?>
                    <label class="toggle toggle--stacked" for="settings_feeds_<?=$name; ?>">
                    	<input type="checkbox" <?=Settings::isREW() ? "" : "disabled"?> value="<?=$name; ?>" name="feeds[]" id="settings_feeds_<?=$name; ?>" <?=in_array($name, $team_idxs) ? 'checked' : ""; ?>>
                    	<span class="toggle__label"><?=$title; ?>
                    </label>
                    <?php } ?>

                </div>
                <div class="field">
                    <label class="field__label"><?=__('Team Website Add-ons'); ?></label>
                    <?php if (!empty($addons)) { ?>
                        <ul style="list-style: none; padding: 0;">
                            <?php foreach ($addons as $addon) { ?>
                                <li class="-marB8">
                                    <label>
                                        <input type="checkbox" <?=(isset($team['subdomain_addons']) && in_array($addon['db_val'], $team['subdomain_addons'])) ? ' checked' : ''; ?> value="<?=$addon['db_val']; ?>" name="subdomain_addons[]">
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
                    <input class="w1/1" name="subdomain_link" value="<?=htmlspecialchars($team->info('subdomain_link'))?>" data-link="<?=sprintf(URL_AGENT_SITE, '*'); ?>">
                </div>
                <div class="field">
                    <label class="field__label"><?= __('Team Site'); ?></label>
                    <?php if (!empty($team['subdomain_link'])) : ?>
                    <a id="account-link" href="<?=sprintf(URL_AGENT_SITE, $team['subdomain_link']); ?>" target="_blank">
                    <?=sprintf(URL_AGENT_SITE, $team['subdomain_link']); ?>
                    </a>
                    <?php else : ?>
                    <a id="account-link" href="javascript:void(0);">
                    <?=sprintf(URL_AGENT_SITE, '*'); ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php } ?>

        <div class="btns btns--stickyB">
            <span class="R">
                <button class="btn btn--positive" type="submit">
                    <svg class="icon icon-check mar0">
                        <use xlink:href="/backend/img/icos.svg#icon-check"/>
                    </svg>
                    <?= __('Save'); ?>
                </button>
            </span>
        </div>

    </div>
</form>