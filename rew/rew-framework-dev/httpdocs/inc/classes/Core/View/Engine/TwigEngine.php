<?php


namespace REW\Core\View\Engine;

use REW\View\Engine\TwigEngine as BaseTwigEngine;
use REW\View\Interfaces\LoaderInterface;
use REW\Core\Interfaces\FormatInterface;
use Twig_SimpleFunction;

/***
 * Extension of TwigEngine
 * @package REW\Backend\View\Engine
 */
class TwigEngine extends BaseTwigEngine {

    /**
     * @var \REW\Core\Interfaces\FormatInterface
     */
    protected $format;

    /**
     * @param FormatInterface $format
     * @param LoaderInterface $loader
     * @param array $envOpts
     */
    public function __construct (FormatInterface $format, LoaderInterface $loader, $envOpts = []) {
        parent::__construct($loader, $envOpts);
        $this->twig->addFunction(new Twig_SimpleFunction('thumbUrl', [$this, 'thumbUrl']));
        $this->twig->addFunction(new Twig_SimpleFunction('number', [$this, 'number']));
        $this->format = $format;
    }

    /**
     * @param string $url
     * @param string $size
     * @return string
     */
    public function thumbUrl ($url, $size) {
        return $this->format->thumbUrl($url, $size);
    }

    /**
     * @param mixed $value
     * @param int $decimals
     * @param string $dec_point
     * @param string $thousands_sep
     * @return string
     */
    public function number ($value, $decimals = 0, $dec_point = '.', $thousands_sep = ',') {
        return $this->format->number($value, $decimals, $dec_point, $thousands_sep);
    }

}
