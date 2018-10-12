<?php

/**
 * @var bool $enabled
 * @var string $link
 * @var string $title
 * @var stirng $class
 */

?>

<?php if($enabled) { ?>
	<a class="bar__title" href="<?=htmlspecialchars($link); ?>"><i class="<?=htmlspecialchars($class); ?>"></i> <?=htmlspecialchars($title); ?></a>
<?php } else { ?>
	<span class="bar__title"><img src="/backend/img/rew-logo.png" height="24" alt="Real Estate Webmasters"></span>
<?php } ?>