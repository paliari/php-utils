<?php
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{

    #[DataProvider('wProvider')]
    public function testW($input, $expected)
    {
        $this->assertEquals($expected, w($input));
    }

    public function testWWithNull()
    {
        $this->assertSame([''], w(null));
    }

    public function testWWithDelimiter()
    {
        $this->assertSame(['a', 'b'], w('a,b', ','));
    }

    public static function wProvider()
    {
        return [
            ['abacaxi mamao melancia', ['abacaxi', 'mamao', 'melancia']],
            ['abacaxi mamao 3 melancia', ['abacaxi', 'mamao', '3', 'melancia']]];
    }

}
