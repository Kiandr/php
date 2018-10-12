<div class="notice notice--negative">
    <div class="notice__message">
        <?php if (count($sent['errors']) > 1) { ?>
        Oops! Your Form Contains Errors.
        <ul>
            <?php foreach ($sent['errors'] as $error) { ?>
            <li><?=$error;?></li>
            <?php } ?>
        </ul>
        <?php } else { ?>
        <?=$sent['errors'][0];?>
        <?php } ?>
    </div>
</div>