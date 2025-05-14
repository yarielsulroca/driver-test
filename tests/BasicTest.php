<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class BasicTest extends TestCase
{
    public function testTrueIsTrue()
    {
        $this->assertTrue(true);
    }
    
    public function testOneEqualsOne()
    {
        $this->assertEquals(1, 1);
    }
}