<?php
use PHPUnit\Framework\TestCase;
use Paliari\Utils\Http\Request;

class RequestTest extends TestCase
{

    protected $server;

    protected function setUp(): void
    {
        $this->server = $_SERVER;
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->server;
    }

    public function testHeadersFromServer()
    {
        $_SERVER['HTTP_X_CUSTOM_HEADER'] = 'abc';
        $_SERVER['CONTENT_TYPE']         = 'application/json';
        $request                         = new Request();
        $this->assertSame('abc', $request->getHeader('x-custom-header'));
        $this->assertSame('abc', $request->getHeader('X_CUSTOM_HEADER'));
        $this->assertSame('application/json', $request->getHeader('Content-Type'));
        $this->assertNull($request->getHeader('X-Missing'));
    }

    public function testGetHeaderWithNullKey()
    {
        $this->assertNull((new Request())->getHeader(null));
    }

    public function testPathAndMethod()
    {
        $_SERVER['REQUEST_URI']    = '/users/1?x=2';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $request                   = new Request();
        $this->assertSame('/users/1', $request->getPath());
        $this->assertSame('POST', $request->getMethod());
    }

}
