<?php if (!empty($testimonials)) { ?>

	<?php foreach ($testimonials as $testimonial) { ?>
		<div class="testimonial -text-md -mar-bottom-lg -pad-bottom-md">
			<?=$testimonial['testimonial']; ?>
			<?=!empty($testimonial['client']) ? '<small class="-block -text-sm"> ' . '- ' . $testimonial['client'] . '</small>' : ''; ?>
		</div>
	<?php } ?>

<?php } ?>