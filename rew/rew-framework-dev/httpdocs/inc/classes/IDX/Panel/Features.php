<?php

/**
 * Property Features
 * @package IDX_Panel
 */
class IDX_Panel_Features extends IDX_Panel implements IDX_Panel_Interface_Taggable
{

    const PANEL_TYPE = 'features';

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'Property Features';

    /**
     * @see IDX_Panel_Interface_Taggable::getTags
     * @return IDX_Search_Tag[]|NULL
     */
    public function getTags()
    {
        $tags = array();
        $values = $this->getValue();
        if (empty($values)) {
            return null;
        }
        foreach ($values as $feature => $value) {
            // Swimming Pool
            if ($feature === 'search_pool' && $value === 'Y') {
                $tags[] = new IDX_Search_Tag('Has Pool', array($feature => $value));
            }
            if ($feature === 'search_pool' && $value === 'N') {
                $tags[] = new IDX_Search_Tag('No Pool', array($feature => $value));
            }

            // Fireplace
            if ($feature === 'search_fireplace' && $value === 'Y') {
                $tags[] = new IDX_Search_Tag('Has Fireplace', array($feature => $value));
            }
            if ($feature === 'search_fireplace' && $value === 'N') {
                $tags[] = new IDX_Search_Tag('No Fireplace', array($feature => $value));
            }

            // Waterfront
            //if ($feature === 'search_waterfront' && $value === 'Y') $tags[] = new IDX_Search_Tag('Is Waterfront', array($feature => $value));
            //if ($feature === 'search_waterfront' && $value === 'N') $tags[] = new IDX_Search_Tag('Not Waterfront', array($feature => $value));
            // These tags get handled by IDX_Panel_Waterfront
        }
        if (empty($tags)) {
            return null;
        }
        return $tags;
    }

    /**
     * @see IDX_Panel::getValue
     * @return array
     */
    public function getValue()
    {
        return array_filter(array(
            'search_pool' => $_REQUEST['search_pool'],
            'search_fireplace' => $_REQUEST['search_fireplace'],
            'search_waterfront' => $_REQUEST['search_waterfront']
        ));
    }

    /**
     * @see IDX_Panel::getMarkup()
     */
    public function getMarkup()
    {

        // Field Wrap
        $html = '<div class="toggleset">';

        // Search Swimming Pool
        if ($this->checkField('HasPool')) {
            $html .= '<label><input type="checkbox" name="search_pool" value="Y"' . ($_REQUEST['search_pool'] == 'Y' ? ' checked' : '') . '> Swimming Pool</label>';
        }

        // Search Waterfront
        if ($this->checkField('IsWaterfront')) {
            $html .= '<label><input type="checkbox" name="search_waterfront" value="Y"' . ($_REQUEST['search_waterfront'] == 'Y' ? ' checked' : '') . '> Waterfront</label>';
        }

        // Search Fireplace
        if ($this->checkField('HasFireplace')) {
            $html .= '<label><input type="checkbox" name="search_fireplace" value="Y"' . ($_REQUEST['search_fireplace'] == 'Y' ? ' checked' : '') . '> Fireplace</label>';
        }

        // Close Wrap
        $html .= '</div>';

        // Return HTML
        return $html;
    }

    /**
     * @return array
     */
    public function typeSpecificJsonSerialize()
    {
        $featureFields = [
            'fields' => [],
            'value' => $this->getValue()
        ];

        if ($this->checkField('HasPool')) {
            $featureFields['fields']['search_pool'] = [
                'title' => 'Swimming Pool',
                'value' => 'Y'
                ];
        }

        if ($this->checkField('IsWaterfront')) {
            $featureFields['fields']['search_waterfront'] = [
                'title' => 'Waterfront',
                'value' => 'Y'
            ];
        }

        if ($this->checkField('HasFireplace')) {
            $featureFields['fields']['search_fireplace'] = [
                'title' => 'Fireplace',
                'value' => 'Y'
            ];
        }

        return array_merge(parent::typeSpecificJsonSerialize() , $featureFields);
    }
}
