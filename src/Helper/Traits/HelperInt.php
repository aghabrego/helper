<?php

namespace Weirdo\Helper\Traits;

use NumberFormatter;
use Weirdo\Helper\Traits\Helper;

/**
 * @license MIT
 * @package Weirdo\Helper
 */
trait HelperInt
{
    use Helper;

    /**
     * Crear un formateador de números
     * @param string $locale
     * @param integer $style
     * @return void
     */
    public function numfmtCreate(string $locale = 'es_US', int $style = NumberFormatter::DECIMAL)
    {
        return numfmt_create($locale, $style);
    }

    /**
     *  Analiza un número
     *
     * @param NumberFormatter $fmt
     * @param integer $decimal
     * @param string $decPoint
     * @param string $thousandsSep
     * @return integer
     */
    public function numfmtParse($fmt, $value, int $type = NumberFormatter::TYPE_DOUBLE)
    {
        return numfmt_parse($fmt, $value, $type);
    }

    /**
     * @param string|int $number
     * @param integer $decimal
     * @param string $decPoint
     * @param string $thousandsSep
     * @return integer
     */
    public function numberFormat($number, int $decimal = 2, string $decPoint = '.', string $thousandsSep = '')
    {
        if (is_string($number)) {
            $number = rtrim($this->strReplaceDeep(array(' ', '%'), array('', ''), $number));
            $fmt = $this->numfmtCreate();
            $number = $this->numfmtParse($fmt, $number);
        }

        return number_format($number, $decimal, $decPoint, $thousandsSep);
    }
}
