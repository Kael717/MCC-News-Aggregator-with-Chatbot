<?php
header('Content-Type: text/plain');

$public = __DIR__;
$oneUp  = dirname(__DIR__);

$checks = [
    'public/vendor/autoload.php'     => $public.'/vendor/autoload.php',
    'public/bootstrap/app.php'       => $public.'/bootstrap/app.php',
    'public/routes/api.php (via bootstrap)' => $public.'/routes/api.php',
    '../vendor/autoload.php'         => $oneUp.'/vendor/autoload.php',
    '../bootstrap/app.php'           => $oneUp.'/bootstrap/app.php',
    '../routes/api.php'              => $oneUp.'/routes/api.php',
];

echo "CWD: {$public}\n";
echo "OneUp: {$oneUp}\n\n";

foreach ($checks as $label => $path) {
    echo str_pad($label, 40) . ': ' . (file_exists($path) ? 'FOUND' : 'MISSING') . " -> {$path}\n";
}

// Extra: show what index.php will pick as base dir
$baseDir = $public;
if (!file_exists($baseDir.'/vendor/autoload.php') || !file_exists($baseDir.'/bootstrap/app.php')) {
    $baseDir = $oneUp;
}
echo "\nindex.php baseDir resolution: {$baseDir}\n";