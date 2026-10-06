<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = Illuminate\Support\Facades\DB::select('SHOW TABLES');
foreach ($tables as $t) {
    $tArr = (array) $t;
    $name = reset($tArr);
    if (str_contains($name, 'sub') || str_contains($name, 'bill') || str_contains($name, 'inv') || str_contains($name, 'pay')) {
        echo $name . "\n";
    }
}
