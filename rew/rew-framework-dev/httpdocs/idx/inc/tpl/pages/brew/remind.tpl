<h1>Reset Password</h1>

<div class="msg"><p>Enter your email address below to receive instructions to reset your password.</p></div>

<?=(!empty($success) ? '<div class="msg positive"><p>' . implode('</p><p>', $success) . '</p></div>' : ''); ?>
<?=(!empty($errors)  ? '<div class="msg negative"><p>' . implode('</p><p>', $errors) . '</p></div>' : ''); ?>

<form action="?remind" method="post">

    <div class="field x12">
        <label>Email Address</label>
        <input type="email" name="email" value="<?=htmlspecialchars($email); ?>" required>
    </div>

    <div class="buttonset">
        <button class="strong" type="submit">Send Reset Request</button>
    </div>

</form>

<p><a href="<?=Settings::getInstance()->SETTINGS['URL_IDX_LOGIN']; ?>">Back to Login Form</a></p>