<!DOCTYPE html>
<html>
    <head>
        <title>PHP Fundamentals</title>
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
            <pre>
                <?php print_r($_GET); ?>
            </pre>
            <div>
                <a href="newsletter.php">Back to the Newsletter</a>
            </div>
        </div>
	</body>
</html>