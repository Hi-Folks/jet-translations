<?php

namespace HiFolks\JetTranslations\Tests;

use Orchestra\Testbench\TestCase;
use HiFolks\JetTranslations\JetTranslationsServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [JetTranslationsServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
