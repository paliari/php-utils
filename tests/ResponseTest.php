<?php
use PHPUnit\Framework\TestCase;
use Paliari\Utils\Http\Response;

class ResponseTest extends TestCase
{

    protected function tearDown(): void
    {
        unset($GLOBALS['X-Time']);
    }

    public function testXTimeWithoutApp()
    {
        unset($GLOBALS['X-Time']);
        $ms = $this->response()->xTime();
        $this->assertGreaterThanOrEqual(0, $ms);
        $this->assertLessThan(3600 * 1000, $ms);
    }

    public function testXTimeWithApp()
    {
        $GLOBALS['X-Time'] = microtime(true) - 1;
        $ms                = $this->response()->xTime();
        $this->assertGreaterThanOrEqual(1000, $ms);
        $this->assertLessThan(2000, $ms);
    }

    protected function response()
    {
        return new class extends Response {
            public function xTime()
            {
                return parent::xTime();
            }
        };
    }

}
