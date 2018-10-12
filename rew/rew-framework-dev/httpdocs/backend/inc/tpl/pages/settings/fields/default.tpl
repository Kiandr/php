<?php
/**
 * @var boolean $canDelete
 * @var array $filters
 * @var string $filter
  * @var array $fields
 */
?>

<?php if (!empty($filters)) { ?>
    <div class="menu menu--drop hidden" id="menu--filters">
        <ul class="menu__list">
            <?php foreach ($filters as $link => $text) {
                printf(
                    '<li class="menu__item"><a class="menu__link%s" href="?filter=%s">%s</a></li>',
                    $link === $filter ? ' is-active' : '',
                    $link,
                    $text
                );
            } ?>
        </ul>
    </div>
<?php }?>

<div class="bar">
    <a class="bar__title" href="javascript:void(0);" data-drop="#menu--filters">
        <?=htmlspecialchars($filters[$filter]); ?>
        <?php if (!empty($filters)) { ?>
            <svg class="icon icon-drop">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-drop"></use>
            </svg>
        <?php } ?>
    </a>
    <div class="bar__actions">
        <a class="bar__action" href="<?=URL_BACKEND; ?>settings/fields/add/"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-add"></use></svg></a>
    </div>
</div>

<?php if (empty($fields) || !is_array($fields)) { ?>
    <div class="block">
        <p class="none"><?= __('There are currently no custom fields.'); ?></p>
    </div>

<?php } else { ?>

    <div class="nodes">
        <ul id="custom_field_manager" class="nodes__list">
            <?php foreach ($fields as $field) { ?>
                <li class="nodes__branch" data-field="pages-<?=$field['id']; ?>">
                    <div class="nodes__wrap">
                        <div class="article dd-handle">
                            <div class="article__body">
                                <div class="article__thumb thumb thumb--medium -bg-rew2<?=!$field['enabled'] ? '-inverse' : ''; ?>">
                                    <svg class="icon icon--invert">
                                        <use xlink:href="/backend/img/icos.svg#icon-<?=$field['enabled'] ? 'tools' : 'close'; ?>"></use>
                                    </svg>
                                </div>
                                <div class="article__content">
                                    <a class="text text--strong truncate-text" href="<?=URL_BACKEND; ?>settings/fields/field/edit/?id=<?=$field['id']; ?>"><?=$field['title']; ?></a>
                                    <div class="text text--mute"><?=!empty($field['type']) ? $field['type'] : ''; ?></div>
                                </div>
                                <div class="nodes__actions">
                                	<?php if ($field['enabled']) {?>
                                        <a class="btn btn--ico btn--ghost" title="<?= __('Disable'); ?>" href="<?=URL_BACKEND; ?>settings/fields/field/disable/?id=<?=$field['id']; ?>">
                                            <svg class="icon icon-close mar0">
                                                <use xlink:href="/backend/img/icos.svg#icon-close"></use>
                                            </svg>
                                        </a>
                                    <?php } else {?>
                                        <a class="btn btn--ico btn--ghost" title="<?= __('Enable'); ?>" href="<?=URL_BACKEND; ?>settings/fields/field/enable/?id=<?=$field['id']; ?>">
                                            <svg class="icon icon-check mar0">
                                                <use xlink:href="/backend/img/icos.svg#icon-check"></use>
                                            </svg>
                                        </a>
                                    <?php }?>
                                    <?php if ($canDelete) { ?>
                                        <a class="btn btn--ico btn--ghost" title="<?= __('Delete'); ?>" href="<?=URL_BACKEND; ?>settings/fields/field/delete/?id=<?=$field['id']; ?>">
                                            <svg class="icon icon-trash mar0">
                                                <use xlink:href="/backend/img/icos.svg#icon-trash"></use>
                                            </svg>
                                        </a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>