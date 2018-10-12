<?php

// Success notices
if (!empty($success)) {
    echo sprintf('<div class="msg msg--pos marB-md">%s</div>',
        implode('<br>', $success)
    );
}

// Error notices
if (!empty($errors)) {
    echo sprintf('<div class="msg msg--neg marB-md">%s</div>',
        implode('<br>', $errors)
    );
}

?>
<form action="?remind" method="post">

    <p class="msg marB-sm">Enter your email address below to receive instructions to reset your password.</p>

    <div class="fld">
        <label>Email Address</label>
        <input type="email" name="email" value="<?=htmlspecialchars($email); ?>" required>
    </div>

    <div class="btns padT">
        <button class="btn btn--primary" type="submit">Send Reset Request</button>
        <a class="R" href="<?=Settings::getInstance()->SETTINGS['URL_IDX_LOGIN']; ?>">Back to Login</a>
    </div>

</form>