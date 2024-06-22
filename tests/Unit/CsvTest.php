<?php

namespace Tests\Unit;

use Tests\TestCase;
use Weirdo\Helper\Support\CSV;

class CsvTest extends TestCase
{
    public function testScv()
    {
        /** @var string $path */
        $path = "tests/Unit/";
        /** @var \Weirdo\Helper\Support\CSV $class */
        $class = new CSV($path);
        /** @var array $result */
        $result = $class->ToArrayCsv("PermissionSeeder.csv");
        $this->assertIsArray($result);
    }
}
