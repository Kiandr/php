<h1>Reset Password</h1>

<div class="uk-alert uk-width-1-1 uk-width-medium-1-2">
    <p>
        Reset Your Password.
    </p>
</div>

<?php include $page->locateTemplate('idx', 'misc', 'messages'); ?>

<form class="uk-form" action="?reset" method="post" autocomplete="off">

    <div class="uk-width-1-1 uk-width-medium-1-2">
        <div class="uk-width-1-1 uk-margin-bottom">
            <input class="uk-width-1-1 uk-form-large" placeholder="New Password" type="password" name="password" required>
        </div>
        <div class="uk-width-1-1 uk-margin-bottom">
            <input class="uk-width-1-1 uk-form-large" placeholder="Confirm" type="password" name="confirm_password" required>
        </div>

        <div class="uk-form-row">
            <button type="submit" class="uk-button uk-button-medium">Submit</button>
            <a class="uk-button uk-button-medium" href="<?= Format::htmlspecialchars(Settings::getInstance()->SETTINGS['URL_IDX_LOGIN']); ?>">Back to Login Form</a>
        </div>
    </div>
</form>
