<?php

/**
 * Rooms
 * @package IDX_Panel
 */
class IDX_Panel_Rooms extends IDX_Panel
{
    const PANEL_TYPE = 'rooms';

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'Rooms';

    /**
     * Hide Title
     * @var string
     */
    protected $showTitle = false;

    /**
     * Input Name for # of Bedrooms
     * @var string
     */
    protected $inputNameBeds = 'minimum_bedrooms';

    /**
     * Input Name for # of Bathrooms
     * @var string
     */
    protected $inputNameBaths = 'minimum_bathrooms';

    /**
     * Placeholder text for bedroom field
     * @var string
     */
    protected $placeholderBeds = '-';

    /**
     * Placeholder text for bathroom field
     * @var string
     */
    protected $placeholderBaths = '-';

    /**
     * Available Options
     * @var array
     */
    protected $options = array(
        array('value' => 1, 'title' => '1'),
        array('value' => 2, 'title' => '2'),
        array('value' => 3, 'title' => '3'),
        array('value' => 4, 'title' => '4'),
        array('value' => 5, 'title' => '5'),
        array('value' => 6, 'title' => '6'),
        array('value' => 7, 'title' => '7'),
        array('value' => 8, 'title' => '8')
    );

    /**
     * @see IDX_Panel::__construct()
     */
    public function __construct($options = array())
    {
        $this->formatOptions = array($this, '_formatOption');
        if (isset($options['inputNameBeds'])) {
            $this->inputNameBeds    = $options['inputNameBeds'];
        }
        if (isset($options['inputNameBaths'])) {
            $this->inputNameBaths   = $options['inputNameBaths'];
        }
        if (isset($options['placeholderBeds'])) {
            $this->placeholderBeds  = $options['placeholderBeds'];
        }
        if (isset($options['placeholderBaths'])) {
            $this->placeholderBaths = $options['placeholderBaths'];
        }
        parent::__construct($options);
    }

    /**
     * @see IDX_Panel::getValue
     * @return array
     */
    public function getValue()
    {
        return array_filter(array(
            $this->inputNameBeds => $_REQUEST[$this->inputNameBeds],
            $this->inputNameBaths => $_REQUEST[$this->inputNameBaths]
        ));
    }

    /**
     * @see IDX_Panel::getMarkup()
     */
    public function getMarkup()
    {
        return '<div class="pair">'
            . '<div class="left">'
                . (!empty($this->titleElement) ? '<' . $this->titleElement . '>' : '')
                . 'Beds'
                . (!empty($this->titleElement) ? '</' . $this->titleElement . '>' : '')
                . '<div class="details' . (!empty($this->closed) ? ' ' . $this->hiddenClass : '') . '">'
                    . $this->getMinBeds()
                . '</div>'
            . '</div>'
            . '<div class="right">'
                . (!empty($this->titleElement) ? '<' . $this->titleElement . '>' : '')
                . 'Baths'
                . (!empty($this->titleElement) ? '</' . $this->titleElement . '>' : '')
                . '<div class="details' . (!empty($this->closed) ? ' ' . $this->hiddenClass : '') . '">'
                    . $this->getMinBaths()
                . '</div>'
            . '</div>'
        . '</div>';
    }

    /**
     * Get HTML Markup for Min. Bedrooms
     * @return string
     */
    public function getMinBeds()
    {
        $options = $this->formatOptions($this->getOptions());
        $inputName = $this->inputNameBeds;
        return '<select name="' . $inputName . '">'
            . '<option value="">' . $this->placeholderBeds . '</option>'
            . implode(array_map(function ($option) use ($inputName) {
                return '<option value="' . $option['value'] . '"' . ($_REQUEST[$inputName] == $option['value'] ? ' selected' : '') . '>' . $option['title'] . '</option>';
            }, $options))
        . '</select>';
    }

    /**
     * Get HTML Markup for Min. bathrooms
     * @return string
     */
    public function getMinBaths()
    {
        $options = $this->formatOptions($this->getOptions());
        $inputName = $this->inputNameBaths;
        return '<select name="' . $inputName . '">'
            . '<option value="">' . $this->placeholderBaths . '</option>'
            . implode(array_map(function ($option) use ($inputName) {
                return '<option value="' . $option['value'] . '"' . ($_REQUEST[$inputName] == $option['value'] ? ' selected' : '') . '>' . $option['title'] . '</option>';
            }, $options))
        . '</select>';
    }

    /**
     * Format options to include a plus sign to indicate minimum values
     * @param array $option
     * @return array
     */
    protected function _formatOption($option)
    {
        if (!empty($option['value'])) {
            $option['title'] = $option['title'] . '+';
        }
        return $option;
    }

    /**
     * Return Input Names
     * @return array
     */
    public function getInputs()
    {
        return [$this->inputNameBeds, $this->inputNameBaths];
    }

    /**
     * Return Placeholders
     * @return array
     */
    public function getPlaceholders()
    {
        return [$this->placeholderBeds, $this->placeholderBaths];
    }

    /**
     * @return array
     */
    public function typeSpecificJsonSerialize()
    {
        return array_merge(parent::typeSpecificJsonSerialize(), [
            'param_name' => [
                'beds' => $this->inputNameBeds,
                'baths' => $this->inputNameBaths
            ],
            'placeholder' => [
                'beds' => $this->placeholderBeds,
                'baths' => $this->placeholderBaths
            ]
        ]);
    }
}
