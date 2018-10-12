<div class="msg errors">
    <h5 class="title">Oops! Your Form Contains Errors.</h5>
    <ul>
        <?php foreach ($sent['errors'] as $error) { ?>
        <li><?=$error;?></li>
        <?php } ?>
    </ul>
</div>