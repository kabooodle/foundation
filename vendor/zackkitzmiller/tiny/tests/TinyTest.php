<?php

use ZackKitzmiller\Tiny;

class TinyTest extends PHPUnit_Framework_TestCase {

    protected $tiny = null;

    public function setUp() {
        $this->tiny = new Tiny('5SX0TEjkR1mLOw8Gvq2VyJxIFhgCAYidrclDWaM3so9bfzZpuUenKtP74QNH6B');
    }

    public function testToTiny() {
        $converted = $this->tiny->to(5);
        $this->assertEquals('E', $converted);
    }

    public function testFromTiny() {
        $reversed = $this->tiny->from('E');
        $this->assertEquals(5, $reversed);
    }

    public function testReversingRandomInt() {
        for ($i = 0; $i <= 100; $i++) {
            $this->assertEquals($this->tiny->from($this->tiny->to($i)), $i);
        }
    }

    public function testGenerateRandomSetsWork() {
        for ($i = 0; $i <= 1000; $i++) {
            $tiny = new Tiny(Tiny::generate_set());
            $this->assertEquals($tiny->from($tiny->to($i)), $i);
        }
    }

    public function testGenerateSetUnique() {
        $set = Tiny::generate_set();
        $set_parts = str_split($set);
        $used = array();
        foreach ($set_parts as $char) {
            $this->assertArrayNotHasKey($char, $used);
            $used[$char] = $char;
        }
    }
}
