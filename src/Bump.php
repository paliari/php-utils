<?php
namespace Paliari\Utils;

use Exception;

class Bump
{
    protected $format  = '/(\d+)\.(\d+)\.(\d+)-?(\w{0,})/';
    protected $file    = '';
    protected $content = [];

    protected $major      = 0;
    protected $minor      = 0;
    protected $patch      = 0;
    protected $prerelease = 0;

    /**
     * @param string $composer_file
     */
    public function __construct($composer_file = '')
    {
        $this->file = $composer_file ?: getcwd() . DIRECTORY_SEPARATOR . 'composer.json';
        $this->read();
    }

    /**
     * Sem parametro executa "bin/bump -v patch"
     * Pode passar a version "bin/bump --version 1.1.0"
     * Ou passar o tipo desejado: "bin/bump --version minor"
     *
     * MAJOR ("major") version when you make incompatible API changes
     * MINOR ("minor") version when you add functionality in a backwards-compatible manner
     * PATCH ("patch") version when you make backwards-compatible bug fixes.
     * PRERELEASE ("prerelease") a pre-release version
     *
     * major: 1.0.0
     * minor: 0.1.0
     * patch: 0.0.2
     * prerelease: 0.0.1-2
     *
     * @param string $version
     *
     * @return string
     *
     * @throws Exception
     */
    public function version($version)
    {
        if (!$version) {
            $version = 'patch';
        }
        if (in_array($version, w('major minor patch prerelease'))) {
            $this->$version();
        } elseif (!$this->populateVersion($version)) {
            throw new Exception('Invalid version format!');
        }
        $this->write();

        return $this->content['version'];
    }

    public function git()
    {
        echo shell_exec('git add ' . escapeshellarg($this->file));
        echo shell_exec("git commit -m 'Bumps package version'");
        echo shell_exec('git tag ' . escapeshellarg($this->content['version']));
    }

    protected function major()
    {
        $this->major++;
        $this->minor = $this->patch = $this->prerelease = 0;
    }

    protected function minor()
    {
        $this->minor++;
        $this->patch = $this->prerelease = 0;
    }

    protected function patch()
    {
        $this->patch++;
        $this->prerelease = 0;
    }

    protected function prerelease()
    {
        $this->prerelease = $this->incrementPrerelease($this->prerelease);
    }

    /**
     * '' => 1, '1' => 2, 'beta' => 'betb'
     *
     * @param int|string $prerelease
     *
     * @return int|string
     */
    protected function incrementPrerelease($prerelease)
    {
        if ('' === $prerelease || null === $prerelease) {
            return 1;
        }
        if (is_numeric($prerelease)) {
            return (int)$prerelease + 1;
        }
        if (function_exists('str_increment') && preg_match('/^[a-zA-Z0-9]+$/', $prerelease)) {
            return str_increment($prerelease);
        }

        return ++$prerelease;
    }

    protected function populateVersion($version)
    {
        if ($ret = preg_match($this->format, (string)$version, $matches)) {
            list($all, $major, $minor, $patch, $this->prerelease) = $matches;
            $this->major = (int)$major;
            $this->minor = (int)$minor;
            $this->patch = (int)$patch;
        }

        return $ret;
    }

    protected function read()
    {
        $this->content = json_decode(file_get_contents($this->file), true);
        if (isset($this->content['version'])) {
            $this->populateVersion($this->content['version']);
        }
    }

    protected function write()
    {
        $this->content['version'] = $this->prepareVersion();

        return file_put_contents($this->file, json_encode($this->content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }

    protected function prepareVersion()
    {
        $version = "$this->major.$this->minor.$this->patch";
        if ($this->prerelease) {
            $version .= "-$this->prerelease";
        }

        return $version;
    }
}
