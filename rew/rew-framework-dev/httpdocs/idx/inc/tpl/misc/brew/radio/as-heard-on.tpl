<div class="as-heard-on">
    <h4><i class="icon-microphone"></i> As Heard On:</h4>
    <?php foreach ($aho as $a) { ?>
        <span class="radioLogo"><img src="/thumbs/75x35/uploads/<?= Format::htmlspecialchars($a['file']); ?>"></span>
    <?php } ?>
</div>
