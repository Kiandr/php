<?php

    $slugs = explode('/', $_GET['page']);
    $slug = $slugs[1];

?>
<div class="menu menu--drop hidden" id="menu--filters">
    <ul class="menu__list">
        <li class="menu__item"><a class="menu__link<?=$slug === 'summary' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>teams/summary/?id=<?=$team['id']; ?>"><?= __('Team Summary'); ?></a></li>
        <li class="menu__item"><a class="menu__link<?=$slug === 'members' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>teams/members/?id=<?=$team['id']; ?>"><?= __('Members'); ?></a></li>
        <li class="menu__item"><a class="menu__link<?=$slug === 'listings' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>teams/listings/?id=<?=$team['id']; ?>"><?= __('Listings'); ?></a></li>
        <?php if (!empty($can_edit) || !empty($can_delete)) { ?>
            <li class="menu__item divider"></li>
            <?php if (!empty($can_edit)) { ?>
                <li class="menu__item"><a class="menu__link<?=$slug === 'edit' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>teams/edit/?id=<?=$team['id']; ?>"><?= __('Edit Mode'); ?></a></li>
            <?php } ?>
            <?php if (!empty($can_delete)) { ?>
                <li class="menu__item"><a class="menu__link menu__link--negative" onclick="return confirm('Are you sure you want to delete this team?');" href="<?=URL_BACKEND; ?>teams/?delete=<?=$team['id']; ?>"><?= __('Delete'); ?></a></li>
            <?php } ?>
        <?php } ?>
    </ul>
</div>