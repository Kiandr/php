<div class="block">
    <div class="article">
        <div class="article__body">
            <div class="article__thumb thumb thumb--large">
                <?php if(empty($lead['image'])) { ?>
                <img src="/thumbs/312x312/<?=(!empty($agent['image']) ? 'uploads/agents/' . $associate['image'] : 'uploads/agents/na.png'); ?>" alt="">
                <?php } ?>
            </div>

            <div class="article__content">
                <div class="text text--strong text--large"><?php if($associate['name']) { echo Format::htmlspecialchars($associate['name']); } else { echo Format::htmlspecialchars($associate['first_name'] . ' ' . $associate['last_name']); } ?></div>
                <div class="text text--mute">
                <?php if($agent['timestamp'] != '0000-00-00 00:00:00' && $agent['timestamp'] != NULL) { ?>
                <?=Format::dateRelative(($agent['timestamp']));?>
                <?php } else { ?>
                Never Signed In
                <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>