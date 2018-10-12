<div class="block">
    <div class="article">
        <div class="article__body">
            <?php if (!empty($team['image'])) { ?>
                <div class="article__thumb thumb thumb--large">
                    <img src="/thumbs/60x60/uploads/teams/<?=urlencode($team['image']); ?>">
                </div>
            <?php } else if (!empty($owning_agent)) { ?>
                <?php if (!empty($owning_agent['image'])) { ?>
                    <div class="article__thumb thumb thumb--large">
                        <img src="/thumbs/60x60/uploads/agents/<?=urlencode($owning_agent['image']); ?>">
                    </div>
                <?php } else { ?>
                    <div class="article__thumb thumb thumb--large -bg-<?=strtolower($owning_agent['last_name'][0]); ?>">
                        <span class="thumb__label"><?=$owning_agent['first_name'][0] . $owning_agent['last_name'][0]; ?></span>
                    </div>
                <?php } ?>
            <?php } ?>
            <div class="article__content">
                <div class="text text--strong text--large"><?=Format::htmlspecialchars($team['name']); ?></div>
                <div class="text text--mute"><?=Format::htmlspecialchars($team['description']); ?></div>
            </div>
        </div>
    </div>
</div>