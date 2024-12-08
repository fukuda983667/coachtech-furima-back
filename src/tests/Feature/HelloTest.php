<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HelloTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testHello()
    {
        $this->assertTrue(true);

        $arr = [];
        $this->assertEmpty($arr);

        $txt = "Hello World";
        $this->assertEquals('Hello World', $txt);

        $n = random_int(0, 100);
        $this->assertLessThan(100, $n);
    }
}
