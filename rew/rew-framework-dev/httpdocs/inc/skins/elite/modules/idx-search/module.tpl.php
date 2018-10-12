<?php $className = $this->config('className') ?: false; ?>
<?php $buttonText = $this->config('button') ?: 'Search'; ?>
<?php $pricePlaceholder = $this->config('price.placeholder') ?: 'Min - Max'; ?>

<form class="uk-form idx-search js-idx-search-header" action="<?= htmlspecialchars(Settings::getInstance()->SETTINGS['URL_IDX_SEARCH']); ?>">
    <?php foreach ($hiddenPanels as $panel) { ?>
        <?php if (!in_array($panel->getID(), array('location', 'price'))) $panel->display(); ?>
    <?php } ?>
    <?php if ($this->config('searchbar')) { ?>

        <input type="hidden" name="refine" value="true">
        <?php if ($_REQUEST['saved_search_id']) { ?>
            <input type="hidden" name="saved_search_id" value="<?= Format::htmlspecialchars($_REQUEST['saved_search_id']); ?>">
        <?php } ?>
        <?php if ($_REQUEST['saved_search_id']) { ?>
            <input type="hidden" name="edit_search" value="<?= Format::htmlspecialchars($_REQUEST['edit_search']); ?>">
        <?php } ?>
        <?php if ($_REQUEST['lead_id']) { ?>
            <input type="hidden" name="lead_id" value="<?= Format::htmlspecialchars($_REQUEST['lead_id']); ?>">
        <?php } ?>
        <?php if ($_REQUEST['create_search']) { ?>
            <input type="hidden" name="create_search" value="<?= Format::htmlspecialchars($_REQUEST['create_search']); ?>">
        <?php } ?>

        <!-- Map Info -->
        <input type="hidden" name="map[longitude]" value="<?=htmlspecialchars($_REQUEST['map']['longitude']); ?>">
        <input type="hidden" name="map[latitude]" value="<?=htmlspecialchars($_REQUEST['map']['latitude']); ?>">
        <input type="hidden" name="map[zoom]" value="<?=htmlspecialchars($_REQUEST['map']['zoom']); ?>">

        <!-- Map Tools -->
        <input type="hidden" name="map[polygon]" value="<?=htmlspecialchars($_REQUEST['map']['polygon']); ?>">
        <input type="hidden" name="map[radius]" value="<?=htmlspecialchars($_REQUEST['map']['radius']); ?>">
        <input type="hidden" name="map[bounds]" value="<?=(!empty($_REQUEST['bounds']) ? 1 : 0); ?>">
        <input type="hidden" name="map[ne]" value="<?=htmlspecialchars($_REQUEST['map']['ne']); ?>">
        <input type="hidden" name="map[sw]" value="<?=htmlspecialchars($_REQUEST['map']['sw']); ?>">
        <div class="">
            <div class="header-search">       
                <div class="uk-grid uk-grid-collapse">
                    <div class="uk-width-1-1 uk-width-large-6-10 uk-width-xlarge-6-10 uk-position-relative">
                        <?= IDX_Panel::get('Location', array('inputClass' => 'uk-width-1-1 autocomplete location'))->getMarkup(); ?>  
                        <button class="uk-button uk-position-top-right uk-hidden-large"><i class="uk-icon-search uk-hidden-large"></i></button>                          
                    </div>
                    <div class="uk-width-large-3-10 uk-width-xlarge-3-10 idx-price uk-position-relative uk-visible-large">                           
                        <span class="label" data-placeholder="<?= Format::htmlspecialchars($pricePlaceholder); ?>"><?= Format::htmlspecialchars($pricePlaceholder); ?></span>
                        <button class="uk-button uk-position-top-right">
                            <?= Format::htmlspecialchars($buttonText); ?>
                        </button>                          
                        <div class="uk-hidden wrapper">
                            <?= IDX_Panel::get('Price', array('inputClass' => 'uk-width-1-1', 'inputHiddenClass' => 'uk-hidden'))->getMarkup(); ?>
                        </div>
                    </div>
                    <div class="uk-width-large-1-10 uk-width-xlarge-1-10 uk-visible-large">
                        <a class="search-filter sf-a js-advanced-search-trigger js-advanced-search-main-trigger uk-float-right">Filter <i class="uk-icon-angle-up"></i></a>
                    </div>
                </div>               
            </div>
        </div>
        <?php } ?>
    </form>
