<div class="nav-login">
    <?php if (!empty($user) && $user->isValid()) { ?>
        <div class="nav">
            <ul>
                <li><a class="popup" href="/idx/dashboard.html">Dashboard</a></li>
                <li><a class="" href="/idx/logout.html">Sign Out</a></li>
            </ul>
        </div>
    <?php } else { ?>
        <div class="nav">
            <ul>
                <li><a class="popup" href="/idx/register.html">Register</a></li>
                <li><a class="popup" href="/idx/login.html">Sign In</a></li>
            </ul>
        </div>
        <?php if (!empty($networks)) { ?>
            <div class="networks">
                <h4>Log In Using...</h4>
                <ul class="social-login">
                    <?php foreach ($networks as $id => $network) { ?>
                        <?php $id = $id === 'microsoft' ? 'windows' : $id; ?>
                        <li class="<?=$id; ?>">
                            <a href="javascript:var w = window.open('<?=$network['connect']; ?>', 'socialconnect', 'toolbar=0,status=0,scrollbars=1,width=600,height=450,left='+(screen.availWidth/2-225)+',top='+(screen.availHeight/2-250)); w.focus();">
                                <?=sprintf('<i class="fa fa-%s"></i>', $id); ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
    <?php } ?>
</div>