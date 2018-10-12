<?php

    $slugs = explode('/', $_GET['page']);
    $slug = $slugs[2];

?>

<div class="menu menu--drop hidden" id="menu--filters">
	<ul class="menu__list">
		<li class="menu__item"><a class="menu__link<?php if($slug=='summary') echo ' is-active';?>" href="<?=URL_BACKEND; ?>lenders/lender/summary/?id=<?=$lender['id']; ?>">Lender Summary</a></li>
		<li class="menu__item"><a class="menu__link<?php if($slug=='tasks') echo ' is-active';?>" href="/backend/lenders/lender/tasks/?id=<?=$lender['id']; ?>">Tasks</a>
		<li class="menu__item"><a class="menu__link<?php if($slug=='history') echo ' is-active';?>" href="/backend/lenders/lender/history/?id=<?=$lender['id']; ?>">History</a>
		<li class="menu__item divider"></li>
		<li class="menu__item"><a class="menu__link" href="<?=URL_BACKEND; ?>email/?id=<?=$lender['id']; ?>&type=lenders">Send Email</a></li>
        <?php if ($can_edit) { ?>
            <li class="menu__item"><a class="menu__link<?php if($slug=='edit') echo ' is-active';?>" href="<?=URL_BACKEND; ?>lenders/lender/edit/?id=<?=$lender['id']; ?>">Edit Mode</a></li>
        <?php } ?>
        <?php if ($can_delete) { ?>
			<li class="menu__item"><a class="menu__link menu__link--negative" href="/backend/lenders/lender/delete/?id=<?=$lender['id']; ?>">Delete</a>
		<?php } ?>
	</ul>
</div>