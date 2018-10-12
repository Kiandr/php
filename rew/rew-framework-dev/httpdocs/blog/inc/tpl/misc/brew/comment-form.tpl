<h3>Leave a Comment</h3>

<form action="?comment#blog-comments" method="post">

    <?php if ($backend_user->isValid()) { ?>

        <span class="field x12">
            <label class="hint">
                You are currently logged in as <strong><?=Format::htmlspecialchars($backend_user->getName()); ?></strong>.
            </label>
        </span>

    <?php } else { ?>

        <?php require Settings::getInstance()->URLS['HONEYPOT']; ?>

        <span class="field x6">
            <label>Your Name</label>
            <input name="comment_name" value="<?=Format::htmlspecialchars($_POST['comment_name']); ?>" required>
        </span>

        <span class="field x6 last">
            <label>Your Email <small>(kept private)</small></label>
            <input type="email" name="comment_email" value="<?=Format::htmlspecialchars($_POST['comment_email']); ?>" required>
        </span>

        <div class="field x12">
            <label>Website <small>(optional)</small></label>
            <input type="url" name="comment_website" value="<?=Format::htmlspecialchars($_POST['comment_website']); ?>" placeholder="http://">
        </div>

    <?php } ?>

    <div class="field x12">
        <label>Comment</label>
        <textarea cols="32" rows="5" name="comment" required><?=Format::htmlspecialchars($_POST['comment']); ?></textarea>
    </div>

    <?php if ($blog_settings['captcha'] === 't' && !$backend_user->isValid()) { ?>
        <div class="field x12">
            <label>Security Code</label>
            <input name="captcha">
            <div class="captcha"><img src="/captcha.png?<?=md5(time()); ?>" border="0"></div>
        </div>
    <?php } ?>

    <div class="btnset">
        <button type="submit" class="strong">Comment</button>
    </div>

</form>