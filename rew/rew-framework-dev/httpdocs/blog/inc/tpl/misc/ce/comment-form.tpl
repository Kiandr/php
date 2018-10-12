<div class="divider -pad-vertical-lg"></div>
<h3 class="-pad-top"><?= __('Leave a Comment'); ?></h3>
<form action="?comment#blog-comments" method="post">
    <?php if ($backend_user->isValid()) { ?>
        <p class="hint">
            <?= __('You are currently logged in as %s.', '<strong>' . Format::htmlspecialchars($backend_user->getName()) . '</strong>'); ?>
        </p>
    <?php } else { ?>

        <?php require Settings::getInstance()->URLS['HONEYPOT']; ?>

        <div class="columns">
            <div class="column -width-1/2 -width-1/1@sm -width-1/1@xs field">
                <label class="field__label"><?= __('Your Name'); ?></label>
                <input name="comment_name" value="<?=Format::htmlspecialchars($_POST['comment_name']); ?>" required>
            </div>
            <div class="column -width-1/2 -width-1/1@sm -width-1/1@xs field">
                <label class="field__label"><?= __('Your Email'); ?> <small><?= __('(kept private)'); ?></small></label>
                <input aria-describedby="comment_email_format" type="email" name="comment_email" value="<?=Format::htmlspecialchars($_POST['comment_email']); ?>" required>
                <small id="comment_email_format"><?= __('Format example: you@domain.com'); ?></small>
            </div>
            <div class="column -width-1/1 field">
                <label class="field__label"><?= __('Website'); ?> <small>(<?= __('optional'); ?>)</small></label>
                <input aria-describedby="comment_website_format" type="url" name="comment_website" value="<?=Format::htmlspecialchars($_POST['comment_website']); ?>">
                <small id="comment_website_format"><?= __('Format example: yourwebsitename.com'); ?></small>
            </div>
        </div>
    <?php } ?>
    <div class="columns">
        <div class="column -width-1/1 field">
            <label class="field__label"><?= __('Comment'); ?></label>
            <textarea cols="32" rows="5" name="comment" required><?=Format::htmlspecialchars($_POST['comment']); ?></textarea>
        </div>
        <?php if ($blog_settings['captcha'] === 't' && !$backend_user->isValid()) { ?>
            <div class="column -width-1/1 field">
                <label class="field__label"><?= __('Security Code'); ?></label>
                <input name="captcha">
                <div class="captcha"><img data-src="/captcha.png?<?=md5(time()); ?>" border="0"></div>
            </div>
        <?php } ?>
    </div>
    <div class="btnset">
        <button type="submit" class="btn"><?= __('Comment'); ?></button>
    </div>
</form>
