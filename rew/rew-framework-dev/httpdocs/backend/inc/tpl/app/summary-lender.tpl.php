<div class="block">
    <div class="article">
        <div class="article__body">
            <div class="article__thumb thumb thumb--large">
                <?php if(empty($lead['image'])) { ?>
                <img src="/thumbs/312x312/<?=(!empty($lender['image']) ? 'uploads/' . $lender['image'] : 'uploads/agents/na.png'); ?>" alt="">
                <?php } ?>
            </div>

            <div class="article__content">
                <div class="text text--strong text--large"><?=Format::htmlspecialchars($lender['first_name']); ?> <?=Format::htmlspecialchars($lender['last_name']); ?></div>
                <div class="text text--mute">
        		<?php if($lender['timestamp'] != '0000-00-00 00:00:00' && $lender['timestamp'] != NULL) { ?>
        		<?=Format::dateRelative(($lender['timestamp']));?>
        		<?php } else { ?>
        		<?= __('Never Signed In'); ?>
        		<?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>