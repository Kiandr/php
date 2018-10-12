<div class="bar">
    <div class="bar__title"><?= __('Manage Action Plans'); ?></div>
    <div class="bar__actions">
        <a class="bar__action" href="add/"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-add"/></svg></a>
    </div>
</div>

<?php if (!empty($action_plans)) { ?>

<div class="nodes">
    <ul class="nodes__list">
        <?php foreach ($action_plans as $action_plan) { ?>
            <li class="nodes__branch">
                <div class="nodes__wrap">
                	<div class="article">
                        <div class="article__body">
                            <div class="article__thumb thumb thumb--medium -bg-rew2">
                                <svg class="icon icon--invert"><use xlink:href="/backend/img/icos.svg#icon-actionplan"/></svg>
                            </div>
                            <div class="article__content">
                                <a class="text text--strong" href="edit/?id=<?=$action_plan['id']; ?>" title="<?=Format::htmlspecialchars($action_plan['name']); ?>"><?=Format::htmlspecialchars(Format::truncate($action_plan['name'], 45)); ?></a>
                                <div class="text text--mute">
                                    <a href="edit/?id=<?=$action_plan['id']; ?>"><?=$action_plan['task_count']; ?> <?= __('Tasks'); ?></a>,
                                    <a href="/backend/leads/?action_plan_status=progress&action_plans[]=<?=$action_plan['id']; ?>"><?=$action_plan['leads_in_progress']; ?> <?= __('Progress'); ?></a>  /
                                    <a href="/backend/leads/?action_plan_status=completed&action_plans[]=<?=$action_plan['id']; ?>"><?=$action_plan['leads_completed']; ?> <?= __('Completed'); ?></a> <?= __('Leads'); ?>
                                </div>
                            </div>
                        </div>
                	</div>
                	<div class="nodes__actions">
                        <a class="btn btn--ghost btn--ico" href="?delete=<?=$action_plan['id']; ?>" onclick="return confirm('<?=__('Are you sure you want to delete this action plan?'); ?>');">
                            <svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-trash"/></svg>
                        </a>
                	</div>
                </div>
            </li>
        <?php } ?>
    </ul>
</div>

<?php } else { ?>
<div class="block">
    <p class="block"><?= __('There are currently no action plans to manage.'); ?></p>
</div>
<?php } ?>