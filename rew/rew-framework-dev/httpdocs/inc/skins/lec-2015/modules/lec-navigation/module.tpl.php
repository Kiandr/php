<div id="<?=$this->getUID(); ?>" class="hidden-sm">
	<?=$navigation; ?>
	<ul>
		<?php if ($user->isValid()) { ?>
			<?php // @todo: truncate long names here ??? ?>
			<li><a href="/idx/dashboard.html"><?=Format::htmlspecialchars($lead_name); ?></a></li>
		<?php } else { ?>
			<li><a data-dialog="#dialog-login">Sign In/Sign Up</a></li>
		<?php } ?>
	</ul>
</div>