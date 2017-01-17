<?php
use Paliari\Utils\Bump;

$autoloadFiles = [__DIR__ . '/../vendor/autoload.php', __DIR__ . '/../../../autoload.php'];
foreach ($autoloadFiles as $autoloadFile) {
    if (file_exists($autoloadFile)) {
        require_once "$autoloadFile";
    }
}
$options = getopt('f:v:g', ['file:', 'version:', 'git']);
$file    = @$options['file'] ?: @$options['f'];
$version = @$options['version'] ?: @$options['v'];
$git     = array_key_exists('git', $options) || array_key_exists('g', $options);
$bump    = new Bump($file);
$version = $bump->version($version);
if ($git) {
    $bump->git();
}
echo "Version: $version\n";
