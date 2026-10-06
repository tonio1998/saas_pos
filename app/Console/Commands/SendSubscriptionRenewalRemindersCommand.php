<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionRenewalReminderMail;
use App\Models\POS\POSTenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendSubscriptionRenewalRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:send-reminders 
                            {--tenant= : Specific tenant ID to remind}
                            {--force : Send reminder even if one was sent today}
                            {--dry-run : Simulate execution without sending emails}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically scan tenants nearing subscription due dates and send email renewal notices';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("🚀 LikhaPOS Subscription Renewal Sentinel — Running scan...");

        $query = POSTenant::with(['subscription'])
            ->whereNotNull('subscription_end')
            ->where('subscription_end', '<=', now()->addDays(7)->endOfDay())
            ->where('status', '!=', 'inactive');

        if ($tenantId = $this->option('tenant')) {
            $query->where('id', $tenantId);
        }

        $tenants = $query->get();

        if ($tenants->isEmpty()) {
            $this->info("✅ No tenants currently due for renewal (<= 7 days).");
            return Command::SUCCESS;
        }

        $force = (bool) $this->option('force');
        $dryRun = (bool) $this->option('dry-run');

        $sentCount = 0;
        $skippedCount = 0;
        $failedCount = 0;

        foreach ($tenants as $tenant) {
            $days = $tenant->days_until_due;
            $recipientEmail = $tenant->email;

            if (empty($recipientEmail)) {
                $ownerUser = $tenant->creator ?? \App\Models\User::where('tenant_id', $tenant->id)->first();
                $recipientEmail = $ownerUser?->email;
            }

            if (empty($recipientEmail)) {
                $this->warn("⚠️ Skipped [{$tenant->business_name}]: No email address registered.");
                $skippedCount++;
                continue;
            }

            // Don't send more than once per day unless forced
            if (!$force && $tenant->last_reminder_sent_at && $tenant->last_reminder_sent_at->isToday()) {
                $this->line("ℹ️ Skipped [{$tenant->business_name}]: Already reminded today ({$tenant->last_reminder_sent_at->format('H:i')}).");
                $skippedCount++;
                continue;
            }

            $this->line("📨 Processing [{$tenant->business_name}] (Due in {$days} days, Recipient: {$recipientEmail})...");

            if ($dryRun) {
                $sentCount++;
                continue;
            }

            try {
                Mail::to($recipientEmail)->queue(new SubscriptionRenewalReminderMail($tenant));

                $tenant->update([
                    'last_reminder_sent_at' => now(),
                    'reminder_count'        => (int) ($tenant->reminder_count ?? 0) + 1,
                    'last_reminder_channel' => 'email',
                    'last_reminder_notes'   => 'Automated cron reminder dispatched.',
                ]);

                $sentCount++;
                $this->info("  -> Sent successfully to {$recipientEmail}!");
            } catch (\Throwable $e) {
                $failedCount++;
                $this->error("  -> Failed to send to {$recipientEmail}: " . $e->getMessage());
                Log::error("Subscription reminder email failed for tenant #{$tenant->id}: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            [
                ['Scanned Tenants Due', $tenants->count()],
                ['Emails Dispatched', $sentCount],
                ['Skipped (Already Reminded / No Email)', $skippedCount],
                ['Failures', $failedCount],
            ]
        );

        return Command::SUCCESS;
    }
}
