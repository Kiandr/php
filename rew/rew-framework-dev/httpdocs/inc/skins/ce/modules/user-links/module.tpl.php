<?php

/**
 * @var \REW\Backend\Interfaces\User\SessionInterface $user
 */

?>
<?php if ($user->isValid()) { ?>
    <ul class="nav__list nav--user -text-xs -mar-bottom@sm -mar-bottom@md">
        <li class="nav__item -mar-left-xs">
            <a href="/idx/dashboard.html" class="nav__link -thumb -pad-0">
                <?php if ($userPhoto = $user->getPhotoUrl()) { ?>
                    <img height="48" width="48" class="-right" data-src="<?=htmlspecialchars($userPhoto); ?>">
                <?php } else { ?>
                    <img height="48" width="48" class="-right" data-src="<?=$default_image; ?>">
                <?php } ?>
            </a>
            <ul class="dropdown">
                <li class="dropdown__item">
                    <a class="dropdown__link" href="/idx/dashboard.html?view=listings">My Favorites</a>
                </li>
                <li class="dropdown__item">
                    <a class="dropdown__link" href="/idx/dashboard.html?view=searches">Saved Searches</a>
                </li>
                <li class="dropdown__item">
                    <a class="dropdown__link" href="/idx/dashboard.html?view=messages">Messages</a>
                </li>
                <li class="dropdown__item">
                    <a class="dropdown__link" href="/idx/dashboard.html?view=preferences">Preferences</a>
                </li>
                <li class="dropdown__item">
                    <a class="dropdown__link" href="/idx/logout.html">Sign Out</a>
                </li>
            </ul>
        </li>
    </ul>
<?php } else { ?>
    <ul class="nav__list nav--user signin -text-xs -inline">
        <li class="nav__item -text-upper -mar-left-xs">
            <a class="nav__link -pill" href="/idx/login.html">Sign In</a>
        </li>
    </ul>
<?php } ?>