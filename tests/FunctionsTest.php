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

    public static function wProvider()
    {
        return [
            ['abacaxi mamao melancia', ['abacaxi', 'mamao', 'melancia']],
            ['abacaxi mamao 3 melancia', ['abacaxi', 'mamao', '3', 'melancia']]];
    }

}
