<?php

use Tests\TestCase;
use Weirdo\Helper\BaseClass;

class HelperIntTest extends TestCase
{
    public function testNumfmtCreate()
    {
        $base = new BaseClass;
        $format = $base->numfmtCreate('es_PA');
        $value = $base->numfmtParse($format, 110.43);
        $this->assertEquals(110.43, $value);
    }

    public function testNumberFormat()
    {
        $base = new BaseClass;
        $value = $base->numberFormat(110, 2, ',');
        $this->assertEquals('110,00', $value);
    }
}
