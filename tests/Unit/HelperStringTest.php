<?php

use Tests\TestCase;
use Weirdo\Helper\BaseClass;

class HelperStringTest extends TestCase
{
    public function testAfter()
    {
        $base = new BaseClass;
        $result = $base->after('This is my name', 'is');
        $this->assertEquals($result, ' is my name');
    }

    public function testAfterLast()
    {
        $base = new BaseClass;
        $result = $base->afterLast('This is my name', 'is');
        $this->assertEquals($result, ' my name');

        $value = '/var/www/html/storage/app/public/sale/2021-12/O3DleFABwcDWx89Twq2TSOpwqNzmLuQl7KE1D04Q.jpg';
        $result = $base->afterLast($value, '/');
        $this->assertEquals($result, 'O3DleFABwcDWx89Twq2TSOpwqNzmLuQl7KE1D04Q.jpg');
    }

    public function testBefore()
    {
        $base = new BaseClass;
        $result = $base->before('This is is my name', 'is');
        $this->assertEquals($result, 'Th');
    }

    public function testBeforeLast()
    {
        $base = new BaseClass;
        $result = $base->beforeLast('This is is', 'is');
        $this->assertEquals($result, 'This is ');
    }

    public function testUpper()
    {
        $base = new BaseClass;
        $result = $base->upper('This is is');
        $this->assertEquals($result, 'THIS IS IS');
    }

    public function testUcfirst()
    {
        $base = new BaseClass;
        $result = $base->ucfirst('this is is');
        $this->assertEquals($result, 'This is is');
    }

    public function testLofirst()
    {
        $base = new BaseClass;
        $result = $base->lofirst('This is is');
        $this->assertEquals($result, 'this is is');
    }

    public function testLower()
    {
        $base = new BaseClass;
        $result = $base->lower('THIS IS IS');
        $this->assertEquals($result, 'this is is');
    }

    public function testEncrypt()
    {
        $base = new BaseClass;
        $result = $base->encryptDecrypt('6214-1994', 'encrypt');
        $this->assertIsNotBool($result);
    }

    public function testDecrypt()
    {
        $base = new BaseClass;
        $result = $base->encryptDecrypt('On4WFmEN1LyKGwm1fRwqjw==', 'decrypt');
        $this->assertIsNotBool($result);
        $this->assertEquals($result, '6214-1994');
    }

    public function testGetFullName()
    {
        $request = [
            'primer_nombre' => 'Angel',
            'segundo_nombre' => 'Gabriel',
            'primer_apellido' => 'Hidalgo',
            'segundo_apellido' => 'Abrego',
        ];
        $base = new BaseClass;
        $fullName = $base->getFullName((object) $request);
        $this->assertEquals($fullName, 'Angel Gabriel Hidalgo Abrego');
    }

    public function testimplodeArrayWithKey()
    {
        $base = new BaseClass;
        $id = $base->implodeArray([
            [
                'nombre' => 'Isabel',
            ],
            [
                'nombre' => 'Florencia',
            ]
        ], '-');
        $this->assertEquals($id, 'Isabel-Florencia');
    }

    public function testImplodeArray()
    {
        $base = new BaseClass;
        $id = $base->implodeArray(['9', '', '734', '1000'], '-');
        $this->assertEquals($id, '9-734-1000');
    }

    public function testGetWordFromTextWith()
    {
        $base = new BaseClass;
        $value = $base->getWordFromTextWith('97341000', 3);
        $this->assertEquals($value, '4');
    }

    public function testPanamaIDFormat()
    {
        $base = new BaseClass;
        $id = $base->panamaIDFormat('97341000', '', '-');
        $this->assertNotNull($id);
        $this->assertEquals($id, '9-734-1000');

        $base = new BaseClass;
        $id = $base->panamaID('88409', 3, 6);
        $this->assertCount(4, $id);

        $base = new BaseClass;
        $id = $base->panamaIDFormat('88409', '', '-');
        $this->assertNotNull($id);
        $this->assertEquals($id, '8-840-9');
    }

    public function testConvertCertificateToText()
    {
        $base = new BaseClass;
        $id = $base->convertCertificateToText('97341000');
        $this->assertNotNull($id);
        $this->assertEquals('nueve-setecientos treinta y cuatro-mil', $id);
    }

    public function testFormatToDollars()
    {
        $base = new BaseClass;
        $format = $base->formatToDollars(16020.00, 0);
        $this->assertNotNull($format);
        $this->assertEquals('dieciséis mil veinte dólares con cero centavos', $format);
    }

    public function testMoneyFormat()
    {
        $base = new BaseClass;
        $result = $base->getMoneyFormat(16020.00);
        $this->assertEquals('$16,020.00', $result);

        $result = $base->getMoneyFormat(16020.00, NumberFormatter::DECIMAL);
        $this->assertEquals('16,020', $result);

        $result = $base->getMoneyFormat('-', NumberFormatter::DECIMAL);
        $this->assertEquals('0', $result);
    }

    public function testConvertAmountinDigittoWords()
    {
        $base = new BaseClass;
        $result = $base->convertAmountinDigittoWords('-');
        $this->assertEquals($result, 'cero');

        $result = $base->convertAmountinDigittoWords('');
        $this->assertEquals($result, 'cero');

        $result = $base->convertAmountinDigittoWords(null);
        $this->assertEquals($result, 'cero');

        $result = $base->convertAmountinDigittoWords(' ');
        $this->assertEquals($result, 'cero');

        $result = $base->convertAmountinDigittoWords(89);
        $this->assertEquals($result, 'ochenta y nueve');
    }

    public function testFirstSubstringDelimiter()
    {
        $base = new BaseClass;
        $text = 'public/sale/signature/1/2021-04/kgrldlxgra.jpg';
        $result = $base->getFirstSubstringDelimiter($text, '/', 5, true);
        $this->assertEquals($result, '2021-04');
    }

    public function testCleanSpecialCharacters()
    {
        $base = new BaseClass;
        $result = $base->cleanSpecialCharacters('Ángel Hidalgo');
        $this->assertEquals('AngelHidalgo', $result);
    }

    public function testPregReplaceString()
    {
        $base = new BaseClass;
        $result = $base->pregReplaceString('Ángel    Hidalgo', ' ');
        $this->assertEquals('Ángel Hidalgo', $result);

        $result = $base->pregReplaceString('Ángel   Hidalgo', '');
        $this->assertEquals('ÁngelHidalgo', $result);
    }

    public function testFirstSubstr()
    {
        $base = new BaseClass;
        $result = $base->getFirstSubstr('Ángel Hidalgo', 'H', 1, false);
        $this->assertEquals('idalgo', $result);

        $result = $base->getFirstSubstr('Ángel Hidalgo', 'H', 1, true);
        $this->assertEquals('Angel ', $result);

        $result = $base->getFirstSubstr('angel Gabriel', 'a', 2, false);
        $this->assertEquals('briel', $result);

        $result = $base->getFirstSubstr('angel Gabriel', 'a', 2, true);
        $this->assertEquals('angel G', $result);
    }

    public function testSubstr()
    {
        $base = new BaseClass;
        $result = $base->getSubstr('Ángel Hidalgo', -3, 1);
        $this->assertEquals('l', $result);

        $result = $base->getSubstr('Ángel Hidalgo', -1);
        $this->assertEquals('o', $result);

        $result = $base->getSubstr('Ángel Hidalgo', -2);
        $this->assertEquals('go', $result);
    }

    public function testTheLargestCell()
    {
        $base = new BaseClass;
        $columns = ['id' => 'Id', 'name' => 'Nombre'];
        $result = $base->getTheLargestCell($columns, 1);
        $this->assertEquals('name', $result);
    }

    public function testCamel()
    {
        $base = new BaseClass;
        $result = $base->camel('set_Fecha_Estado_solicitud_bancaria_Attribute');
        $this->assertEquals($result, 'setFechaEstadoSolicitudBancariaAttribute');
    }

    public function testStorageAsset()
    {
        $result = storage_asset('tests/Unit/contact.vcard');
        $this->assertNotEquals(false, $result);
        $this->assertEquals($result, base_path('tests/Unit/contact.vcard'));

        $result = storage_asset('tests/Unit/contact.vcard', true);
        $this->assertNotEquals(false, $result);
    }
}
