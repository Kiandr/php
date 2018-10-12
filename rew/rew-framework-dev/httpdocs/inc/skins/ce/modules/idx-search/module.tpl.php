<?php

/**
 * @var array $panels
 * @var boolean $linkAdvanced
 * @var boolean $showAdvanced
 * @var \IDX_Search_Tag[] $idxTags
 * @var array $queryStringParameters
 * @var array[] $sortOptions {
 *   @var string $value
 *   @var string $title
 * }
 * @var array $sortOrder {
 *   @var string $value
 *   @var string $title
 * }
 */

?>
<div class="quicksearch">
    <div id="sub-quicksearch" class="container">
        <?php

            // Display IDX feed switcher
            $this->getPage()->container('idx-feeds')->addModule('idx-feeds', [
                'dataAttr' => !empty($linkAdvanced)
            ])->display();

        ?>
        <form id="<?=$this->getUID() ; ?>" action="<?=$settings->SETTINGS['URL_IDX_SEARCH']; ?>" method="get" class="search__form idx-search">

            <input type="hidden" name="feed" value="<?=htmlspecialchars($settings->IDX_FEED); ?>">
            <input type="hidden" name="sortorder" value="<?=htmlspecialchars($_REQUEST['sortorder']); ?>">
            <input type="hidden" name="refine" value="true">

            <input type="hidden" name="map[zoom]" value="<?=htmlspecialchars($_REQUEST['map']['zoom']); ?>">
            <input type="hidden" name="map[longitude]" value="<?=htmlspecialchars($_REQUEST['map']['longitude']); ?>">
            <input type="hidden" name="map[latitude]" value="<?=htmlspecialchars($_REQUEST['map']['latitude']); ?>">
            <input type="hidden" name="map[polygon]" value="<?=htmlspecialchars($_REQUEST['map']['polygon']); ?>">
            <input type="hidden" name="map[radius]" value="<?=htmlspecialchars($_REQUEST['map']['radius']); ?>">
            <input type="hidden" name="map[bounds]" value="<?=(!empty($_REQUEST['bounds']) ? 1 : 0); ?>">
            <input type="hidden" name="map[ne]" value="<?=htmlspecialchars($_REQUEST['map']['ne']); ?>">
            <input type="hidden" name="map[sw]" value="<?=htmlspecialchars($_REQUEST['map']['sw']); ?>">

            <?php

                // Create Lead Search
                if (!empty($_REQUEST['create_search']) && !empty($backend_user) && !empty($lead)) {
                    echo '<input type="hidden" name="lead_id" value="' . $lead['id'] . '">';
                    echo '<input type="hidden" name="create_search" value="true">';

                // Edit Saved Search
                } else if (!empty($saved_search) && !empty($_REQUEST['edit_search'])) {
                    echo '<input type="hidden" name="saved_search_id" value="' . $saved_search['id'] . '">';
                    echo '<input type="hidden" name="edit_search" value="true">';

                    // Edit Lead Search
                    if (!empty($backend_user) && !empty($lead)) {
                        echo '<input type="hidden" name="lead_id" value="' . $lead['id'] . '">';
                    }

                }

            ?>

            <div class="search__container -pill">
                <div class="input -pill">
                    <?php

                        // Display location search
                        echo IDX_Panel::get('Location', [
                            'inputClass' => 'autocomplete location -pill',
                            'placeholder'    => sprintf(
                                'City, %s, Address, %s or %s #',
                                Locale::spell('Neighborhood'),
                                Locale::spell('Zip'),
                                Lang::write('MLS')
                            ),
                            'toggle' => false
                        ])->getMarkup();

                    ?>
                    <div class="search__filters<?=$showAdvanced ? ' -is-active' : ''; ?>">
                        <div>
                            <?php

                                // Display refine button to toggle search panels
                                if (!empty($linkAdvanced)) {
                                    echo sprintf('<a href="%s?advanced" class="refine" data-expand>', $settings->SETTINGS['URL_IDX_SEARCH']);
                                    echo '<span class="-text-xs -text-upper -is-hidden@xs -is-hidden@sm"> More Options</span>';
                                    echo '<svg class="icon--filter -mar-left-xs"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/inc/skins/ce/img/assets.svg#icon--filters"></use></svg>';
                                    echo '</a>';

                                } else {
                                    $tagCount = count($idxTags);
                                    $expandLabel = $showAdvanced ? 'Less Options' : 'More Options';
                                    echo sprintf('<a class="refine%s" data-expand>', $showAdvanced ? ' -is-active' : '');
                                    echo sprintf('<span class="-text-xs -text-upper -is-hidden@xs -is-hidden@sm"> %s</span>', $expandLabel);
                                    echo '<svg class="icon--filter -mar-left-xs"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/inc/skins/ce/img/assets.svg#icon--filters"></use></svg>';
                                    if ($tagCount > 0) echo sprintf('<span class="badge -mar-left-xs">%s</span>', $tagCount) . PHP_EOL;
                                    echo '</a>';
                                }

                            ?>
                        </div>
                    </div>
                    <button type="submit" id="quicksearch-submit" class="button button--strong button--pill -text-upper<?=$showAdvanced ? ' -is-active' : ''; ?>">
                        <svg class="icon--search">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/inc/skins/ce/img/assets.svg#icon--search"></use>
                        </svg>
                        <span><?=$this->config('button') ?: 'Find Your Home'; ?></span>
                    </button>
                </div>
                <?php if (empty($linkAdvanced)) { ?>
                    <div class="adv-search <?=$showAdvanced ? ' expanded' : ' -is-hidden'; ?>">
                        <div class="columns">
                            <?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_DRIVE_TIME']) && in_array('drivetime', $settings->ADDONS)) { ?>
                                <?php $driveTimePanel = IDX_Panel::get('DriveTime'); ?>
                                <div id="field-drive_time" class="field drivetime search__panel column -width-1/1">
                                    <label><?=sprintf('<i class="fa fa-automobile -mar-right-xs"></i> %s', $driveTimePanel->getTitle()); ?></label>
                                    <?=$driveTimePanel->getMarkup(); ?>
                                </div>
                            <?php } ?>
                            <div id="field-price" class="field search__panel column -width-1/2 -width-1/1@sm -width-1/1@xs">
                                <label>Price Range</label>
                                <?php

                                    // Display price range field
                                    $priceRange = IDX_Panel::get('Price', [
                                        'placeholderMinPrice' => 'Min. Price',
                                        'placeholderMaxPrice' => 'Max. Price',
                                        'placeholderMinRent' => 'Min. Rent',
                                        'placeholderMaxRent' => 'Max. Rent'
                                    ]);

                                    echo $priceRange->getMarkup();

                                ?>
                            </div>
                            <div id="field-rooms" class="field search__panel column -width-1/2 -width-1/1@sm -width-1/1@xs">
                                <label>Rooms</label>
                                <?php

                                    // Display bedrooms/bathrooms panel
                                    $rooms = IDX_Panel::get('Rooms', ['placeholderBeds' => 'Bedrooms', 'placeholderBaths' => 'Bathrooms']);
                                    echo sprintf('<div class="search__field field">%s</div>',  $rooms->getMinBeds());
                                    echo sprintf('<div class="search__field field">%s</div>',  $rooms->getMinBaths());

                                ?>
                            </div>
                            <?php

                                // Display IDX search panels
                                foreach ($panels as $panel) {
                                    $panelClasses = 'search__panel column -width-1/2 -width-1/1@sm -width-1/1@xs';;
                                    $panel->setPanelClass(sprintf('%s %s', $panel->getPanelClass(), $panelClasses));
                                    $panel->display();
                                }

                            ?>
                        </div>

                        <div class="buttons">
                            <ul class="-pad-0 -left -mar-vertical-sm">
                                <li class="filters__list">
                                    <button type="button" class="button button--bordered button--pill -mar-bottom-sm">
                                        <?=htmlspecialchars($sortOrder['title']); ?>
                                    </button>
                                    <ul id="sort-orders" class="filters__dropdown">
                                        <?php foreach ($sortOptions as $sortOption) {
                                            echo sprintf(
                                                '<li class="dropdown__item dropdown__current"><a class="dropdown__link" href="#" data-value="%s">%s</a></li>',
                                                $sortOption['value'],
                                                htmlspecialchars($sortOption['title'])
                                            );
                                        } ?>
                                    </ul>
                                </li>
                            </ul>
                            <button type="submit" class="button button--strong button--pill -mar-vertical-sm -right -text-upper">Find Your Home</button>
                        </div>
                    </div>
                    <?php

                        // Edit search controls
                        if (!empty($saved_search) && !empty($_REQUEST['edit_search'])) {

                            // $_REQUEST over-ride
                            $saved_search['title'] = $_REQUEST['search_title'] ?: $saved_search['title'];
                            $saved_search['frequency'] = $_REQUEST['frequency'] ?: $saved_search['frequency'];

                            // Edit controls
                            echo '<div class="columns">'
                                . '<div class="field search__panel column -width-1/2 -width-1/1@sm -width-1/1@xs">'
                                    . '<label>Search Title</label>'
                                    . '<input type="text" name="search_title" value="' . htmlspecialchars($saved_search['title']) . '" required>'
                                . '</div>'
                                . '<div class="field search__panel column -width-1/2 -width-1/1@sm -width-1/1@xs">'
                                    . '<label>Update Frequency</label>'
                                    . '<select name="frequency">'
                                        . '<option value="never"' . ($saved_search['frequency'] == 'never' ? ' selected' : '') . '>Never</option>'
                                        . '<option value="immediately"' . ($saved_search['frequency'] == 'immediately' ? ' selected' : '') . '>Immediately</option>'
                                        . '<option value="daily"' . ($saved_search['frequency'] == 'daily' ? ' selected' : '') . '>Daily</option>'
                                        . '<option value="weekly"' . (empty($saved_search['frequency']) || $saved_search['frequency'] == 'weekly' ? ' selected' : '') . '>Weekly</option>'
                                        . '<option value="monthly"' . ($saved_search['frequency'] == 'monthly' ? ' selected' : '') . '>Monthly</option>'
                                    . '</select>'
                                . '</div>'
                            . '</div>';

                        }

                        // Hidden map search controls
                        echo '<div id="map-draw-controls" class="hidden">';
                        echo '<div id="field-polygon">' . IDX_Panel::get('Polygon')->getMarkup() . '</div>';
                        echo '<div id="field-radius">' . IDX_Panel::get('Radius')->getMarkup() . '</div>';
                        echo '<div id="field-bounds">' . IDX_Panel::get('Bounds')->getMarkup() . '</div>';
                        echo '</div>';

                    }

                ?>
			</div>
        </form>
    </div>
</div>