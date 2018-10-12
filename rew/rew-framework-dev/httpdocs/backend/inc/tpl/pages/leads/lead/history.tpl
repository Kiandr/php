<?php

// Render lead summary header (menu/title/preview)
echo $this->view->render('inc/tpl/partials/lead/summary.tpl.php', [
    'title' => 'Lead History',
    'lead' => $lead,
    'leadAuth' => $leadAuth
]);

?>

<form>
    <input type="hidden" name="id" value="<?=$lead['id']; ?>">
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
            <button type="submit" class="btn">Apply Filter</button>
        </div>
    </div>
</form>

<?php

// Render timeline
if (!empty($history)) {
    echo $this->view->render('inc/tpl/partials/history-timeline.tpl.php', [
        'pagination' => $pagination,
        'history' => $history,
        'view' => 'lead',
        'user' => NULL
    ]);

} else {

    if (!empty($title)) {
        echo '<p class="block">No ' . $title . '.</p>';
    } else {
        if (!empty($_GET['filters'])) {
            echo '<p class="-padH">This lead currently has no tracked history matching the provided filter.</p>';
        } else {
            echo '<p class="-padH">This lead currently has no tracked history.</p>';
        }
    }

}