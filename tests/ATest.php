<?php
use PHPUnit\Framework\TestCase;
use Paliari\Utils\A;

class ATest extends TestCase
{

    /**
     * @dataProvider mergeProvider
     */
    public function testMerge($a1, $a2, $expected)
    {
        $this->assertEquals($expected, A::merge($a1, $a2));
    }

    /**
     * @dataProvider deepKeyProvider
     */
    public function testDeepKey($array)
    {
        $this->assertEquals(3, A::deepKey($array, 'a.b.c'));
        $this->assertNull(A::deepKey($array, 'a.b.c.d'));
    }

    /**
     * @dataProvider arrayFlattenProvider
     */
    public function testArrayFlatten($array, $expected)
    {
        $this->assertEquals($expected, A::flatten($array));
        $this->assertEmpty(A::flatten([]));
        $this->assertNotNull(A::flatten([]));
    }

    public function mergeProvider()
    {
        return [
            [['a' => '1'], ['b' => 2], ['a' => '1', 'b' => 2]],
            [['a' => ['a.b' => 1]], ['b' => 2], ['a' => ['a.b' => 1], 'b' => 2]],
            [['a' => 1], ['b' => ['b.1' => 2]], ['a' => 1, 'b' => ['b.1' => 2]]],
        ];
    }

    public function deepKeyProvider()
    {
        return [[['a' => ['b' => ['c' => 3]]], 3]];
    }

    public function arrayFlattenProvider()
    {
        return [[['a', 'b' => ['c' => ['d' => 3]], 'e'], ['a', 3, 'e']]];
    }

}
