<?php if (empty($authorized)) : ?>
<?=errorMsg('You do not have permission to access this page.', '<img src="/backend/img/ills/security.png" width=200/> Authorization Error'); ?>
<?php else : ?>

<section>
	<header>
		<h1>Action Performed</h1>
		<div class="app_actions"> <a class="btn btn--positive" href="javascript:history.go(-1);">Go Back</a> </div>
	</header>
	<section>
		<?php if (!empty($success)) : ?>
		<div class="rewui message positive">
			<ul>
				<?php foreach ($success as $message) : ?>
				<li>
					<?=$message; ?>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php unset($success); ?>
		<?php endif; ?>
		<?php if (!empty($errors)) : ?>
		<div class="rewui message negative">
			<ul>
				<?php foreach ($errors as $error) : ?>
				<li>
					<?=$error; ?>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php unset($errors); ?>
		<?php endif; ?>
	</section>
</section>
<?php endif; ?>
