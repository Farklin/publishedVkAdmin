<?php

namespace Tests\Unit;

use App\Services\Parsing\ParsingService;
use Tests\TestCase; 

class ParsingTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        $test = new ParsingService();
        fwrite(STDERR, print_r($test->start(), TRUE));

    }
}
