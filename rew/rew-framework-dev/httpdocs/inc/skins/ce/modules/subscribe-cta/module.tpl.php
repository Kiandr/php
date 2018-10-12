<?php

/**
 * @var bool $isOptIn
 * @var \REW\Core\Interfaces\SettingsInterface $settings
 */
if (!empty($isOptIn)) return;

?>
<div class="subscribe-cta">
    <div class="container">
        <div class="columns">
            <div class="column -width-1/2 -width-1/1@md -width-1/1@sm -width-1/1@xs">
                <h2 class="-text-upper -mar-bottom-0"><?php rew_snippet('site-signup-cta'); ?></h2>
            </div>
            <div class="column -width-1/2 -width-1/1@md -width-1/1@sm -width-1/1@xs">
                <form action="?newsletter" class="subscribe__form" method="POST">
                    <input type="hidden" name="opt_marketing" value="in">
                    <div class="input -pill">
                        <input placeholder="Your email address..." name="mi0moecs" value="">
                        <button type="submit" class="button button--strong button--pill">Sign Up</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>