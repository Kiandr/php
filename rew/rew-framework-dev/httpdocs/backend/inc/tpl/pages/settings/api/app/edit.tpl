<form action="?submit" method="post" class="rew_check">
    <div class="block">
        <input type="hidden" name="id" value="<?=$application['id'];?>">
        <div class="bar -padL0 -padR0">
            <h1 class="bar__title mar0 -padL0 -padR0" style="font-weight: normal;">
                <?php if (!empty($application)) { ?>
                <?=htmlspecialchars($application['name']);?>
                <?php } else { ?>
                <?= __('Edit Application'); ?>
                <?php } ?>
            </h1>
            <div class="bar__actions">
                <a href="/backend/settings/api/?back" class="bar__action">
                    <svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg>
                </a>
            </div>
        </div>
        <?php if (empty($application)) {
            echo errorMsg(
                __('The specified application could not be found.'),
                __('Database Error')
            );
        } else { ?>
        <div class="field">
            <label class="field__label" for="app_name"><?= __('Name'); ?> <em class="required">*</em></label>
            <input class="w1/1" id="app_name" type="text" name="name" value="<?=htmlspecialchars($_POST['name']);?>">
            <p class="hint"> <?= __('This is for your own reference. Example: MyThirdPartySite.com'); ?> </p>
        </div>
        <div class="field">
            <label class="field__label"><?= __('API Key'); ?></label>
            <input class="w1/1" value="<?=htmlspecialchars($application['api_key']);?>" readonly>
            <p><a onclick="javascript:return confirm('<?= __('Are you sure you want to generate a new API key for this application? Any solutions using the previous key will stop working.'); ?>');" href="?id=<?=htmlspecialchars($application['id']);?>&generate"> <?= __('ReGenerate API Key'); ?> </a></p>
        </div>
        <div class="field">
            <label class="field__label"><?= __('Active'); ?></label>
            <label class="toggle" for="enabled_Y">
                <input type="radio" id="enabled_Y" name="enabled" value="Y" <?=(($_POST['enabled'] === 'Y') ? ' checked="checked"' : ''); ?>>
                <span class="toggle__label"><?= __('Yes'); ?></span>
            </label>
            <label class="toggle" for="enabled_N">
                <input type="radio" id="enabled_N" name="enabled" value="N" <?=(($_POST['enabled'] !== 'Y') ? ' checked="checked"' : ''); ?>>
                <span class="toggle__label"><?= __('No'); ?></span>
            </label>
        </div>
        <?php } ?>

        <div class="btns btns--stickyB">
            <span class="R">
            <button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?></button>
            </span>
        </div>
    </div>
</form>
