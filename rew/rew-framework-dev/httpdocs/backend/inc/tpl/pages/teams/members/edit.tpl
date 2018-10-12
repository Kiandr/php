<form action="?submit" method="post" class="rew_check">
    <input name="id" type="hidden" value="<?=urlencode($_GET['id']); ?>"></input>
    <input name="agent" type="hidden" value="<?=urlencode($_GET['agent']); ?>"></input>

    <div class="bar">
        <div class="bar__title"><?= __("Edit %s's Permissions in %s", Format::htmlspecialchars($agent->getName()), Format::htmlspecialchars($team['name'])); ?></div>
		<div class="bar__actions">
		    <a class="bar__action timeline__back" href="/backend/teams/summary/?id=<?=urlencode($_GET['id']); ?>" link="<?=URL_BACKEND;?>teams/members/?id=<?=urlencode($_GET['id']); ?>"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
	    </div>
    </div>

    <div class="btns btns--stickyB">
    	<span class="R">
        	<button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"/></svg> <?= __('Save'); ?></button>
        </span>
    </div>

	<?php foreach ($permissionSets AS $permissionSet) { ?>
        <div class="block -marB">
            <div class="divider text">
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
</form>