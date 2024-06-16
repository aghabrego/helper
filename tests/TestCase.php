<?php

namespace Tests;

use Symfony\Component\VarDumper\VarDumper;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @var \Illuminate\Foundation\Application
     */
    public $app;

    public function setUp(): void
    {
        parent::setUp();

        $this->app = $this->createApplication();
    }

    /**
     * @param mixed $value
     * @return void
     */
    public function dd($value)
    {
        VarDumper::dump($value);

        exit(1);
    }
}
