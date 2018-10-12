<?php 

    $slugs = explode('/', $_GET['page']);
    $slug = $slugs[2];

?>

<div class="menu menu--drop hidden" id="menu--filters">
	<ul class="menu__list">
		<li class="menu__item"><a class="menu__link<?php if($slug=='summary') echo ' is-active';?>" href="<?=URL_BACKEND; ?>associates/associate/summary/?id=<?=$associate['id']; ?>">Associate Summary</a></li>
		<li class="menu__item"><a class="menu__link<?php if($slug=='history') echo ' is-active';?>" href="<?=URL_BACKEND; ?>associates/associate/history/?id=<?=$associate['id']; ?>">History</a></li>
		<li class="menu__item divider"></li>
		<li class="menu__item"><a class="menu__link" href="<?=URL_BACKEND; ?>email/?id=<?=$associate['id']; ?>&type=associates">Send Email</a></li>
		<?php if ($can_edit) { ?>
		    <li class="menu__item"><a class="menu__link<?php if($slug=='edit') echo ' is-active';?>" href="<?=URL_BACKEND; ?>associates/associate/edit/?id=<?=$associate['id']; ?>"><?=($associate['id'] == $authuser->info('id')) ? 'Preferences' : 'Edit Mode'; ?></a></li>
		<?php } ?>
        <?php if ($can_delete) { ?>
            <li class="menu__item"><a class="menu__link menu__link--negative" href="<?=URL_BACKEND; ?>associates/associate/delete/?id=<?=$associate['id']; ?>"><?= __('Delete'); ?></a></li>
        <?php } ?>
	</ul>
</div>
