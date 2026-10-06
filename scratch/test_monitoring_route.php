<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing route: " . route('sa.subscriptions.monitoring.export-csv') . "\n";
echo "Testing monitoring view render:\n";
$html = view('pages.sa.subscriptions.monitoring')->render();
echo "Rendered monitoring view successfully! Length: " . strlen($html) . " bytes\n";
