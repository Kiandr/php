<div class="login-wrap">

    <div class="app__login-aside">
    </div>

    <div class="app__login">
    	<h2><?=__('Password Reminder'); ?> <small class="R"><a href="<?=Settings::getInstance()->URLS['URL_BACKEND']; ?>login/"><?=__('Back to Login'); ?></a></small></h2>
    	<form action="?submit" method="post" autocomplete="off">
    		<?php if (!empty($show_form)) { ?>
    		<div class="field username">
    			<label class="field__label">
    				<?=tpl_lang('LBL_FORM_LOGIN_USERNAME'); ?>
    			</label>
    			<input class="w1/1" name="username" value="<?=htmlspecialchars($_POST['username']); ?>" autofocus required>
    		</div>
    		<div class="btns padT">
    			<button class="btn btn--strong" tyope="submit"><?=__('Send'); ?></button>
    		</div>
    		<?php } else { ?>
    		<div class="copy">
    			<p class="strong"><?=__('We\'ve sent password reset instructions to your email address.'); ?></p>
    			<p><?=__('If you don\'t receive instructions within a minute or two, check your email\'s spam and junk filters, or try %sresending your request', '<a href="?">'); ?></a>.</p>
    			<br>
    			<p><a href="<?=Settings::getInstance()->URLS['URL_BACKEND']; ?>login/"><?=__('Back to Login Form'); ?></a></p>
    		</div>
    		<?php } ?>
    	</form>
    </div>

</div>


<div class="login-footer text text--mute text--small">
    <span>&copy; 2000-<?php echo date('Y'); ?>, <?=__('All Rights Reserved'); ?>.</span>
    <span>REW CRM <?=__('by'); ?> Real Estate Webmasters.</span>
</div>