<?php

// Load default "module.tpl.php"
$template = $this->locateFile('module.tpl.php', __FILE__);
if (!empty($template)) {
	require_once $template;
}

?>
<div class="module nav">
	<header><h4>Subscribe</h4></header>
	<ul class="nav">
		<li><a href="<?=URL_BLOG; ?>rss/" target="_blank" title="RSS Feed"><i class="icon-rss"></i> RSS Feed</a></li>
	</ul>
</div>
