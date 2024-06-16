<?php

use Carbon\Carbon;
use Tests\TestCase;
use Weirdo\Helper\BaseClass;

class HelperCarbonTest extends TestCase
{
    public function testOriginalDateFormat()
    {
        $base = new BaseClass;
        $result = $base->getOriginalDateFormat('0000-00-00 00:00:00', 'd-m-Y h:i:s A');
        $this->assertNull($result);

        $result = $base->getOriginalDateFormat('30-11--0001 12:00:00 AM', 'd-m-Y h:i:s A');
        $this->assertNull($result);

        $result = $base->getOriginalDateFormat('2021-03-25 22:07:19', 'd-m-Y h:i:s A');
        $this->assertNotNull($result);
        $this->assertEquals($result, '25-03-2021 10:07:19 PM');

        $result = $base->getOriginalDateFormat('2021-03-25 22:07:19', 'Y-m-d');
        $this->assertNotNull($result);
        $this->assertEquals($result, '2021-03-25');
    }

    public function testCreateCarbonFormat()
    {
        $base = new BaseClass;
        $carbon = $base->createCarbonFormat('2021-03-25 22:07:19', 'Y-m-d');
        $this->assertInstanceOf(Carbon::class, $carbon);
    }

    public function testDifferencesInDays()
    {
        $base = new BaseClass;
        $days = $base->differencesInDays('2021-03-25 22:07:19', '2021-03-29 14:07:19');
        $this->assertEquals($days, 4);
    }

    public function testDifferencesInHours()
    {
        $base = new BaseClass;
        $hours = $base->differencesInHours('2021-03-25 22:07:19', '2021-03-25 23:10:19');
        $this->assertEquals($hours, 1);
    }

    public function testSpecificDateFormat()
    {
        $base = new BaseClass;
        $carbon = $base->getSpecificDateFormat('2021-03-25 22:07:19');
        $this->assertEquals($carbon, '25-mar.-2021 10:07:19');

        $carbon = $base->getSpecificDateFormat('2021-03-25', 'LL');
        $this->assertEquals($carbon, '25 de marzo de 2021');

        $carbon = $base->getSpecificDateFormat('2021-03-25', 'MMMM Y', 'Y-m-d');
        $this->assertEquals($carbon, 'marzo 2021');
    }

    public function testDifferencesInMinutes()
    {
        $base = new BaseClass;
        $minutes = $base->differencesInMinutes('2021-03-25 23:07:19', '2021-03-25 23:10:19');
        $this->assertEquals($minutes, 3);
    }

    public function testYesterday()
    {
        $base = new BaseClass;
        $carbon = $base->createCarbonFormat('2021-07-06 08:32:56', 'Y-m-d H:i:s');
        $carbonA = $base->getYesterday($carbon);
        $this->assertInstanceOf(Carbon::class, $carbonA);
        $this->assertEquals($carbonA->day, 5);

        $carbonB = $base->getYesterday('2021-07-06 08:32:56');
        $this->assertInstanceOf(Carbon::class, $carbonB);
        $this->assertEquals($carbonA->day, 5);
    }

    public function testGreaterThan()
    {
        $base = new BaseClass;
        $result = $base->greaterThan('2021-07-06 08:32:56', '2021-07-07 08:32:56', 'Y-m-d H:i:s');
        $this->assertIsBool($result);
        $this->assertEquals($result, false);
    }

    public function testEquals()
    {
        $base = new BaseClass;
        $result = $base->equals('2021-07-07 08:32:50', '2021-07-07 08:32:56', 'Y-m-d H:i:s');
        $this->assertIsBool($result);
        $this->assertEquals($result, false);

        $result = $base->equals('2021-07-07 08:32:56', '2021-07-07 08:32:56', 'Y-m-d H:i:s');
        $this->assertIsBool($result);
        $this->assertEquals($result, true);
    }

    public function testNotEquals()
    {
        $base = new BaseClass;
        $result = $base->notEquals('2021-07-07 08:32:50', '2021-07-07 08:32:56', 'Y-m-d H:i:s');
        $this->assertIsBool($result);
        $this->assertEquals($result, true);
    }

    public function testGreaterThanOrEquals()
    {
        $base = new BaseClass;
        $result = $base->greaterThanOrEquals('2021-07-07 08:32:50', '2021-07-07 08:32:56', 'Y-m-d H:i:s');
        $this->assertIsBool($result);
        $this->assertEquals($result, false);

        $result = $base->greaterThanOrEquals('2021-07-08 08:32:50', '2021-07-07 08:32:56', 'Y-m-d H:i:s');
        $this->assertIsBool($result);
        $this->assertEquals($result, true);
    }

    public function testCheckCarbonPHPFormat()
    {
        $base = new BaseClass;
        $valid = $base->validateDate('2023-03-02');
        $this->assertIsBool($valid);
        $this->assertEquals($valid, true);

        $valid = $base->validateDate('hola');
        $this->assertIsBool($valid);
        $this->assertEquals($valid, false);

        $valid = $base->validateDate('2023');
        $this->assertIsBool($valid);
        $this->assertEquals($valid, true);
    }

    public function testDayWeek()
    {
        $base = new BaseClass;
        $day = $base->getDayWeek('2024-01-01 00:00');
        $this->assertEquals('lu', $day);

        $day = $base->getDayWeek('2024-01-02 00:00');
        $this->assertEquals('ma', $day);

        $day = $base->getDayWeek('2024-01-03 00:00');
        $this->assertEquals('mi', $day);

        $day = $base->getDayWeek('2024-01-04 00:00');
        $this->assertEquals('ju', $day);

        $day = $base->getDayWeek('2024-01-05 00:00');
        $this->assertEquals('vi', $day);

        $day = $base->getDayWeek('2024-01-06 00:00');
        $this->assertEquals('sa', $day);

        $day = $base->getDayWeek('2024-01-07 00:00');
        $this->assertEquals('do', $day);
    }

    public function testWorkdayTime()
    {
        $base = new BaseClass;
        $time = $base->getWorkdayTime(86400);
        $this->assertIsInt($time);
    }
}
