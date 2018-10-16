<?php

$uploadDirectory = $_SERVER['DOCUMENT_ROOT']."/uploads/";
$messageResult = "";

for($i = 0; $i < count($_FILES['myFiles']['name']); $i++)
{
	if (move_uploaded_file($_FILES['myFiles']['tmp_name'][$i], $uploadDirectory.$_FILES['myFiles']['name'][$i])) 
	{
		$messageResult .= "The file, ".$_FILES['myFiles']['name'][$i].", was succesfully uploaded.<br>";
	} 
	else 
	{
		$messageResult .=  "The file, ".$_FILES['myFiles']['name'][$i].", was <strong>NOT</strong> uploaded!<br>";
	}
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
                    Mailing List Information
                </h2>
            </div>        
        </div>
        <div id="Body">
			<?=$messageResult?>
            <pre>
				<?php print_r($_FILES); ?>
            </pre>
            <div>
                <a href="newsletter.php">Back to the Newsletter</a>
            </div>
        </div>
	</body>
</html>