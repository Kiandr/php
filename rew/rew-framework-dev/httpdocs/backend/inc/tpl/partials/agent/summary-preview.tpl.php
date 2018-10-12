<?php

/**
 * Agent preview template
 * @var Backend_Agent $agent
 */

?>

<div class="block">
    <div class="article">
        <div class="article__body">
            <?php if (empty($agent['image'])) { ?>
                <div class="article__thumb thumb thumb--large -bg-<?=strtolower($agent['last_name'][0]); ?>">
                    <span class="thumb__label"><?=$agent['first_name'][0] . $agent['last_name'][0]; ?></span>
                </div>
            <?php } else { ?>
                <div class="article__thumb thumb thumb--large">
                    <img src="/thumbs/60x60/uploads/agents/<?=urlencode($agent['image']) ?: 'na.png'; ?>">
                </div>
            <?php } ?>
            <div class="article__content">
                <div class="text text--strong text--large"><?=Format::htmlspecialchars($agent['first_name']); ?> <?=Format::htmlspecialchars($agent['last_name']); ?></div>
                <div class="text text--mute">
                    <?php if($agent['last_logon'] != '0000-00-00 00:00:00' && $agent['last_logon'] != NULL) { ?>
                        <?=Format::dateRelative($agent['last_logon']);?>
                    <?php } else {
                        echo __('Never Signed In');
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>