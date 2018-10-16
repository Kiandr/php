<?php

include 'includes/general.php';

session_destroy();

?>
<!DOCTYPE html>
<html>
    <head>
        <title>PHP Predefined Variables</title>
		<link href="assets/styles.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
		<div id="HeaderWrapper">
            <div id="Header">
                <a href="index.php"><img src="assets/hwl.jpg" border="0" alt=""></a>
                <h2>
                    Mailing List Information
                </h2>
            </div>        
        </div>
        <div id="Body">
			<h3>Thank you, <?=$firstName." ".$lastName?>, for subscribing!</h3>
            <pre>
                <?php print_r($_POST); ?>
            </pre>
            <div>
                <a href="newsletter.php">Back to the Newsletter</a>
            </div>
        </div>
	</body>
</html>