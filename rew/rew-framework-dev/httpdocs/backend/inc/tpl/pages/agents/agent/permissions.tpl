<?php
// Render agent summary header (menu/title/preview)
echo $this->view->render('inc/tpl/partials/agent/summary.tpl.php', [
    'title' => __('Permissions'),
    'agent' => $agent,
    'agentAuth' => $agentAuth
]);
?>
<form id="agent-permissions" action="?submit" method="post" class="rew_check">
    <input type="hidden" name="id" value="<?=$agent['id']; ?>">

    <div class="btns btns--stickyB"><span class="R">
        <button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?></button>
        </span>
    </div>

    <div id="permissionList" class="block">

        <?php foreach ($permissions as $k => $section) { ?>
            <?php if (!empty($section['permissions'])) { ?>
                <div class="field field--permissions">
                    <h3 class="panel__hd panel__hd--permissions">
                        <?= __('%s Privileges', $section['title']); ?>
                        <svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-drop"></use></svg>
                    </h3>
                    <div class="permissions__content">
                    <?php foreach ($section['permissions'] as $j => $permission) { ?>
                        <?php if (empty($permission)) continue; ?>
                        <?php $hasSubpermissions = (is_array($permission['subpermissions']) && !empty($permission['subpermissions'])); ?>
                        <?php $permissionSet = ($permission['type'] === 'admin') ? $agent['permissions_admin'] : $agent['permissions_user'] ; ?>

                        <label class="boolean">
                            <input type="checkbox" name="permissions[<?=$permission['type']; ?>][]" value="<?=$permission['value']; ?>"<?=($permissionSet & $permission['value']) ? ' checked' : ''; ?><?=$hasSubpermissions ? ' data-superpermission=' . implode('-',[$k,$j]) : ''; ?> data-full-permissions=<?=$permission['full'] ? 1 : 0?>>
                            <?=$permission['title']; ?>
                        </label>
                        <p class="text--mute">
                            <?=$permission['description']; ?>
                        </p>
                        <?php if ($hasSubpermissions) {?>
                            <div class="subpermissions">
                                <?php foreach ($permission['subpermissions'] AS $subpermission) { ?>
                                    <?php if (empty($subpermission)) continue; ?>
                                    <?php $subpermissionSet= ($subpermission['type'] === 'admin') ? $agent['permissions_admin'] : $agent['permissions_user'] ; ?>
                                    <label class="boolean">
                                        <input type="checkbox" name="permissions[<?=$subpermission['type']; ?>][]" value="<?=$subpermission['value']; ?>"<?=($subpermissionSet & $subpermission['value']) ? ' checked' : ''; ?> data-subpermission=<?=implode('-',[$k,$j]); ?> data-full-permissions=<?=$subpermission['full'] ? 1 : 0?>>
                                        <span class="toggle__label"><?=$subpermission['title']; ?></span>
                                    </label>
                                    <p class="text--mute">
                                        <?=$subpermission['description']; ?>
                                    </p>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </div>

</form>
