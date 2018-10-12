<form action="?submit" method="post" class="rew_check">

    <div class="bar">
        <div class="bar__title"><?= __('Blog Settings'); ?></div>
    </div>

    <div class="btns btns--stickyB">
        <span class="R">
            <button class="btn btn--positive" type="submit"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-check"/></svg> <?= __('Save'); ?></button>
        </span>
    </div>

    <div class="block">
        <div class="cols">
        	<div class="field col w1/4">
        		<label class="field__label"><?= __('Blog Name'); ?> <em class="required">*</em></label>
        		<input class="w1/1" name="blog_name" value="<?=htmlspecialchars($blog_settings['blog_name']); ?>" required>
        	</div>
        	<div class="field col w3/4">
        		<label class="field__label"><?= __('Page Title'); ?></label>
        		<input class="w1/1" name="page_title" value="<?=htmlspecialchars($blog_settings['page_title']); ?>">
        		<p class="text--mute"><?=tpl_lang('DESC_FORM_BLOG_TITLE'); ?></p>
        	</div>
        </div>
    	<div class="field">
    		<label class="field__label"><?= __('Meta Description'); ?></label>
    		<textarea class="w1/1" id="meta_tag_desc" name="meta_tag_desc" cols="24" rows="4"><?=htmlspecialchars($blog_settings['meta_tag_desc']); ?></textarea>
    		<p class="text--mute"><?=tpl_lang('DESC_FORM_BLOG_DESCRIPTION'); ?></p>
    	</div>
    	<div class="field">
    		<label class="field__label"><?= __('Enable CAPTCHA'); ?></label>
    		<div>
    			<label class="toggle" for="captcha_true">
    			    <input type="radio" name="captcha" id="captcha_true" value="t"<?=($blog_settings['captcha'] == 't') ? ' checked' : ''; ?>>
    			    <span class="toggle__label"><?= __('Enabled'); ?></span>
    			 </label>
    			<label class="toggle" for="captcha_false">
    			    <input type="radio" name="captcha" id="captcha_false" value="f"<?=($blog_settings['captcha'] != 't') ? ' checked' : ''; ?>>
    			    <span class="toggle__label"><?= __('Disabled'); ?></span>
                </label>
    		</div>
    		<p class="text--mute"><?= __('Enable CAPTCHA for Blog Comments to add extra protection against SPAM bots.'); ?></p>
    	</div>
    </div>

</form>
