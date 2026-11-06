<?php
use PHPUnit\Framework\TestCase;
use Paliari\Utils\CatchFatalError;
use Paliari\Utils\Logger;

class CatchFatalErrorTest extends TestCase
{

    protected $logs = [];

    protected function setUp(): void
    {
        Logger::setWriteCustom(function ($machine, $level, $message) {
            $this->logs[] = [$level, $message];
        });
    }

    protected function tearDown(): void
    {
        Logger::setWriteCustom(null);
        CatchFatalErrorStub::reset();
    }

    public function testNonFatalErrorKeepsOutput()
    {
        $this->assertSame('output', CatchFatalErrorStub::handleOutput('output', null));
        $this->assertSame('output', CatchFatalErrorStub::handleOutput('output', ['type' => E_WARNING, 'message' => 'w']));
        $this->assertSame([], $this->logs);
    }

    public function testFatalErrorReturnsDefault()
    {
        $e   = ['type' => E_ERROR, 'message' => 'boom'];
        $ret = CatchFatalErrorStub::handleOutput('output', $e);
        $this->assertSame(json_encode(['error' => $e, 'message' => 'Fatal Error!']), $ret);
        $this->assertSame(Logger::CRITICAL, $this->logs[0][0]);
    }

    public function testFatalErrorWithCustomHandler()
    {
        CatchFatalErrorStub::setHandler(function ($e) {
            return 'custom ' . $e['message'];
        });
        $this->assertSame('custom boom', CatchFatalErrorStub::handleOutput('output', ['type' => E_ERROR, 'message' => 'boom']));
    }

    public function testCustomHandlerNonStringResultIsCastToString()
    {
        CatchFatalErrorStub::setHandler(function () {
            return 123;
        });
        $this->assertSame('123', CatchFatalErrorStub::handleOutput('output', ['type' => E_ERROR, 'message' => 'boom']));
    }

    public function testUnencodableErrorKeepsOutput()
    {
        $e = ['type' => E_ERROR, 'message' => "\xB1\x31"];
        $this->assertSame('output', CatchFatalErrorStub::handleOutput('output', $e));
    }

}

class CatchFatalErrorStub extends CatchFatalError
{
    public static function handleOutput($output, $e)
    {
        return parent::handleOutput($output, $e);
    }

    public static function setHandler($handler)
    {
        static::$handler = $handler;
    }

    public static function reset()
    {
        static::$handler = null;
    }

    public static function defaultErrorHandler($e)
    {
        return json_encode(['error' => $e, 'message' => 'Fatal Error!']);
    }
}
