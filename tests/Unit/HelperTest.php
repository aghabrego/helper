<?php

namespace Tests\Unit;

use Tests\TestCase;
use ReflectionMethod;
use Weirdo\Helper\BaseClass;

class HelperTest extends TestCase
{
    public function testConvertHexToRGB()
    {
        $base = new BaseClass;
        $coloresRGB = $base->convertHexToRGB("/blue/i");
        $this->assertIsArray($coloresRGB);
    }

    public function testColorNamesBy()
    {
        $base = new BaseClass;
        $colores = $base->getColorsNames("/blue/i");
        $this->assertIsArray($colores);
    }

    public function testColorNames()
    {
        $base = new BaseClass;
        $colores = $base->getColorsNames();
        $this->assertIsArray($colores);
    }

    public function testReflectionMethod()
    {
        $reflection = BaseClass::reflectionMethod('hasMethodExist');
        $this->assertInstanceOf(ReflectionMethod::class, $reflection);
    }

    public function testCheckVariable()
    {
        $base = new BaseClass();
        $result = $base->checkVariable('test');
        $this->assertEquals($result, true);
    }

    public function testArrayInsert()
    {
        $array = ["9-734-1672", "9", "734", "1672"];
        array_insert($array, 1, "");
        $this->assertEquals($array, ["9-734-1672", "", "9", "734", "1672"]);
    }

    public function testStreamContextCreate()
    {
        $base = new BaseClass;
        $uri = 'https://getsamplefiles.com/download/ogg/sample-1.ogg';
        $result = $base->streamContextCreate($uri, null, 'GET', 'https');
        $this->assertIsString($result);
    }

    public function testCheckPhonePanama()
    {
        $base = new BaseClass();
        $result = $base->checkPhonePanama('62141994');
        $this->assertEquals($result, '+50762141994');
    }

    public function testValidCellPhoneFormatPanama()
    {
        $base = new BaseClass();
        $result = $base->getValidCellPhoneFormatPanama('62141994');
        $this->assertEquals($result, '62141994');

        $result = $base->getValidCellPhoneFormatPanama('+50762141994');
        $this->assertEquals($result, '62141994');

        $result = $base->getValidCellPhoneFormatPanama('+5079982425');
        $this->assertEquals($result, '9982425');

        $result = $base->getValidCellPhoneFormatPanama('whatsapp:+50762141994');
        $this->assertEquals($result, '62141994');

        $result = $base->getValidCellPhoneFormatPanama('');
        $this->assertNull($result);

        $result = $base->getValidCellPhoneFormatPanama(null);
        $this->assertNull($result);

        $result = $base->getValidCellPhoneFormatPanama('angel');
        $this->assertNull($result);

        $result = $base->getValidCellPhoneFormatPanama('+5075116007');
        $this->assertEquals($result, '5116007');

        $codecResult = $base->getRegionCodeForNumber('+5075116007');
        $result = $base->getValidCellPhoneFormat('5116007', $codecResult);
    }

    public function testPhoneNumberFormat()
    {
        $base = new BaseClass();
        $result = $base->getPhoneNumberFormat('62141994', 'PA');
        $this->assertEquals($result, '+50762141994');

        $result = $base->getPhoneNumberFormat('+50762141994', 'PA');
        $this->assertEquals($result, '+50762141994');

        $codecResult = $base->getRegionCodeForNumber('+50762141994');
        $this->assertEquals($codecResult, 'PA');

        $result = $base->getPhoneNumberFormat('+14842918963', 'US');
        $this->assertEquals($result, '+14842918963');

        $codecResult = $base->getRegionCodeForNumber('+14842918963');
        $this->assertEquals($codecResult, 'US');

        $result = $base->getPhoneNumberFormat('+14842918963', $codecResult);
        $this->assertEquals($result, '+14842918963');
    }

    public function testFileDetail()
    {
        $base = new BaseClass;
        $result = $base->getFileDetail('https://getsamplefiles.com/download/ogg/sample-1.ogg');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('wrapper_data', $result);
        $data = array_get($result, 'wrapper_data');
        $result = $base->findFirstMatch($data, "/Content-Type/i");
        $this->assertNotNull($result);
    }

    public function testFilterVar()
    {
        $base = new BaseClass;
        $result = $base->filterVar("1", FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        $this->assertNotNull($result);
        $this->assertIsInt($result);

        $result = $base->filterVar("1", FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        $this->assertNotNull($result);
        $this->assertIsBool($result);
    }

    /**
     * 
     * Resultado Ejemplos:
     * 
     *  array:2 [
     *      "sector" => "Gobierno"
     *      "valor" => 0.55
     *  ]
     */
    public function testJsonDecode()
    {
        $base = new BaseClass;
        $value = "'{\"sector\":\"Gobierno\";\"valor\":0.55}'";

        $result = $base->getJsonDecode(str_replace(";", ",", $value), true);
        $this->assertIsArray($result);
    }

    public function testValueRequestLogTwilio()
    {
        $data = [
            "To" => "whatsapp:+5078339534",
            "From" => "whatsapp:+50762141994",
            "MessageSid" => "SM3c5c696cd4b176a001f695d86db1f5de",
        ];          
        $base = new BaseClass;
        $result = $base->getValueRequest($data, 'from', 'From');

        $result = $base->getValueRequest($data, 'from');
        $this->assertNull($result);
    }

    public function testDuration()
    {
        $path = "https://getsamplefiles.com/download/ogg/sample-1.ogg";
        $base = new BaseClass;
        $time = $base->getDurationInSeconds($path);
        $this->assertNotNull($time);
        $this->assertEquals($time, 96.06639455782313);
    }

    public function testCreateRouteAccordingToSystem()
    {
        $base = new BaseClass;
        $path = $base->createRouteColorsAccordingToSystem();
        $this->assertNotEquals($path, false);
    }

    public function testSystemRoute()
    {
        $base = new BaseClass;
        $dir = $base->getDirname(__DIR__, 2);
        $originalPath = $base->createRouteColorsAccordingToSystem();
        $resultPath = $base->getSystemRoute($dir, "/src/config/color-names.json");
        $this->assertEquals($originalPath, $resultPath);
    }

    public function testProperPhoneFormat()
    {
        $base = new BaseClass();
        $result = $base->getProperPhoneFormat('+18509403447');
        $this->assertEquals($result, '8509403447');

        $result = $base->getProperPhoneFormat('+18509403445');
        $this->assertEmpty($result);

        $result = $base->getProperPhoneFormat('+507 202 1767');
        $this->assertEquals($result, '2021767');

        $result = $base->getProperPhoneFormat('+507 833 9534');
        $this->assertEquals($result, '8339534');

        $result = $base->getProperPhoneFormat('+507 6214 1994');
        $this->assertEquals($result, '62141994');

        $result = $base->getProperPhoneFormat('+50762141994');
        $this->assertEquals($result, '62141994');
    }
}
