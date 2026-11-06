<?php
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Paliari\Utils\Bump;

class BumpTest extends TestCase
{

    protected $file;

    protected function setUp(): void
    {
        $this->file = tempnam(sys_get_temp_dir(), 'bump') . '.json';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->file)) {
            unlink($this->file);
        }
    }

    #[DataProvider('versionProvider')]
    public function testVersion($current, $type, $expected)
    {
        $this->writeComposer($current);
        $bump = new Bump($this->file);
        $this->assertSame($expected, $bump->version($type));
        $this->assertSame($expected, $this->readComposer()['version']);
        $this->assertSame('paliari/test', $this->readComposer()['name']);
    }

    public static function versionProvider()
    {
        return [
            ['1.2.3', 'patch', '1.2.4'],
            ['1.2.3', '', '1.2.4'],
            ['1.2.3', 'minor', '1.3.0'],
            ['1.2.3', 'major', '2.0.0'],
            ['1.2.3-4', 'patch', '1.2.4'],
            ['1.2.3', 'prerelease', '1.2.3-1'],
            ['1.2.3-1', 'prerelease', '1.2.3-2'],
            ['1.2.3-9', 'prerelease', '1.2.3-10'],
            ['1.2.3-beta', 'prerelease', '1.2.3-betb'],
            ['1.2.3', '3.1.0-rc1', '3.1.0-rc1'],
            [null, 'patch', '0.0.1'],
        ];
    }

    public function testInvalidVersion()
    {
        $this->writeComposer('1.0.0');
        $this->expectException(Exception::class);
        (new Bump($this->file))->version('invalid');
    }

    protected function writeComposer($version)
    {
        $content = ['name' => 'paliari/test'];
        if (null !== $version) {
            $content['version'] = $version;
        }
        file_put_contents($this->file, json_encode($content));
    }

    protected function readComposer()
    {
        return json_decode(file_get_contents($this->file), true);
    }

}
