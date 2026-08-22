<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$cols = DB::select("SHOW COLUMNS FROM pos_cash_shifts");
foreach ($cols as $c) {
    echo "Field: {$c->Field} | Type: {$c->Type}\n";
}
