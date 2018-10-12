<h1>Reset Password</h1>

<div class="msg"><p>Reset Your Password.</p></div>

<?=(!empty($success) ? '<div class="msg positive"><p>' . implode('</p><p>', $success) . '</p></div>' : ''); ?>
<?=(!empty($errors)  ? '<div class="msg negative"><p>' . implode('</p><p>', $errors) . '</p></div>' : ''); ?>

<form action="?reset" method="post" autocomplete="off">

    <div class="field x12">
        <label>New Password</label>
        <input type="password" name="password" required>
    </div>
    <div class="field x12">
        <label>Confirm</label>
        <input type="password" name="confirm_password" required>
    </div>

    <div class="buttonset">
        <button class="strong" type="submit">Submit</button>
    </div>

</form>

<p><a href="<?=Settings::getInstance()->SETTINGS['URL_IDX_LOGIN']; ?>">Back to Login Form</a></p>
