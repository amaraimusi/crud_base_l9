<?php 
use PHPUnit\Framework\TestCase;
use CrudBase\Neko;

class NekoTest extends TestCase{
    
    protected $obj;
    protected function setUp() :void {
        $this->object = new Neko();
    }
    
    public function testAdd() {
        $this->assertEquals('赤猫はニャーンと吠えた', $this->object->bark('赤猫'));
    }

}