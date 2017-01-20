<?php
use PHPUnit\Framework\TestCase;
use Paliari\Utils\Url;

class UrlTest extends TestCase
{

    /**
     * @param string $url
     * @param array  $expected
     * @param array  $query
     *
     * @dataProvider urlProvider
     */
    public function testMerge($url, $expected, $query = [])
    {
        $u = Url::parse($url);
        foreach ($expected as $k => $v) {
            $this->assertEquals($v, $u->$k, "Key: $k on $url, Expected: $v actual: {$u->$k}");
        }
        $this->assertEquals($query, $u->queryToArray(), "Error query in $url");
    }

    public function urlProvider()
    {
        return [
            [
                'https://user_name:password@domain.com:443/home/?p=1&q[a]=11&q[b]=22#/home?p=q',
                [
                    'scheme'   => 'https',
                    'host'     => 'domain.com',
                    'port'     => 443,
                    'user'     => 'user_name',
                    'pass'     => 'password',
                    'path'     => '/home/',
                    'query'    => 'p=1&q[a]=11&q[b]=22',
                    'fragment' => '/home?p=q',
                    'url'      => 'https://user_name:password@domain.com:443/home/?p=1&q[a]=11&q[b]=22#/home?p=q',
                ],
                [
                    'p' => '1',
                    'q' => ['a' => '11', 'b' => '22'],
                ],
            ],
            [
                'https://domain.com/home/?q[a]=11&q[b]=22#/home?p=q',
                [
                    'scheme'   => 'https',
                    'host'     => 'domain.com',
                    'port'     => null,
                    'user'     => null,
                    'pass'     => null,
                    'path'     => '/home/',
                    'query'    => 'q[a]=11&q[b]=22',
                    'fragment' => '/home?p=q',
                    'url'      => 'https://domain.com/home/?q[a]=11&q[b]=22#/home?p=q',
                ],
                [
                    'q' => ['a' => '11', 'b' => '22'],
                ],
            ],
            [
                'http://domain.com/home/',
                [
                    'scheme'   => 'http',
                    'host'     => 'domain.com',
                    'port'     => null,
                    'user'     => null,
                    'pass'     => null,
                    'path'     => '/home/',
                    'query'    => null,
                    'fragment' => null,
                    'url'      => 'http://domain.com/home/',
                ],
            ],
            [
                'http://domain.com',
                [
                    'scheme'   => 'http',
                    'host'     => 'domain.com',
                    'port'     => null,
                    'user'     => null,
                    'pass'     => null,
                    'path'     => null,
                    'query'    => null,
                    'fragment' => null,
                    'url'      => 'http://domain.com',
                ],
            ],
            [
                'file://domain.com',
                [
                    'scheme'   => 'file',
                    'host'     => 'domain.com',
                    'port'     => null,
                    'user'     => null,
                    'pass'     => null,
                    'path'     => null,
                    'query'    => null,
                    'fragment' => null,
                    'url'      => 'file://domain.com',
                ],
            ],
        ];
    }

}
