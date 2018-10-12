<?php

/**
 * @var array $navigation
 * @var string $userName
 * @var string $userImage
 * @var string $editLink
 * @var string $logoutLink
 * @var string $helpLink
 */

?>

<a class="bar__action" data-drop="#menu--apps" data-drop-options='{"position" : "bottom right"}' href="javascript:void(0);" title="<?=htmlspecialchars($userName); ?>">
    <svg class="icon">
        <use xlink:href="/backend/img/icos.svg#icon-menu"></use>
    </svg>
</a>

<div class="menu menu--drop hidden -right" id="menu--apps" style="width: 320px; text-align: center">

	<ul class="menu__list menu__list--columns">
	    <?php if (!empty($navigation)):?>
            <?php foreach ($navigation AS $section):?>
                <?php if(!empty($section['link'])): ?>
                    <li class="menu__item -txtC -w1/3">
                    <a class="menu__link menu__link--rows text--small" href="<?=htmlspecialchars($section['link']);?>"<?=($section['current'] ? ' class="current"' : '');?>>
                        <?php if (!empty($section['icon'])):?>
                            <i class="sprite sprite--<?=htmlspecialchars($section['icon']);?> sprite--medium -marB8 -C"></i>
                        <?php endif;?>
                        <?=htmlspecialchars($section['title']);?>
                    </a>
                    </li>
                <?php endif;?>
			<?php endforeach;?>
		<?php endif;?>
	</ul>

	<div class="article pad" style="background: #edecef; text-align: left">
		<div class="article__body">
			<div class="article__thumb thumb -medium">
			    <img src="<?=htmlspecialchars($userImage); ?>">
		    </div>
			<div class="article__content">
				<div class="text -strong"><?=htmlspecialchars($userName); ?></div>
                <div class="text -mute">
                    <a class="txtSM" href="<?=htmlspecialchars($editLink); ?>" title="<?= __('Preferences'); ?>" > <?= __('Preferences'); ?></a> &nbsp;&nbsp;
                    <a class="txtSM" href="<?=htmlspecialchars($logoutLink); ?>" title="<?= __('Sign Out'); ?>"><?= __('Sign Out'); ?></a>&nbsp;&nbsp;
                    <a class="txtSM" href="<?=htmlspecialchars($helpLink); ?>" title="<?= __('Help'); ?>" ><?= __('Help'); ?></a>
                </div>
			</div>
		</div>
    </div>

</div>