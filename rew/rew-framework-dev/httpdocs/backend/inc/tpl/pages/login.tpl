<div class="login-wrap">

    <div class="app__login-aside">
    </div>

    <div class="app__login">
    	<h2><?=__('Sign In'); ?> <small class="R"><a href="<?=Settings::getInstance()->URLS['URL_BACKEND']; ?>remind/"><?=__('Forgot your password?'); ?></a></small></h2>
    	<form action="?submit" method="post" autocomplete="off" novalidate>
    		<div class="field">
    			<label class="field__label">
    				<?=tpl_lang('LBL_FORM_LOGIN_USERNAME'); ?>
    			</label>
    			<input class="w1/1" name="username" value="<?=htmlspecialchars($_POST['username']); ?>" autofocus required autocomplete="address-level4">
    		</div>
    		<div class="field">
    			<label class="field__label">
    				<?=tpl_lang('LBL_FORM_LOGIN_PASSWORD'); ?>
    			</label>
    			<input class="w1/1" type="password" name="password" value="" required autocomplete="off">
    		</div>
    		<div class="btns padT">
    			<button class="btn btn--strong"><?=__('Sign In'); ?></button>
    			<label class="toggle R">
    				<input type="checkbox" name="remember" value="true"<?=(!isset($_POST['remember']) || !empty($_POST['remember']) ? 'checked' : ''); ?>>
    				<span class="toggle__label"><?=tpl_lang('LBL_FORM_LOGIN_REMEMBER_ME'); ?></span>
    			</label>
    		</div>
    	</form>
    </div>

</div>


<div class="login-footer text text--mute text--small">
    <span>&copy; 2000-<?php echo date('Y'); ?>, <?=__('All Rights Reserved'); ?>.</span>
    <span>REW CRM <?=__('by'); ?> Real Estate Webmasters.</span>
</div>