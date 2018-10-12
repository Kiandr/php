<div class="bar">
    <div class="bar__title"><?= __('REWText Auto-Responders'); ?></div>
    <div class="bar__actions">
        <a class="btn btn--ghost" href="/backend/leads/tools/"><svg class="icon icon-left-a mar0"><use xlink:href="/backend/img/icos.svg#icon-left-a"/></svg></a>
    </div>
</div>

<div class="block">
    <?php if (empty($autoresponders)) { ?>
    <p class="block"><?= __('No auto-responders have been setup.'); ?></p>
    <?php } else { ?>
    <div class="table__wrap marB">
        <table class="table">
        	<thead>
        		<tr>
        			<th><?= __('Agent Name'); ?></th>
        			<th><?= __('Auto-Responder Message'); ?></th>
        			<th><?= __('Is Active'); ?></th>
        			<th width="100">&nbsp;</th>
        		</tr>
        	</thead>
        	<tbody>
        		<?php foreach ($autoresponders as $autoresponder) { ?>
        		<tr>
        			<td class="item_content_title"><a href="<?=URL_BACKEND; ?>agents/agent/summary/?id=<?=$autoresponder['agent_id']; ?>">
        				<?=Format::htmlspecialchars($autoresponder['agent_name']); ?>
        				</a></td>
        			<?php if (!empty($autoresponder['id'])) { ?>
        			<td class="item_content_title"><a href="edit/?id=<?=$autoresponder['id']; ?>">
        				<?=Format::htmlspecialchars($autoresponder['body']); ?>
        				<?=!empty($autoresponder['media']) ? '[MEDIA]' : ''; ?>
        				</a></td>
        			<td><?=!empty($autoresponder['active']) ? 'Yes' : 'No'; ?></td>
        			<td class="actions compact"><a href="edit/?id=<?=$autoresponder['id']; ?>" class="btn edit"><?= __('Edit'); ?></a></td>
        			<?php } else { ?>
        			<td><a href="add/?agent_id=<?=$autoresponder['agent_id']; ?>"> <span>(<?= __('No REWText Auto-Responder Setup'); ?>)<span> </a></td>
        			<td>&ndash;</td>
        			<td class="actions compact"><a href="add/?agent_id=<?=$autoresponder['agent_id']; ?>" class="btn"><?= __('Setup'); ?></a></td>
        			<?php } ?>
        		</tr>
        		<?php } ?>
        	</tbody>
        </table>
    </div>
    <?php } ?>
    <?php if (!empty($can_setup)) { ?>
    <div class="btns"> <a href="add/" class="btn btn--positive"><?= __('Setup Auto-Responder'); ?></a> </div>
    <?php } ?>
</div>
