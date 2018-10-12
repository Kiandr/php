<div id="sub-quicksearch">
    <?php

        // Display IDX feed switcher
        if (!empty($this->config['showFeeds'])) {
            $this->getContainer()->addModule('idx-feeds', array(
                'template' => 'idx-search.tpl.php',
                'disabled' => !!$this->config('advanced')
            ))->display();
        }

    ?>
    <form id="<?=$this->getUID() ; ?>" action="<?=Settings::getInstance()->SETTINGS['URL_IDX_SEARCH']; ?>" method="get" class="idx-search">

        <input type="hidden" name="feed" value="<?=htmlspecialchars(Settings::getInstance()->IDX_FEED); ?>"
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

        <div class="idx-search-form">
            <div class="mmm search-input-container">
                <?php

                    // Display location search
                    echo IDX_Panel::get('Location', [
                        'inputClass' => 'autocomplete location',
                        'placeholder'	=> sprintf(
                            'City, %s, Address, %s or %s #',
                            Locale::spell('Neighborhood'),
                            Locale::spell('Zip'),
                            Lang::write('MLS')
                        ),
                        'toggle' => false,
                    ])->getMarkup();

                ?>
                <button type="submit" class="btn btn--primary">
                    <svg style="width:16px;height:16px;vertical-align: middle; position: relative; top: -1px; margin: -1px 0 0 0" viewBox="0 0 24 24">
                        <path fill="#fff" d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z" />
                    </svg>
                    <?=$this->config('button') ?: 'Search'; ?>
                </button>
            </div>
            <div style="display: flex;" class="nnn">
                <div style="display: inline-flex; margin-right: 20px" class="nnn-b">
                    <?php

                        // Display price range field
                        echo IDX_Panel::get('Price', [
                            'placeholderMinPrice' => 'Min Price',
                            'placeholderMaxPrice' => 'Max Price',
                            'placeholderMinRent' => 'Min Rent',
                            'placeholderMaxRent' => 'Max Rent',
                            'toggle' => false
                        ])->getMarkup();

                    ?>
                </div>
                <select name="minimum_bedrooms" style="margin-right: 20px;">
                    <option value="">All Beds</option>
                    <?php

                        // Minimum bedroom options
                        for ($beds = 1; $beds <= 8; $beds++) {
                            $selected = $_REQUEST['minimum_bedrooms'] == $beds ? ' selected' : '';
                            echo sprintf('<option%s value="%s">%s+ Beds</option>', $selected, $beds, $beds);
                        }

                    ?>
                </select>
                <select name="minimum_bathrooms">
                    <option value="">All Baths</option>
                    <?php

                        // Minimum bedroom options
                        for ($baths = 1; $baths <= 8; $baths++) {
                            $selected = $_REQUEST['minimum_bathrooms'] == $baths ? ' selected' : '';
                            echo sprintf('<option%s value="%s">%s+ Baths</option>', $selected, $baths, $baths);
                        }

                    ?>
                </select>
                <?php if ($this->config('advanced') && !empty($panels)) { ?>
                    <a class="show-advanced">
                        <span class="inner-text">
                            <?=($show_advanced ? 'Less Options' : 'More Options'); ?>
                        </span>
                    </a>
                <?php } ?>
            </div>
        </div>

        <?php

            // Advanced search options
            if ($this->config('advanced') && !empty($panels)) {

                // Current search tags
                echo '<div class="search-criteria" data-idx-tags>' . PHP_EOL;
                if (!empty($idx_tags) && is_array($idx_tags)) {
                    echo '<div class="msg marB-md">';
                    foreach ($idx_tags as $tag) {
                        echo '<a class="btn S1" data-idx-tag=\'' . json_encode($tag->getField()) . '\'>';
                        echo Format::htmlspecialchars($tag->getTitle()) . PHP_EOL;
                        echo '<svg xmlns="http://www.w3.org/2000/svg" width="8" height="8" viewBox="0 0 12.531 12.531">';
                        echo '<path d="M79.908,27.171l-5.091,5.091,5.069,5.069L78.7,38.517l-5.069-5.07-5.064,5.064-1.185-1.185,5.064-5.064L67.36,27.176l1.185-1.185,5.086,5.086,5.091-5.091Z" transform="translate(-67.375 -26)" fill="#000" />';
                        echo '</svg>';
                        echo '</a>' . PHP_EOL;
                    }
                    echo '</div>';
                }
                echo '</div>';

                // Advanced search panels
                echo '<div class="advanced-options' . ($show_advanced ? '' : ' hid') . '">';
                foreach ($panels as $panel) $panel->display();
                echo '<button type="submit" class="btn btn--primary">Update Search Results</button>';
                echo '</div>';

            }

            // Edit search controls
            if (!empty($saved_search) && !empty($_REQUEST['edit_search'])) {

                // $_REQUEST over-ride
                $saved_search['title'] = $_REQUEST['search_title'] ?: $saved_search['title'];
                $saved_search['frequency'] = $_REQUEST['frequency'] ?: $saved_search['frequency'];

                // Edit controls
                echo '<fieldset class="cols marB-md">'
                    . '<div class="fld col w1/2">'
                        . '<label>Search Title:</label>'
                        . '<div class="details">'
                            . '<input class="x12" name="search_title" value="' . htmlspecialchars($saved_search['title']) . '" required>'
                        . '</div>'
                    . '</div>'
                    . '<div class="fld col w1/2">'
                        . '<label>Update Frequency:</label>'
                        . '<div class="details">'
                            . '<select name="frequency" class="x12">'
                                . '<option value="never"' . ($saved_search['frequency'] == 'never' ? ' selected' : '') . '>Never</option>'
                                . '<option value="immediately"' . ($saved_search['frequency'] == 'immediately' ? ' selected' : '') . '>Immediately</option>'
                                . '<option value="daily"' . ($saved_search['frequency'] == 'daily' ? ' selected' : '') . '>Daily</option>'
                                . '<option value="weekly"' . (empty($saved_search['frequency']) || $saved_search['frequency'] == 'weekly' ? ' selected' : '') . '>Weekly</option>'
                                . '<option value="monthly"' . ($saved_search['frequency'] == 'monthly' ? ' selected' : '') . '>Monthly</option>'
                            . '</select>'
                        . '</div>'
                    . '</div>'
                . '</fieldset>';

            }

            // Hidden map search controls
            echo '<div id="map-draw-controls" '.($this->config['MapControlsVisible'] ? '' : 'class="hidden"').'>';
            echo '<div id="field-polygon">' . IDX_Panel::get('Polygon')->getMarkup() . '</div>';
            echo '<div id="field-radius">' . IDX_Panel::get('Radius')->getMarkup() . '</div>';
            echo '<div id="field-bounds">' . IDX_Panel::get('Bounds')->getMarkup() . '</div>';
            echo '</div>';

        ?>
    </form>
</div>
