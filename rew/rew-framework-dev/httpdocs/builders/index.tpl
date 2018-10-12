<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title></title>
		<meta name="description" content="">
		<link href="/builders/res/css/app.css" rel="stylesheet">
	</head>
	<body class="bdx-standalone">
		<div id="main" class="container">
			<?=$content; ?>
		</div>
		
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script src="/builders/res/js/lib/require-2.1.15.min.js"></script>
		<script src="/builders/res/js/app.js"></script>
		<?php if (!empty($javascript) && is_array($javascript)) { ?>
			<?php foreach ($javascript as $js) { ?>
				<script>require(['<?=$js;?>'])</script>
			<?php } ?>
		<?php } ?>
	</body>
</html>