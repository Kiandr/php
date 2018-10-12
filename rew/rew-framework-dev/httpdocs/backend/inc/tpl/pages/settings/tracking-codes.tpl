<form action="?submit" method="post" class="rew_check">
    <div class="bar">
	    <div class="bar__title"><?= __('Tracking Codes'); ?></div>
    </div>

    <div class="block">
        <?php
            echo $this->view->render('::partials/subdomain/selector', [
                'subdomain' => $subdomain,
                'subdomains' => $subdomains,
            ]);
        ?>
    </div>

    <div class="block">

        <h3><img src="<?=Settings::getInstance()->URLS['URL_BACKEND']; ?>img/icons/google.png" alt=""> <?= __('Google API Settings'); ?></h3>
    	<div class="field">
    		<label class="field__label"><?= __('Client ID'); ?></label>
    		<input class="w1/1" name="google_apikey" value="<?=htmlspecialchars($google_apikey); ?>">
    	</div>
    	<div class="field">
    		<label class="field__label"><?= __('Client Secret'); ?></label>
    		<input class="w1/1" name="google_secret" value="<?=htmlspecialchars($google_secret); ?>">
    	</div>
    	<p>
            <?= __(
                    'To unlock integrated Google features on your website, you must first %s to provide your Client ID and Client Secret.',
                    '<a href="https://code.google.com/apis/console/#access" target="_blank">' . __('register your application') . '</a>'
            ); ?>
        </p>
    	<p><?= __('When registering your app with Google, you must use the following settings:'); ?></p>
    	<div class="field">
    		<label class="field__label"><?= __('Redirect URIs:'); ?></label>
    		<input class="w1/1" value="<?=Settings::getInstance()->URLS['URL'] . 'oauth'; ?>" readonly>
    	</div>
    	<div class="field">
    		<label class="field__label"><?= __('JavaScript Origins:'); ?></label>
    		<input class="w1/1" value="<?=trim(Settings::getInstance()->URLS['URL'], '/'); ?>" readonly>
    	</div>
    	<div class="field">
    		<label class="field__label"><?= __('Google Verification Meta Tag'); ?></label>
    		<input class="w1/1" name="metatag" value="<?=$metav1; ?>" placeholder="YzkkuppaPeggsc7GvCGsRaeUJKPGmckut6i8fmcpdK5=">
    	</div>
    	<div class="field">
    		<label class="field__label"><?= __('Google Analytics UACCT Number'); ?></label>
    		<input class="w1/1" name="analytics" value="<?=$uacct; ?>" placeholder="UA-1728841-2">
    	</div>
    	<div class="field">
    		<label class="field__label"><?= __('Bing Verification Code'); ?></label>
    		<input class="w1/1" name="msvalidate" value="<?=$msvalidate; ?>" placeholder="0123456789ABCDEF0123456789ABCDEF">
    	</div>
    	<div class="field">
    		<label class="field__label"><?= __('5-Digit HitTail Key'); ?></label>
    		<input class="w1/1" name="hittail" value="<?=$hittail; ?>" placeholder="12345">
    	</div>
    	<div class="btns btns--stickyB"> <span class="R">
    		<button type="submit" class="btn btn--positive"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?></button>
    		</span>
    	</div>

    </div>

</form>