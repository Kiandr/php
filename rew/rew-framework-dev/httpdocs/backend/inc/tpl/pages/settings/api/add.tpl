<form action="?submit" method="post" class="rew_check">
    <div class="block">
        <div class="bar -padL0 -padR0">
            <h1 class="bar__title mar0 -padL0 -padR0" style="font-weight: normal;"><?= __('Add New Application'); ?></h1>
            <div class="bar__actions">
                <a href="/backend/settings/api/?back" class="bar__action">
                    <svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg>
                </a>
            </div>
        </div>
        <div class="field">
            <label class="field__label" for="app_name"><?= __('Name'); ?> <em class="required">*</em></label>
            <input class="w1/1" id="app_name" type="text" name="name" value="<?=htmlspecialchars($_POST['name']);?>">
            <p class="hint mute"><?= __('This is for your own reference. Example: MyThirdPartySite.com'); ?></p>
        </div>
        <div class="field">
            <label class="field__label"><?= __('Active'); ?></label>
            <label class="toggle" for="enabled_Y">
                <input type="radio" id="enabled_Y" name="enabled" value="Y" <?=(($_POST['enabled'] === 'Y') ? ' checked="checked"' : ''); ?>>
                <span class="toggle__label"><?= __('Yes'); ?></span>
            </label>
            <label class="toggle" for="enabled_N">
                <input type="radio" id="enabled_N" name="enabled" value="N" <?=(($_POST['enabled'] === 'Y') ? ' checked="checked"' : ''); ?>>
                <span class="toggle__label"><?= __('No'); ?></span>
            </label>
        </div>
        <div class="btns btns--stickyB">
            <span class="R">
                <button class="btn btn--positive" type="submit"><?= __('Save'); ?></button>
            </span>
        </div>
    </div>
</form>
