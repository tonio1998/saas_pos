<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$saUser = \App\Models\User::where('is_super_admin', true)->first() ?: \App\Models\User::first();
auth()->login($saUser);

$req = Illuminate\Http\Request::create('/sa/subscriptions/monitoring', 'GET');
$controller = app(\App\Http\Controllers\SASubscriptionMonitoringController::class);
$response = $controller->index($req);

echo "Controller response status: " . ($response instanceof \Illuminate\View\View ? 'View returned' : 'Other') . "\n";
$html = $response->render();
echo "Rendered monitoring view successfully! Length: " . strlen($html) . " bytes\n";
