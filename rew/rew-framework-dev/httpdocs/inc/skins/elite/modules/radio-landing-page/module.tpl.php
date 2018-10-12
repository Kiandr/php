<?php if (empty($pods) || !is_array($pods)) return; ?>

<div id="sellers-landing">
    <?php foreach($pods as $pod) { ?>
        <?= $pod['markup']; ?>
    <?php } ?>
</div>
