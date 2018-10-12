<?php

/**
 * Custom field menu template
 * @var int $fieldId
 * @var bool $enabled
 * @var REW\Backend\Auth\CustomAuth $customAuth
 */

// Current route name
$slugs = explode('/', $_GET['page']);
$slug = $slugs[3];

// Check Custom Field Authorization
$canDelete = $customAuth->canDeleteFields();

?>
<div class="menu menu--drop hidden" id="menu--filters">
    <ul class="menu__list">
        <li class="menu__item"><a class="menu__link<?=$slug === 'edit' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>settings/fields/field/edit/?id=<?=$fieldId; ?>"><?=__('Edit Mode'); ?></a></li>
        <?php if (!$enabled) {?>
        	<li class="menu__item"><a class="menu__link<?=$slug === 'enable' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>settings/fields/field/enable/?id=<?=$fieldId; ?>"><?=__('Enable'); ?></a></li>
        <?php } else { ?>
        	<li class="menu__item"><a class="menu__link<?=$slug === 'disable' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>settings/fields/field/disable/?id=<?=$fieldId; ?>"><?=__('Disable'); ?></a></li>
        <?php }?>
        <?php if ($canDelete) { ?>
            <li class="menu__item"><a class="menu__link menu__link--negative<?=$slug === 'delete' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>settings/fields/field/delete/?id=<?=$fieldId; ?>"><?= __('Delete'); ?></a></li>
        <?php } ?>
    </ul>
</div>