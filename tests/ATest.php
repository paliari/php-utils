<?php
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Paliari\Utils\A;

class ATest extends TestCase
{

    #[DataProvider('mergeProvider')]
    public function testMerge($a1, $a2, $expected)
    {
        $this->assertEquals($expected, A::merge($a1, $a2));
    }

    #[DataProvider('deepKeyProvider')]
    public function testDeepKey($array)
    {
        $this->assertEquals(3, A::deepKey($array, 'a.b.c'));
        $this->assertNull(A::deepKey($array, 'a.b.c.d'));
    }

    public function testDeepKeyWithNullKey()
    {
        $this->assertSame(5, A::deepKey(['' => 5], null));
        $this->assertNull(A::deepKey(['a' => 1], null));
    }

    public function testSetDeepKey()
    {
        $array = ['a' => ['x' => 1]];
        A::setDeepKey($array, 'a.b.c', 2);
        $this->assertSame(['a' => ['x' => 1, 'b' => ['c' => 2]]], $array);
    }

    public function testSetDeepKeyWithNullKey()
    {
        $array = [];
        A::setDeepKey($array, null, 1);
        $this->assertSame(['' => 1], $array);
    }

    public function testGet()
    {
        $this->assertSame(1, A::get(['a' => 1], 'a'));
        $this->assertSame('d', A::get(['a' => 1], 'b', 'd'));
        $this->assertSame('d', A::get(['a' => null], 'a', 'd'));
        $this->assertSame('d', A::get(null, 'a', 'd'));
    }

    public function testGetWithNullKey()
    {
        $this->assertSame('x', A::get(['' => 'x'], null));
        $this->assertSame('d', A::get([], null, 'd'));
    }

    public function testSanitize()
    {
        $this->assertSame(['a' => 1], A::sanitize(['a' => 1, 'b' => 2], ['a']));
        $this->assertSame(['a' => 1, 'b' => 2], A::sanitize(['a' => 1, 'b' => 2], []));
    }

    #[DataProvider('arrayFlattenProvider')]
    public function testArrayFlatten($array, $expected)
    {
        $this->assertEquals($expected, A::flatten($array));
        $this->assertEmpty(A::flatten([]));
        $this->assertNotNull(A::flatten([]));
    }

    public static function mergeProvider()
    {
        return [
            [['a' => '1'], ['b' => 2], ['a' => '1', 'b' => 2]],
            [['a' => ['a.b' => 1]], ['b' => 2], ['a' => ['a.b' => 1], 'b' => 2]],
            [['a' => 1], ['b' => ['b.1' => 2]], ['a' => 1, 'b' => ['b.1' => 2]]],
        ];
    }

    public static function deepKeyProvider()
    {
        return [[['a' => ['b' => ['c' => 3]]], 3]];
    }

    public static function arrayFlattenProvider()
    {
        return [[['a', 'b' => ['c' => ['d' => 3]], 'e'], ['a', 3, 'e']]];
    }

}
