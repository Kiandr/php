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
<div class="login-page container -sm">
<form action="?remind" method="post">

	<h1>Reset Password</h1>
    <p>Enter your email address below to receive instructions to reset your password.</p>

    <div class="field">
        <label>Email Address</label>
        <input type="email" name="email" value="<?=htmlspecialchars($email); ?>" required>
    </div>

    <div class="buttons buttons--login -pad-vertical-md">
        <button class="button button--pill button--strong -mar-bottom-sm@xs" type="submit">Send Reset Request</button>
        <a class="-text-center" href="<?=Settings::getInstance()->SETTINGS['URL_IDX_LOGIN']; ?>">Back to Login</a>
    </div>

</form>
</div>