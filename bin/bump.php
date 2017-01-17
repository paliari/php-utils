<?php
use Paliari\Utils\Bump;

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
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
