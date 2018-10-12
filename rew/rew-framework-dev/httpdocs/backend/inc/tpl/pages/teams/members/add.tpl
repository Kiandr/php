<form action="?submit" method="post" class="rew_check">
    <input name="id" type="hidden" value="<?=urlencode($_GET['id']); ?>"></input>

    <div class="bar">
        <div class="bar__title"><?= __('Add New Agent to %s', Format::htmlspecialchars($team['name'])); ?></div>
        <div class="bar__actions">
            <a class="bar__action timeline__back" href="<?=URL_BACKEND;?>teams/members/?id=<?=urlencode($_GET['id']); ?>" link="<?=URL_BACKEND;?>teams/members/?id=<?=urlencode($_GET['id']); ?>"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
        </div>
    </div>

    <div class="btns btns--stickyB">
        <span class="R">
            <button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"/></svg> <?= __('Save'); ?></button>
        </span>
    </div>

    <div class="block">

        <div class="field">
            <label class="field__label"><?= __('Agent'); ?> <em class="required">*</em></label>
            <select name="agent_id" id="assign-agent" class="w1/1" required>
                <option value=""><?= __('Select an Agent'); ?></option>
                <?php foreach ($new_agents as $new_agent) { ?>
                    <option value="<?=$new_agent['id']; ?>"><?=Format::htmlspecialchars($new_agent['name']); ?></option>
                <?php } ?>
            </select>
        </div>


    	<?php foreach ($permissionSets AS $permissionSet) { ?>
            <div class="block -marB">
                <span class="divider__label divider__label--left text text--medium"><?=htmlspecialchars($permissionSet['title']); ?></span>
                <?php foreach ($permissionSet['permissions'] AS $permission) { ?>
                    <div class="field">
                        <label class="field__label"><?=$permission->getTitle(); ?></label>
                        <div>
                            <?php foreach ($permission->getValues() AS $key => $value) { ?>
                                <label class="toggle" for="<?=$permission->getColumn(); ?>_<?=$key; ?>_<?=$value['value'] ?: 0; ?>">
                                    <input
                                        type="radio"
                                        id="<?=$permission->getColumn();?>_<?=$key; ?>_<?=$value['value'] ?: 0; ?>"
                                        name="<?=$permission->getColumn(); ?>"
                                        value="<?=intval($value['value']); ?>"
                                        <?=(($_POST['granted_permissions'] & intval($value['value'])) || ($permission->use_default && $permission->getDefault() == $value['value'])) ? ' checked' : ''; ?>
                                    >
                                    <span class="toggle__label"> <?=$value['title']; ?></span>
                                </label>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

    </div>

</form>