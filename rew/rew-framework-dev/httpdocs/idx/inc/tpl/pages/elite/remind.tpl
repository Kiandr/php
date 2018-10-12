<div class="modal-header">
    <h2>Reset Password</h2>
</div>

<div class="modal-body">
    <?php include $page->locateTemplate('idx', 'misc', 'messages'); ?>
    <form class="uk-form" action="<?= Format::htmlspecialchars(Settings::getInstance()->SETTINGS['URL_IDX_REMIND']); ?>?remind" method="post">
        <div class="uk-width-1-1 uk-margin-bottom">
            <input class="uk-width-1-1 uk-form-large" placeholder="Email Address" type="email" name="email" value="<?= Format::htmlspecialchars($email); ?>" required>
        </div>
        <div class="uk-form-row">
            <a class="uk-button uk-button-medium" data-modal="login">Back to Login Form</a>
            <button type="submit" class="uk-button uk-button-medium">Send Reset Request</button>
        </div>
    </form>
</div>
