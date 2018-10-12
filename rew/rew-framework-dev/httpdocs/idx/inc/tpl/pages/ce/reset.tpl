<?php

// Success notices
if (!empty($success)) {
    echo sprintf('<div class="notice notice--positive">%s</div>',
        implode('<br>', $success)
    );
}

// Error notices
if (!empty($errors)) {
    echo sprintf('<div class="notice notice--negative">%s</div>',
        implode('<br>', $errors)
    );
}

?>
<form action="?reset" method="post" autocomplete="off">

    <p class="msg marB-sm">Enter a new password for your account.</p>

    <div class="field">
        <label>New Password</label>
        <input type="password" name="password" required>
    </div>

    <div class="field">
        <label>Confirm</label>
        <input type="password" name="confirm_password" required>
    </div>

    <div class="btns padT">
        <button class="btn btn--primary" type="submit">Reset Password</button>
        <a class="R" href="<?=Settings::getInstance()->SETTINGS['URL_IDX_LOGIN']; ?>">Back to Login</a>
    </div>

</form>