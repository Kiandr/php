<?php include('inc/tpl/app/menu-lenders.tpl.php'); ?>
<div class="bar">
    <a class="bar__title" data-drop="#menu--filters" href="javascript:void(0);">
        <?= __('Delete Lender'); ?>
        <svg class="icon icon-drop">
            <use xlink:href="/backend/img/icos.svg#icon-drop" xmlns:xlink="http://www.w3.org/1999/xlink"/>
        </svg>
    </a>
    <div class="bar__actions">
        <a class="bar__action" href="<?=URL_BACKEND; ?>lenders/">
            <svg class="icon">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use>
            </svg>
        </a>
    </div>
</div>
<?php include('inc/tpl/app/summary-lender.tpl.php'); ?>

<div class="block">
    <form method="post">
        <input type="hidden" name="delete" value="1">
        <p><?= __('Are you sure you want to delete this lender?'); ?></p>
        <div class="btns">
            <button type="submit" class="btn btn--negative"><?= __('Yes, Delete'); ?></button>
            <a href="../../" class="btn"><?= __('Cancel'); ?></a>
        </div>
        <?php

            // Require Leads
            if (!empty($leads)) {

                // Output
                echo '<h3>' . __('What you you like to do with %s\'s %s Leads?', Format::htmlspecialchars($lender->getName()),
                        '<a href="' . URL_BACKEND . 'leads/?submit=true&lenders[]=' . $lender->getId() . '">' . Format::number($leads) . '</a>') . '</h3>';

                // Re-Assign Leads
                if (!empty($lenders)) {
                    echo '<div class="field">';
                    echo '<label class="field__label">' . __('Re-Assign %s\'s Leads To Another Lender:', Format::htmlspecialchars($lender->getName())) . '</label>';
                    echo '<select class="w1/1" name="lender">';
                    echo '<option value="">--' .  __('No Lender')  . '--</option>';
                    foreach($lenders as $l) {
                        echo sprintf('<option value="%s">%s</option>', $l['id'], $l['name']);
                    }
                    echo '</select>';
                    echo '</fieldset>';
                }

            // Confirm
            } else {
                echo '';
            }

        ?>
    </form>
</div>