<?php


echo $randomVar;

//-- set up the variable
if($_POST['firstName'] != '') 
{
	$firstName = $_POST['firstName'];
}

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
                    Welcome!
                </h2>
            </div>        
        </div>
        <div id="Body">
			<? if($firstName == ''): ?>
				<form method="post" action="index.php">
					 <div>
						<label>First Name:</label>
						<input type="text" name="firstName" />
					</div>
					<div>
						<label>&nbsp;</label>
						<input type="submit" name="submit" value="Personalize">
					</div>
				</form>
			<? else: ?>
				<h3>Welcome, <?=$firstName?></h3>
			<? endif;?>
			<div>
                <a href="aboutus.php?version=2">About Us</a> |
                <a href="newsletter.php">Join Our Newsletter</a>
            </div>
        </div>
	</body>
</html>