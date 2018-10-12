<?=$subdomain->getInput(); ?>

<?php if (!empty($subdomains) && is_array($subdomains) && count($subdomains) > 1) { ?>
    <select class="w1/1" onchange="window.location.href = this.value;">
        <?php

        // Display list of available domains
        foreach ($subdomains as $otherSubdomain) {
            echo sprintf(
                '<option value="%s"%s>%s</option>',
                $otherSubdomain->getPostLink() ?: '?',
                $subdomain->compare($otherSubdomain) ? ' selected' : '',
                preg_replace('#https?://#i', '', trim($otherSubdomain->getLink(), '/'))
            );
        }

        ?>
    </select>
<?php } else { ?>
    <select class="w1/1" disabled>
        <option><?=!empty($subdomain->getTitle()) ? str_replace('http://','',strtolower($subdomain->getTitle())) : ''; ?></option>
    </select>
<?php } ?>

