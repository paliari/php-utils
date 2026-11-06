<?php
use PHPUnit\Framework\TestCase;
use Paliari\Utils\VO\ParamsVO;

class AbstractVOTest extends TestCase
{

    public function testArrayAccessAndToArray()
    {
        $vo = new ParamsVO(['a' => 1, 'b' => ['c' => 2], 'n' => null]);
        $this->assertSame(1, $vo['a']);
        $this->assertInstanceOf(ParamsVO::class, $vo['b']);
        $this->assertSame(2, $vo['b']['c']);
        $this->assertTrue(isset($vo['a']));
        $this->assertFalse(isset($vo['n']));
        $this->assertSame(['a' => 1, 'b' => ['c' => 2]], $vo->toArray());
        $this->assertCount(2, $vo);
    }

    public function testOffsetSetAndUnset()
    {
        $vo        = new ParamsVO();
        $vo['x']   = 1;
        $vo[]      = 'y';
        unset($vo['x']);
        $this->assertSame([1 => 'y'], $vo->toArray());
    }

    public function testMissingOffsetReturnsNull()
    {
        $vo = new ParamsVO(['a' => 1]);
        $this->assertNull($vo['missing']);
    }

    public function testJson()
    {
        $vo = new ParamsVO(['a' => 1, 'b' => ['c' => 'd']]);
        $this->assertSame('{"a":1,"b":{"c":"d"}}', $vo->toJson());
        $this->assertSame('{"a":1,"b":{"c":"d"}}', (string)$vo);
        $this->assertSame('{"a":1,"b":{"c":"d"}}', json_encode($vo));
    }

    public function testToStringWithInvalidJson()
    {
        $vo = new ParamsVO(['a' => "\xB1\x31"]);
        $this->assertFalse($vo->toJson());
        $this->assertSame('', (string)$vo);
    }

}
