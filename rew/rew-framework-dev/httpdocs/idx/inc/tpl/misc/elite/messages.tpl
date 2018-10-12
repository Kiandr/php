<?php if (!empty($success)) { ?>
    <?php foreach ($success as $msg) { ?>
        <div class="uk-alert uk-alert-success" data-uk-alert>
            <p><?= $msg; ?></p>
        </div>
    <?php } ?>
<?php } ?>

<?php if (!empty($errors)) { ?>
    <div class="uk-alert uk-alert-danger" data-uk-alert>
        <a href="" class="uk-alert-close uk-close"></a>
        <p class="uk-text-bold uk-text-large">Houston, we have a problem...</p>
        <?php foreach ($errors as $msg) { ?>
            <p><?= $msg; ?></p>
        <?php } ?>
    </div>
<?php } ?>
