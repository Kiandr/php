<?php

/**
 * Price Range
 * @package IDX_Panel
 */
class IDX_Panel_Price extends IDX_Panel_Type_Range implements IDX_Panel_Interface_Taggable
{

    /**
     * Types to consider rentals
     * @var array
     */
    protected $rentalTypes = array('Rental', 'Rentals', 'Lease', 'Residential Lease', 'Commercial Lease', 'Residential Rental');

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'Price Range';

    /**
     * Input Name for Min. Price
     * @var string
     */
    protected $inputMinPrice = 'minimum_price';

    /**
     * Input Name for Max. Price
     * @var string
     */
    protected $inputMaxPrice = 'maximum_price';

    /**
     * Input Name for Min. Rent
     * @var string
     */
    protected $inputMinRent = 'minimum_rent';

    /**
     * Input Name for Max. Rent
     * @var string
     */
    protected $inputMaxRent = 'maximum_rent';

    /**
     * Placeholder Text for Min. Price
     * @var string
     */
    protected $placeholderMinPrice = 'Min';

    /**
     * Placeholder Text for Max. Price
     * @var string
     */
    protected $placeholderMaxPrice = 'Max';

    /**
     * Placeholder Text for Min. Rent
     * @var string
     */
    protected $placeholderMinRent = 'Min';

    /**
     * Placeholder Text for Max. Rent
     * @var string
     */
    protected $placeholderMaxRent = 'Max';

    /**
     * Force a specific range to be used instead of auto-detect
     * @var string|null
     */
    protected $forceRange;

    /**
     * @see IDX_Panel::__construct()
     */
    public function __construct($options = array())
    {
        $opts = array('inputMinPrice', 'inputMaxPrice', 'inputMinRent', 'inputMaxRent', 'placeholderMinPrice', 'placeholderMaxPrice', 'placeholderMinRent', 'placeholderMaxRent');
        foreach ($opts as $opt) {
            if (isset($options[$opt])) {
                $this->$opt = $options[$opt];
            }
        }
        parent::__construct($options);
    }

    /**
     * @see IDX_Panel_Interface_Taggable::getTags
     * @return IDX_Search_Tag[]|NULL
     */
    public function getTags()
    {
        $tags = array();
        foreach (array(
            array($this->inputMinPrice, $this->inputMaxPrice),
            array($this->inputMinRent, $this->inputMaxRent)
        ) as $range) {
            list ($minInput, $maxInput) = $range;
            $value = array($minInput => $_REQUEST[$minInput], $maxInput => $_REQUEST[$maxInput]);
            $minValue = $value[$minInput];
            $maxValue = $value[$maxInput];
            if (empty($minValue) && empty($maxValue)) {
                continue;
            }
            $minPrice = $minValue ? '$' . number_format($minValue) : false;
            $maxPrice = $maxValue ? '$' . number_format($maxValue) : false;
            if (!empty($minValue) && !empty($maxValue)) {
                $tags[] = new IDX_Search_Tag($minPrice . ' - ' . $maxPrice, $value);
            } else if (!empty($minValue)) {
                $tags[] = new IDX_Search_Tag('Over ' . $minPrice, array($minInput => $minValue));
            } else if (!empty($maxValue)) {
                $tags[] = new IDX_Search_Tag('Under ' . $maxPrice, array($maxInput => $maxValue));
            }
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
            $this->inputMinPrice => $_REQUEST[$this->inputMinPrice],
            $this->inputMaxPrice => $_REQUEST[$this->inputMaxPrice],
            $this->inputMinRent => $_REQUEST[$this->inputMinRent],
            $this->inputMaxRent => $_REQUEST[$this->inputMaxRent]
        ));
    }

    /**
     * Get Input Names
     * @return array
     */
    public function getInputs()
    {
        return array($this->inputMinPrice, $this->inputMaxPrice, $this->inputMinRent, $this->inputMaxRent);
    }

    /**
     * Get Sale Prices
     * @return array
     */
    public function getPriceOptions()
    {
        for ($price = 50000; $price <= 10000000; $price += 25000) {
            if (($price > 500000) && ($price <= 1000000)) {
                $price += 25000;
            }
            if (($price > 1000000) && ($price <= 3000000)) {
                $price += 75000;
            }
            if (($price > 3000000) && ($price <= 4000000)) {
                $price += 225000;
            }
            if (($price > 4000000) && ($price <= 10000000)) {
                $price += 475000;
            }
            $options[] = array('value' => $price, 'title' => '$' . number_format($price));
        }
        return $options;
    }

    /**
     * Get Rental Prices
     * @return array
     */
    public function getRentOptions()
    {
        for ($price = 500; $price <= 10000; $price += 100) {
            if (($price > 1000) && ($price <= 5000)) {
                $price += 150;
            }
            if (($price > 5000) && ($price <= 10000)) {
                $price += 400;
            }
            $options[] = array('value' => $price, 'title' => '$' . number_format($price));
        }
        return $options;
    }

    /**
     * @return string
     */
    public function getActiveRange()
    {
        $type = $_REQUEST['search_type'];
        $range = (is_string($type) && in_array($type, $this->rentalTypes)) ||
            (!empty($type) && is_array($type) && (array_diff($type, $this->rentalTypes) === [])) ? 'Rent' : 'Price';
        return $range;
    }

    /**
     * Get Options for Min. Range Input
     * @return array
     */
    public function getMinOptions()
    {
        $func = array($this, 'get' . ($this->forceRange ?: $this->getActiveRange()) . 'Options');

        return array_merge(array(
            array('value' => '', 'title' => $this->minOption)
        ), $func());
    }

    /**
     * Get Options for Max. Range Input
     * @return array
     */
    public function getMaxOptions()
    {
        $func = array($this, 'get' . ($this->forceRange ?: $this->getActiveRange()) . 'Options');

        return array_merge(array(
            array('value' => '', 'title' => $this->maxOption)
        ), $func());
    }

    /**
     * @see IDX_Panel::getMarkup()
     */
    public function getMarkup()
    {
        $html = '';

        $activeRange = $this->getActiveRange();
        foreach (array('Rent' => 'rent', 'Price' => 'sale') as $methodRange => $inputRange) {
            $this->forceRange = $methodRange;

            $html .= '<div class="' . $inputRange . ($methodRange != $activeRange ? ' ' . $this->hiddenClass : '') . '">';

            $this->minInput = $this->{'inputMin' . $methodRange};
            $this->maxInput = $this->{'inputMax' . $methodRange};
            $this->minOption = $this->{'placeholderMin' . $methodRange};
            $this->maxOption = $this->{'placeholderMax' . $methodRange};
            $html .= parent::getMarkup();

            $html .= '</div>';
        }

        return $html;
    }

    /**
     * Get HTML for Min. Price <select> Input
     * @param string $attrs Extra attributes for <select>
     * @return string
     */
    public function getMinPriceSelect($attrs = null)
    {
        $this->minInput = $this->inputMinPrice;
        $this->maxInput = $this->inputMaxPrice;
        $this->minOption = $this->placeholderMinPrice;
        $this->maxOption = $this->placeholderMaxPrice;
        return parent::getMinMarkup($attrs);
    }

    /**
     * Get HTML for Max. Price <select> Input
     * @param string $attrs Extra attributes for <select>
     * @return string
     */
    public function getMaxPriceSelect($attrs = null)
    {
        $this->minInput = $this->inputMinPrice;
        $this->maxInput = $this->inputMaxPrice;
        $this->minOption = $this->placeholderMinPrice;
        $this->maxOption = $this->placeholderMaxPrice;
        return parent::getMaxMarkup($attrs);
    }

    /**
     * Get HTML for Min. Rent <select> Input
     * @param string $attrs Extra attributes for <select>
     * @return string
     */
    public function getMinRentSelect($attrs = null)
    {
        $this->minInput = $this->inputMinRent;
        $this->maxInput = $this->inputMaxRent;
        $this->minOption = $this->placeholderMinRent;
        $this->maxOption = $this->placeholderMaxRent;
        return parent::getMinMarkup($attrs);
    }

    /**
     * Get HTML for Max. Rent <select> Input
     * @param string $attrs Extra attributes for <select>
     * @return string
     */
    public function getMaxRentSelect($attrs = null)
    {
        $this->minInput = $this->inputMinRent;
        $this->maxInput = $this->inputMaxRent;
        $this->minOption = $this->placeholderMinRent;
        $this->maxOption = $this->placeholderMaxRent;
        return parent::getMaxMarkup($attrs);
    }

    /**
     * {inheritDoc}
     * @param string $attrs
     * @return string
     */
    public function getMinMarkup($attrs = null)
    {
        $attrs = is_string($attrs) ? $attrs : '';
        $activeRange = $this->getActiveRange();
        if ($activeRange === 'Rent') {
            $attrs .= $this->minInput !== $this->inputMinRent ? ' disabled' : '';
        } else {
            $attrs .= $this->minInput !== $this->inputMinPrice ? ' disabled' : '';
        }
        return parent::getMinMarkup($attrs);
    }

    /**
     * {inheritDoc}
     * @param string $attrs
     * @return string
     */
    public function getMaxMarkup($attrs = null)
    {
        $attrs = is_string($attrs) ? $attrs : '';
        $activeRange = $this->getActiveRange();
        if ($activeRange === 'Rent') {
            $attrs .= $this->maxInput !== $this->inputMaxRent ? ' disabled' : '';
        } else {
            $attrs .= $this->maxInput !== $this->inputMaxPrice ? ' disabled' : '';
        }
        return parent::getMaxMarkup($attrs);
    }

    /**
     * Get the array of rental types
     * @return array
     */
    public function getRentalTypes()
    {
        return $this->rentalTypes;
    }

    /**
     * Get Placeholders
     * @return string[]
     */
    public function getPlaceholders()
    {
        return [
            $this->placeholderMinPrice,
            $this->placeholderMaxPrice,
            $this->placeholderMinRent,
            $this->placeholderMaxRent
        ];
    }

    public function typeSpecificJsonSerialize()
    {
        return array_merge(parent::typeSpecificJsonSerialize(),
            [
                'param_name' => [
                    'min' => [$this->inputMinPrice, $this->inputMinRent],
                    'max' => [$this->inputMaxPrice, $this->inputMaxRent]
                ],
                'options' => [
                    'min' => $this->getMinOptions(),
                    'max' => $this->getMaxOptions()
                ]
            ]);
    }
}
