<div class="bar">
    <span class="bar__title" data-drop="#menu--filters" href="javascript:void(0);">Feature Agent on <?=Format::htmlspecialchars($listing_title); ?></span>
    <div class="bar__actions">
        <a class="bar__action timeline__back" href="<?=URL_BACKEND;?>teams/listings/listing/?id=<?=urlencode($_GET['id']); ?>&feed=<?=urlencode($_GET['feed']); ?>&team=<?=urlencode($_GET['team']); ?>&back"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"/></svg></a>
    </div>
</div>

<?php if(!empty($owning_agent)) { ?>
    <div class="block">
        <div class="article">
            <div class="article__body">
                <?php if (!empty($owning_agent['image'])) { ?>
                    <div class="article__thumb thumb thumb--large">
                        <img src="/thumbs/60x60/uploads/agents/<?=urlencode($owning_agent['image']); ?>" alt="">
                    </div>
                <?php } else { ?>
                    <div class="article__thumb thumb thumb--large -bg-<?=strtolower($owning_agent['last_name'][0]); ?>">
                        <span class="thumb__label"><?=$owning_agent['first_name'][0] . $owning_agent['last_name'][0]; ?></span>
                    </div>
                <?php } ?>
                <div class="article__content">
                    <div class="text text--strong text--large"><?=Format::htmlspecialchars($owning_agent['first_name'] . ' ' . $owning_agent['last_name']); ?></div>
                    <div class="text text--mute"><span><?= __('Owning Agent'); ?></span></div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<div class="block">
    <form action="?submit" method="post" class="rew_check">
        <input name="id" type="hidden" value="<?=urlencode($_GET['id']); ?>"></input>
        <input name="feed" type="hidden" value="<?=urlencode($_GET['feed']); ?>"></input>
        <input name="team" type="hidden" value="<?=$team->getId(); ?>"></input>
        <div class="btns btns--stickyB">
            <span class="R">
                <button class="btn btn--positive" type="submit">
                    <svg class="icon icon-check mar0">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use>
                    </svg> <?= __('Save'); ?>
                </button>
            </span>
        </div>
        <div class="field">
            <label class="field__label"><?= __('Share this listing with:'); ?></label>
            <select class="w1/1" name="agent_id" required>
                <option value=""><?= __('Select an Agent'); ?></option>
                <?php if (!empty($new_agents)) { ?>
                    <?php foreach ($new_agents as $new_agent) { ?>
                        <option value="<?=$new_agent['id']; ?>"><?=Format::htmlspecialchars($new_agent['name']); ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
        </div>
    </form>
</div>