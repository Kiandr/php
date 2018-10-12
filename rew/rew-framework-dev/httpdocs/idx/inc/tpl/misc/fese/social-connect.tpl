<?php if (!empty($networks)) { ?>
    <div class="networks">
        <ul class="social-login">
            <?php foreach ($networks as $id => $network) { ?>
                <?php

                    // Microsoft live icon
                    if ($id === 'microsoft') {
                        $network['title'] = 'Microsoft';
                        $id = 'windows';
                    }

                ?>
                <li class="<?=$id; ?>">
                    <a title="Login with <?=Format::htmlspecialchars($network['title']); ?>" href="javascript:var w = window.open('<?=$network['connect']; ?>', 'socialconnect', 'toolbar=0,status=0,scrollbars=1,width=600,height=450,left='+(screen.availWidth/2-225)+',top='+(screen.availHeight/2-250)); w.focus();">
                        <?=sprintf('<i class="fa fa-%s"></i>', $id); ?>
                        <span><?=Format::htmlspecialchars($network['title']); ?></span>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>