<?php

/**
 * Display IDX builder panels
 * @var \IDX_Builder $builder
 * @var \REW\Core\Interfaces\BackendInterface $page
 */

?>
<div id="idx-builder-panels">
    <?php $builder->display($page); ?>
</div>

<div class="field panel-new">
    <div class="fld-inputs">
        <select class="wFill" id="new-search-panel">
            <option value="" selected><?= __('Add a Panel...'); ?></option>
            <?php
            foreach ($builder->getPanels() as $panel) {
                if (!$panel->isBlocked()) { ?>
                <option value="<?=$panel->getId(); ?>"<?=$panel->getDisplay() ? ' disabled' : ''; ?>><?=Format::htmlspecialchars($panel->getTitle()); ?></option>
            <?php
                }
            }
            ?>
        </select>
        <button id="add-search-panel" type="button" class="btn"><?= __('Add'); ?></button>
    </div>
</div>
