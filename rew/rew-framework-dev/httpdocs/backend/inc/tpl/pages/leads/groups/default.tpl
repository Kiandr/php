<div class="bar">
    <div class="bar__title"><?= __('Lead Groups'); ?></div>
    <div class="bar__actions">
        <a href="/backend/leads/groups/add/" class="bar__action ">
			<svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-add"></use></svg>
		</a>
    </div>
</div>

<?php if (empty($groups)) { ?>
<div class="block">
    <p class="block"><?= __('There are currently no groups to manage.'); ?></p>
</div>
<?php } else { ?>
<div class="nodes">
    <ul class="nodes__list">
    <?php foreach ($groups as $group) { ?>
        <li class="nodes__branch">

            <div class="nodes__wrap">
            	<div class="article">
                    <div class="article__body">
                        <div class="article__thumb thumb thumb--medium -bg-<?=$group['style']; ?>"><svg class="icon icon--invert"><use xlink:href="/backend/img/icos.svg#icon-search"/></svg></div>
                        <div class="article__content">
                            <?=(!empty($group['can_edit'])) ? '<a class="text text--strong" href="edit/?id=' . $group['id'] . '">' . Format::htmlspecialchars($group['name']) . '</a>' : '<span class="text text--strong">' . Format::htmlspecialchars($group['name']) . '</span>';?>
                            <div class="text text--mute">
                                <?php
                                // Group Owner
                                echo (is_null($group['agent_id']) && is_null($group['associate']) ? ($group['user'] == 'false' ? __('(Global)') : __('(Shared)')) : Format::htmlspecialchars($group['owner'])) . ' &bull; ';
                                ?>
                                <a href="<?=URL_BACKEND; ?>leads/?submit=true&groups[]=<?=$group['id']; ?>"><?=$group['leads']; ?> <?= __('Leads'); ?></a>
                            </div>
                        </div>
                    </div>
            	</div>
                <?php if (!empty($group['can_edit'])) { ?>
                <div class="nodes__actions">
                    <a class="btn btn--ghost btn--ico" href="?delete=<?=$group['id']; ?>" onclick="return confirm('<?= __('Are you sure you want to delete this group?'); ?>');">
                        <svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-trash"></use></svg>
                    </a>
                </div>
                <?php } ?>
            </div>
        </li>
    <?php } ?>
</ul>
</div>
<?php } ?>