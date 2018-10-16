<?php

include 'includes/general.php';

$_SESSION['firstName'] = "Humble";

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
                    About Us
                </h2>
            </div>        
        </div>
        <div id="Body">
			<h3>Welcome, <?=$firstName." ".$lastName?></h3>
            <pre>
                <?php //print_r($_GET); ?>
            </pre>
            <div>
                <a href="index.php">Home</a> |
                <a href="newsletter.php">Join Our Newsletter</a>
            </div>
        </div>
	</body>
</html>