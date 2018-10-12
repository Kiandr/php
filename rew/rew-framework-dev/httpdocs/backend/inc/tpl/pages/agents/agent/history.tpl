<?php
// Render agent summary header (menu/title/preview)
echo $this->view->render('inc/tpl/partials/agent/summary.tpl.php', [
    'title' => __('History'),
    'agent' => $agent,
    'agentAuth' => $agentAuth
]);
?>
<form>
    <input type="hidden" name="id" value="<?=$agent['id']; ?>">
    <div class="block">
        <div class="input w1/1">
            <select name="filters[]" data-selectize multiple>
                <?php foreach ($filters as $group => $types) { ?>
                    <?php if (empty($types)) continue; ?>
                    <?php foreach ($types as $type) { ?>
                        <?php if (empty($type)) continue; ?>
                        <option value="<?=$type['value']; ?>"<?=(is_array($_GET['filters']) && in_array($type['value'], $_GET['filters'])) ? ' selected' : ''; ?>>
                            <?=$type['title']; ?>
                        </option>
                    <?php } ?>
                <?php } ?>
            </select>
            <button type="submit" class="btn"><?= __('Apply Filter'); ?></button>
        </div>
    </div>
</form>

<?php

// Render timeline
if (!empty($history)) {
    echo $this->view->render('inc/tpl/partials/history-timeline.tpl.php', [
        'pagination' => $pagination,
        'history' => $history,
        'view' => 'agent',
        'user' => $agent['id']
    ]);

} else {

    if (!empty($title)) {
        echo '<p class="-padH">' . __('No %s.', $title) . '</p>';
    } else {
        if (!empty($_GET['filters'])) {
            echo '<p class="-padH">' . __('This agent currently has no tracked history matching the provided filter.') . '</p>';
        } else {
            echo '<p class="-padH">' . __('This agent currently has no tracked history.') . '</p>';
        }
    }

}