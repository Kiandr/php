<form action="?submit" method="post" class="rew_check">

<div class="bar">
    <div class="bar__title">IDX Social Connect</div>
    <div class="bar__actions">
        <a class="bar__action" href="/backend/leads/tools/"><svg class="icon icon-left-a mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
    </div>
</div>

<div class="block">

	<div class="btns btns--stickyB"> <span class="R">
		<button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> Save</button>
		</span> </div>
	<p>Please be aware that these features are dependent on the operation and cooperation of third party services. As such, Real Estate Webmasters can't guarantee continuous availability of these features.</p>
	<h3 class="panel__hd">
        <img class="-marR8" src="<?=Settings::getInstance()->URLS['URL_BACKEND']; ?>img/icons/facebook_16x16.svg" alt="" style="vertical-align: middle;">
        <span style="vertical-align: middle;">Facebook API Settings</span>
    </h3>
	<div class="field">
		<label class="field__label">App ID</label>
		<input class="w1/1" name="facebook_apikey" value="<?=htmlspecialchars($settings['facebook_apikey']); ?>">
	</div>
	<div class="field">
		<label class="field__label">App Secret</label>
		<input class="w1/1" name="facebook_secret" value="<?=htmlspecialchars($settings['facebook_secret']); ?>">
	</div>
	<div class="field">
		<p>To unlock integrated Facebook features on your website, you must first <a href="http://developers.facebook.com/apps/" target="_blank">register your application</a> to provide your App ID/API Key and App Secret.</p>
		<p>When registering your app with Facebook, you must use the following settings:</p>
		<div class="field">
			<label class="field__label">App Domain:</label>
			<input class="w1/1" class="ui-state-highlight" value="<?=preg_replace('#^http://#i', '', trim(Settings::getInstance()->URLS['URL'], '/')); ?>" readonly>
		</div>
	</div>
	<h3 class="panel__hd">
        <img class="-marR8" src="<?=Settings::getInstance()->URLS['URL_BACKEND']; ?>img/icons/google_16x16.svg" alt="" style="vertical-align: middle;">
        <span style="vertical-align: middle;">Google API Settings</span>
    </h3>
	<div class="field">
		<label class="field__label">Client ID</label>
		<input class="w1/1" name="google_apikey" value="<?=htmlspecialchars($settings['google_apikey']); ?>">
	</div>
	<div class="field">
		<label class="field__label">Client Secret</label>
		<input class="w1/1" name="google_secret" value="<?=htmlspecialchars($settings['google_secret']); ?>">
	</div>
	<div class="field">
		<p>To unlock integrated Google features on your website, you must first <a href="https://code.google.com/apis/console/#access" target="_blank">register your application</a> to provide your Client ID and Client Secret.</p>
		<p>When registering your app with Google, you must use the following settings:</p>
		<div class="field">
			<label class="field__label">Redirect URIs:</label>
			<input class="w1/1" class="ui-state-highlight" value="<?=Settings::getInstance()->URLS['URL'] . 'oauth'; ?>" readonly>
		</div>
		<div class="field">
			<label class="field__label">JavaScript Origins:</label>
			<input class="w1/1" class="ui-state-highlight" value="<?=trim(Settings::getInstance()->URLS['URL'], '/'); ?>" readonly>
		</div>
	</div>
    <?php if(Settings::getInstance()->SSL === true || !empty($settings['microsoft_apikey']) || !empty($settings['microsoft_secret'])) { ?>
	<h3 class="panel__hd">
        <img class="-marR8" src="<?=Settings::getInstance()->URLS['URL_BACKEND']; ?>img/icons/windows_16x16.svg" alt="" style="vertical-align: middle;">
        <span style="vertical-align: middle;">Windows Live API Settings</span>
    </h3>
	<div class="field">
		<label class="field__label">Client ID</label>
		<input class="w1/1" name="microsoft_apikey" value="<?=htmlspecialchars($settings['microsoft_apikey']); ?>">
	</div>
	<div class="field">
		<label class="field__label">Client Secret</label>
		<input class="w1/1" name="microsoft_secret" value="<?=htmlspecialchars($settings['microsoft_secret']); ?>">
	</div>
	<div class="field">
		<p>To unlock integrated Windows Live features on your website, you must first <a href="https://account.live.com/developers/applications" target="_blank">register your application</a> to provide your Client ID and Client Secret.</p>
		<p>When registering your app with Windows Live, you must use the following settings:</p>
		<div class="field">
			<label class="field__label">Redirect Domain:</label>
			<input class="w1/1" class="ui-state-highlight" value="<?=Settings::getInstance()->URLS['URL']; ?>oauth" readonly>
		</div>
	</div>
    <?php } ?>
	<h3 class="panel__hd">
        <img class="-marR8" src="<?=Settings::getInstance()->URLS['URL_BACKEND']; ?>img/icons/twitter_16x16.svg" alt="" style="vertical-align: middle;">
        <span style="vertical-align: middle;">Twitter API Settings</span>
    </h3>
	<div class="field">
		<label class="field__label">Consumer Key</label>
		<input class="w1/1" name="twitter_apikey" value="<?=htmlspecialchars($settings['twitter_apikey']); ?>">
	</div>
	<div class="field">
		<label class="field__label">Consumer Secret</label>
		<input class="w1/1" name="twitter_secret" value="<?=htmlspecialchars($settings['twitter_secret']); ?>">
	</div>
	<div class="field">
		<p>To unlock integrated Twitter features on your website, you must first <a href="https://dev.twitter.com/apps" target="_blank">register your application</a> to provide your Consumer Key and Consumer Secret.</p>
		<p>When registering your app with Twitter, you must use the following settings:</p>
		<div class="field">
			<label class="field__label">Callback URL:</label>
			<input class="w1/1" class="ui-state-highlight" value="<?=Settings::getInstance()->URLS['URL']; ?>" readonly>
		</div>
	</div>
	<h3 class="panel__hd">
        <img class="-marR8" src="<?=Settings::getInstance()->URLS['URL_BACKEND']; ?>img/icons/linkedin_16x16.svg" alt="" style="vertical-align: middle;">
        <span style="vertical-align: middle;">LinkedIn API Settings</span>
    </h3>
	<div class="field">
		<label class="field__label">API Key</label>
		<input class="w1/1" name="linkedin_apikey" value="<?=htmlspecialchars($settings['linkedin_apikey']); ?>">
	</div>
	<div class="field">
		<label class="field__label">Secret Key</label>
		<input class="w1/1" name="linkedin_secret" value="<?=htmlspecialchars($settings['linkedin_secret']); ?>">
	</div>
	<div class="field">
		<p>To unlock integrated LinkedIn features on your website, you must first <a href="https://www.linkedin.com/secure/developer" target="_blank">register your application</a> to provide your API Key and Secret Key.</p>
	</div>
	<h3 class="panel__hd">
        <img class="-marR8" src="<?=Settings::getInstance()->URLS['URL_BACKEND']; ?>img/icons/yahoo_16x16.svg" alt="" style="vertical-align: middle;">
        <span style="vertical-align: middle;">Yahoo! API Settings</span>
    </h3>
	<div class="field">
		<label class="field__label">Client ID</label>
		<input class="w1/1" name="yahoo_apikey" value="<?=htmlspecialchars($settings['yahoo_apikey']); ?>">
	</div>
	<div class="field">
		<label class="field__label">Client Secret</label>
		<input class="w1/1" name="yahoo_secret" value="<?=htmlspecialchars($settings['yahoo_secret']); ?>">
	</div>
	<div class="field">
		<p>To unlock integrated Yahoo! features on your website, you must first <a href="http://developer.yahoo.com/oauth" target="_blank">register your application</a> to provide your Consumer Key and Consumer Secret.</p>
		<p>When registering your app, you must select set permissions to: <span class="ui-state-highlight" style="padding: 2px 5px;">Social Directory (Profiles) Read/Write Public and Private</span></p>
	</div>

</div>

</form>