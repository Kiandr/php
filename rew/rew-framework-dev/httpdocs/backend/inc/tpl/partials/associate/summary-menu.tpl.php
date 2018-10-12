<?php

/**
 * Associate menu template
 * @var int $associateId
 * @var REW\Backend\Auth\Associates\AssociateAuth $associateAuth
 */

// Current route name
if ($_GET['page'] === 'email') {
    $slug = 'email';
} else {
    $slugs = explode('/', $_GET['page']);
    $slug = $slugs[2];
}

// Check Associate Authorization
$canEdit = $associateAuth->canEditAssociate();

?>
<div class="menu menu--drop hidden" id="menu--filters">
	<ul class="menu__list">
		<li class="menu__item"><a class="menu__link<?=($slug === 'summary') ?' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>associates/associate/summary/?id=<?=$associateId; ?>"><?= __('Summary'); ?></a></li>
		<li class="menu__item"><a class="menu__link<?=($slug === 'email') ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>email/?id=<?=$associateId; ?>&type=associates"><?= __('Send Email'); ?></a></li>
		<li class="menu__item"><a class="menu__link<?=($slug === 'history') ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>associates/associate/history/?id=<?=$associateId; ?>"><?= __('History'); ?></a></li>
		<li class="menu__item"><a class="menu__link<?=($slug === 'tasks') ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>associates/associate/tasks/?id=<?=$associateId; ?>"><?= __('Tasks'); ?></a></li>
		<?php if ($canEdit) { ?>
			<li class="menu__item divider"></li>
			<li class="menu__item"><a class="menu__link<?=($slug === 'edit') ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>associates/associate/edit/?id=<?=$associateId; ?>"><?=($associateAuth->isSelf()) ? __('Preferences') : __('Edit Mode'); ?></a></li>
		<?php } ?>
	</ul>
</div>