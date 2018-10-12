<?php

trait IDX_Panel_Trait_TypedMarkup
{
    /**
     * @see IDX_Panel::getMarkup()
     */
    public function getMarkup()
    {
        $method = array($this, 'get' . ucwords($this->markupStyle) . 'Markup');
        if (!is_callable($method)) {
            die(Log::halt('Cannot generate ' . $this->markupStyle . ' markup for ' . $this->getId()));
        }
        return $method();
    }
}
