<h3>Reset Password</h3>
<?=(!empty($success) ? '<div class="msg positive"><p>' . implode('</p><p>', $success) . '</p></div>' : ''); ?>
<?=(!empty($errors)  ? '<div class="msg negative"><p>' . implode('</p><p>', $errors) . '</p></div>' : ''); ?>
<form action="?remind" method="post">
    <div class="field x12">
        <input type="email" name="email" value="<?=htmlspecialchars($email); ?>" placeholder="Email Address" required>
    </div>
    <div class="btnset">
    	<a class="pright" href="<?=Settings::getInstance()->SETTINGS['URL_IDX_LOGIN']; ?>">Back to Login Form</a>
        <button class="strong" type="submit">Send Reset Request</button>
    </div>
</form>