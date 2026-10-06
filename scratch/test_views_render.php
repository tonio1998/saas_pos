<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing route generation:\n";
echo "Create route: " . route('sa.subscriptions.plans.create') . "\n";
$firstPlan = \App\Models\POS\POSSubscription::first();
if ($firstPlan) {
    echo "Edit route for ID {$firstPlan->id}: " . route('sa.subscriptions.plans.edit', $firstPlan->id) . "\n";
    
    // Test rendering create view
    echo "Rendering create view...\n";
    $createHtml = view('pages.sa.subscriptions.plans.create')->render();
    echo "Create view rendered successfully! Size: " . strlen($createHtml) . " bytes\n";
    
    // Test rendering edit view
    echo "Rendering edit view...\n";
    $editHtml = view('pages.sa.subscriptions.plans.edit', ['plan' => $firstPlan])->render();
    echo "Edit view rendered successfully! Size: " . strlen($editHtml) . " bytes\n";
} else {
    echo "No subscription plan found in DB.\n";
}
