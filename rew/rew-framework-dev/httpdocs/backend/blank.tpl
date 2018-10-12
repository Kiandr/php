<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="en" class="ie ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="ie ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="ie ie8"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html lang="en"> <!--<![endif]-->
    <head>
		<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?=isset($page_title) ? htmlspecialchars($page_title) : 'REW Backend'; ?></title>
        <?php foreach ($this->getStylesheets() as $stylesheet) echo $stylesheet->execute() . PHP_EOL; ?>
    </head>
    <body class="<?=$body_class; ?>">
        <?php echo $page->config('content'); ?>
        <?php $this->includeFile('inc/tpl/app/notifications.tpl.php'); ?>
        <?php foreach ($this->getJavascripts() as $javascript) echo $javascript->execute() . PHP_EOL; ?>
	</body>
</html>
