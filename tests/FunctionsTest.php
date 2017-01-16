<?php
use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{

    /**
     * @dataProvider wProvider
     */
    public function testW($input, $expected)
    {
        $this->assertEquals($expected, w($input));
    }

    public function wProvider()
    {
        return [
            ['abacaxi mamao melancia', ['abacaxi', 'mamao', 'melancia']],
            ['abacaxi mamao 3 melancia', ['abacaxi', 'mamao', '3', 'melancia']]];
    }

}
