<?php if (!$show_form) { ?>
    <div class="rewui message positive"> <strong class="title"><?=__('Thank you!'); ?></strong>
        <p><?=__('Your inquiry has been successfully submitted.'); ?></p>
    </div>
<?php } else { ?>
    <form action="?submit" method="post" class="rew_check">

        <div class="btns btns--stickyB">
            <span class="R">
                <button class="btn btn--positive" type="submit"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-check"/></svg> <?=__('Send'); ?></button>
            </span>
        </div>

        <?php if ($authuser->isSuperAdmin()) { ?>
        <div class="bar">
            <div class="bar__title"><?=__('Contact Support'); ?></div>
            <div class="bar__actions">
                <a class="bar__action" href="javascript: history.go(-1)"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-left-a"/></svg></a>
            </div>
        </div>

        <div class="-C" style="width: 200px">
            <img src="/backend/img/rew-support.png" width="200" alt=""/>
        </div>

        <?php } else { ?>
        <div class="bar">
            <div class="bar__title"><?=__('Contact Your Administrator'); ?></div>
            <div class="bar__actions">
                <a class="bar__action" href="javascript: history.go(-1)"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-left-a"/></svg></a>
            </div>
        </div>
        <?php } ?>

        <div class="block">
            <div class="field">
                <label class="field__label"><?=__('Subject'); ?> <em class="required">*</em></label>
                <input class="w1/1" name="subject" value="<?=htmlspecialchars($_POST['subject']);?>" placeholder="<?=__('Summarize your issue or question here'); ?>">
            </div>
            <div class="field">
                <label class="field__label"><?=__('Inquiry'); ?> <em class="required">*</em></label>
                <textarea class="w1/1" name="inquiry" rows="13" placeholder="<?=__('Provide as much information as possible'); ?>"><?=htmlspecialchars($_POST['inquiry']);?></textarea>
            </div>
            <div class="field">
                <label class="field__label"><?=__('Name'); ?> <em class="required">*</em></label>
                <input class="w1/1" name="name" value="<?=htmlspecialchars($_POST['name']);?>" placeholder="<?=__('Enter your name'); ?>">
            </div>
            <div class="field">
                <label class="field__label"><?=__('E-Mail'); ?> <em class="required">*</em></label>
                <input class="w1/1" name="email" type="email" value="<?=htmlspecialchars($_POST['email']);?>" placeholder="<?=__('Enter your e-mail address'); ?>">
            </div>
            <div class="field">
                <label class="field__label"><?=__('Phone'); ?> <em class="required">*</em></label>
                <input class="w1/1" name="phone" type="tel" value="<?=htmlspecialchars($_POST['phone']);?>" placeholder="<?=__('Enter your Phone #'); ?>">
            </div>
        </div>

    </form>
<?php } ?>
<div class="version text text--small block">
    <ul style="list-style: none;">
        <li><?=$container->get(REW\Core\Interfaces\SettingsInterface::class)->getVersion(); ?></li>
        <?php if (!empty($settings->MODULES['REW_IDX_DRIVE_TIME'])) { ?>
            <li>REW Drive Time <?=$drive_time::REW_DRIVE_TIME_VERSION; ?></li>
        <?php } ?>
    </ul>
</div>
