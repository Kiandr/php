<form action="?submit" method="post" autocomplete="off">
	<div class="rewui rewmodule login_form block">
		<div class="rewmodule_title">Reset Your Password</div>
		<div class="rewmodule_content">
			<?php if (!empty($show_form)) { ?>
			<div>
				<div class="field username">
					<label class="field__label">New Password</label>
					<input class="w1/1" type="password" name="password" required>
				</div>
				<div class="field username">
					<label class="field__label">Confirm</label>
					<input class="w1/1" type="password" name="confirm_password" required>
				</div>
				<div class="rewui buttonset">
					<button class="btn btn--positive">Submit</button>
				</div>
			</div>
			<?php } else { ?>
			<div class="copy">
				<p class="strong">Your password has successfully been changed.</p>
			</div>
			<?php } ?>
		</div>
	</div>
</form>
