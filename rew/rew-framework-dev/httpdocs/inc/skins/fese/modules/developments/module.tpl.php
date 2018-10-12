<?php

if (!empty($developments)) {
    foreach ($developments as $development) {

        // Concatenate address component string
        $address = implode(', ', array_filter([
            $development['address'],
            $development['city'],
            $development['state'],
            $development['zip']
        ]));

        // Full details
        $details = [
            // Development info
            'website_url' => ['label' => 'Website URL', 'type' => 'url'],
            'completion_status' => ['label' => 'Completion Status'],
            'completion_date' => ['label' => 'Completion Date'],
            'completion_is_partial' => ['label' => 'Partial Completion', 'type' => 'enum'],
            // Building units
            'num_units' => ['label' => '# of Units', 'type' => 'number'],
            'unit_min_price' => ['label' => 'Unit Minimum Price', 'type' => 'currency'],
            'unit_max_price' => ['label' => 'Unit Maximum Price', 'type' => 'currency'],
            'unit_styles' => ['label' => 'Unit Styles'],
            'num_stories' => ['label' => '# of Stories', 'type' => 'number'],
            // Descriptions
            'common_features' => ['label' => 'Common Features'],
            'views' => ['label' => 'Views Description'],
            'construction' => ['label' => 'Construction'],
            'parking' => ['label' => 'Parking']

        ];

        // Load photo gallery module
        $photoGallery = $this->getContainer()->module('fgallery', [
            'images' => $development['images']
        ])->display(false);

        // Community photo gallery
        $communityPhotoGallery = false;
        if ($community = $development['community']) {
            $communityPhotoGallery = $this->getContainer()->module('fgallery', [
                'images' => $community['images']
            ])->display(false);
        }

        // More developments
        $moreDevelopments = $this->getContainer()->module('developments', [
            'template' => 'results.tpl.php',
            'javascript' => 'results.js.php',
            'stylesheet' => 'results.css.php',
            'exclude' => (int) $development['id']
        ])->display(false);

?>
<div class="community">
    <div class="wrp S4">
        <h1 class="txtDisplayAlt"><?=Format::htmlspecialchars($development['title']); ?></h1>
        <?php if (!empty($development['subtitle'])) { ?>
            <div class="cols marB">
                <div class="col w1/1">
                    <div class="description"><?=Format::htmlspecialchars($development['subtitle']); ?></div>
                </div>
            </div>
        <?php } ?>
        <div class="cols">
            <div class="col w1/1">
                <?=$photoGallery; ?>
            </div>
        </div>
        <?php if (!empty($development['description'])) { ?>
            <article class="development details marB">
                <div class="desc">
                    <?php if (!empty($development['about_heading'])) { ?>
                        <h2><?=Format::htmlspecialchars($development['about_heading']); ?></h2>
                    <?php } ?>
                    <p><?=Format::htmlspecialchars($development['description']); ?></p>
                </div>
            </article>
        <?php } ?>
        <?php if ($tags = $development['tags']) { ?>
            <p><?=implode(' &bull; ', $development['tags']);?></p>
        <?php } ?>
        <article class="building-details marB">
            <div class="desc">
                <h2>Building Details</h2>
                <ul>
                    <?php

                        // Display building details
                        foreach ($details as $field => $detail) {
                            $value = Format::trim($development[$field]);
                            if (empty($value)) continue;
                            switch ($detail['type']) {
                                // Currency
                                case 'currency':
                                    $value = '$' . Format::number($value);
                                    break;
                                // Number
                                case 'number':
                                    $value = Format::number($value);
                                    break;
                                // ENUM (Y/N)
                                case 'enum':
                                    $value = $value === 'Y' ? 'Yes' : false;
                                    break;
                                // Link URL
                                case 'url':
                                    $value = sprintf(
                                        '<a href="%2$s" target="_blank" rel="nofollow">%1$s</a>',
                                        parse_url($value, PHP_URL_HOST) ?: $value,
                                        $value
                                    );
                                    break;
                            }
                            // Display details
                            if (!empty($value)) {
                                echo sprintf(
                                    '<li>%s: %s</li>',
                                    $detail['label'],
                                    $value
                                );
                            }
                        }

                    ?>
                </ul>
            </div>
        </article>
        <?php if (!empty($address)) { ?>
            <article class="dev-details-properties">
                <h2>Address</h2>
                <?=Format::htmlspecialchars($address); ?>
            </article>
        <?php } ?>

        <?php if (!empty($development['listings'])) { ?>
            <article class="dev-details-properties">
                <h2 class="marT-md">Available Properties</h2>
                <?php

                    // Display IDX search results
                    echo '<div class="listings cols">';
                    foreach ($development['listings'] as $result) {
                        include $result_tpl;
                    }
                    echo '</div>';

                ?>
            </article>
        <?php } ?>

        <?php if ($community = $development['community']) { ?>

            <div class="cols neighborhood-details">
                <div class="col w1/1">
                    <h2>The Neighborhood</h2>
                    <?=$communityPhotoGallery; ?>
                </div>
            </div>

            <article>
                <div class="desc">
                    <span class="lg-title"><?=Format::htmlspecialchars($community['title']); ?></span>
                    <?php if (!empty($community['description'])) { ?>
                        <p><?=nl2br($community['description']); ?></p>
                    <?php } ?>
                </div>
            </article>

            <?php } ?>

        <?php if (!empty($moreDevelopments)) { ?>
            <div class="articleset cols all-development-results marB"></div>
                <h2>Available Developments</h2>
                <?=$moreDevelopments; ?>
            </div>
        <?php } ?>

    </div>
</div>
<?

    }
}
