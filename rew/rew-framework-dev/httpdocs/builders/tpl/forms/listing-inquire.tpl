<?php namespace BDX; ?>

<div class="listing-inquire-wrap">

	<div class="error-success">
		<?=(!empty($success) ? '<div class="bdx-msg-positive"><p>' . implode('</p><p>', $success) . '</p></div>' : ''); ?>
		<?=(!empty($errors) ? '<div class="bdx-msg-negative"><p>' . implode('</p><p>', $errors) . '</p></div>' : ''); ?>
	</div>

	<?php if ($show_form) { ?>
	
		<h4>Ask a Real Estate Professional for more info about <?=$listing['PlanName'];?></span></h4>

		<div class="bdx-inquire-form">
			
			<form method="post" action="?submit" id='form_tpl_listing_inquire'>
	
				<input type="text" value="" style="display: none;" name="email">
				<input type="text" value="" style="display: none;" name="first_name">
				<input type="text" value="" style="display: none;" name="last_name">
				<input type="hidden" name="listing_id" value="<?=$listing['id'];?>">
				<input type="hidden" name="lender" value="<?=$lender['id'];?>">
				<input type="hidden" name="source" value="<?=$_SERVER['HTTP_REFERER']; ?>"> 
	
				<div class="field firstname">
					<label for="onc5khko">First Name</label>
					<input name="onc5khko" value="<?=htmlspecialchars($_POST['onc5khko']); ?>" required>
				</div>
	
				<div class="field lastname">
					<label for="sk5tyelo">Last Name</label>
				    <input name="sk5tyelo" value="<?=htmlspecialchars($_POST['sk5tyelo']); ?>" required>
				</div>
	
				<div class="field email">
					<label for="mi0moecs">Email</label>
				    <input type="email" name="mi0moecs" value="<?=htmlspecialchars($_POST['mi0moecs']); ?>" required>
				</div>
	
				<div class="field phone">
					<label for="telephone">Phone</label>
				    <input type="tel" name="telephone" value="<?=htmlspecialchars($_POST['telephone']); ?>" required>
				</div>
	
				<div class="field message">
					<label for="comments">Message</label>
					<textarea rows="3" name="comments" required><?=htmlspecialchars($_POST['comments']); ?></textarea>
				</div>
	
				<div class="btnset">
					<button type="submit">Send Inquiry</button>
				</div>
	
			</form>
		</div>
	<?php } ?>
</div>

<?php if (!empty($success) && empty($errors)) { ?>
	<script>
		// Javascript Callback
		setTimeout(function () {
			window.parent.location.reload();
			window.parent.$.Window('close');
		}, 1000);
	</script>
<?php } ?>