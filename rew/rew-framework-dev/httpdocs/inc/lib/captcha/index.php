<?php

// Start Session
session_start();

// Process Submit
if (isset($_GET['submit'])) {

	// Verify CAPTCHA
	$_POST['captcha'] = strtoupper($_POST['captcha']);
	if (md5($_POST['captcha']) != $_SESSION['captcha'] || empty($_SESSION['captcha']) || !isset($_SESSION['captcha'])) {
		echo '<span>Wrong image code</span>';
	}
	unset($_SESSION['captcha']);

}

?>
<form action="?submit" method="post">
	<p><img src="captcha.php?<?=md5(time()); ?>" border="0"></p>
	<input name="captcha">
	<input type="submit" value="Submit">
</form>