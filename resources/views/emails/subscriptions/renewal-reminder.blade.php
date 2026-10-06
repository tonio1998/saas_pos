<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Renewal Reminder — LikhaPOS Cloud</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
            line-height: 1.6;
        }
        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }
        .email-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 32px 30px;
            text-align: center;
            color: #ffffff;
        }
        .logo-badge {
            display: inline-block;
            background: #059669;
            color: #ffffff;
            font-size: 20px;
            font-weight: 800;
            padding: 6px 14px;
            border-radius: 10px;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }
        .email-body {
            padding: 32px 30px;
        }
        .alert-box {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-weight: 600;
            font-size: 15px;
        }
        .alert-critical {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }
        .alert-warning {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
        }
        .invoice-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 22px;
            margin-bottom: 24px;
        }
        .invoice-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #cbd5e1;
            font-size: 14px;
        }
        .invoice-row:last-child {
            border-bottom: none;
            padding-top: 14px;
            font-size: 17px;
            font-weight: 800;
            color: #0f172a;
        }
        .payment-methods-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 24px;
        }
        .cta-button {
            display: block;
            width: 100%;
            box-sizing: border-box;
            background: #059669;
            color: #ffffff !important;
            text-align: center;
            padding: 15px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 24px;
            box-shadow: 0 4px 14px rgba(5, 150, 105, 0.35);
        }
        .support-banner {
            background: #f1f5f9;
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            font-size: 13px;
            color: #475569;
        }
        .email-footer {
            background: #f8fafc;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="logo-badge">LikhaPOS</div>
            <div style="font-size: 20px; font-weight: 700; margin-top: 4px;">Subscription Renewal Notice</div>
            <div style="font-size: 13px; color: #94a3b8;">Cloud POS &amp; Retail Store Management Platform</div>
        </div>

        <!-- Body -->
        <div class="email-body">
            <p style="font-size: 16px; margin-top: 0;">
                Dear <strong>{{ $tenant->owner_name ?? 'Store Owner' }}</strong>,
            </p>

            <p style="color: #475569; margin-bottom: 20px;">
                This is an automated subscription renewal reminder for your store 
                <strong style="color: #0f172a;">{{ $tenant->business_name }}</strong> 
                (Store Code: <code>{{ $tenant->business_code }}</code>).
            </p>

            <!-- Urgency Alert -->
            @if($daysRemaining !== null && $daysRemaining < 0)
                <div class="alert-box alert-critical">
                    ⚠️ Your subscription expired on {{ $dueDateFormatted }} ({{ abs($daysRemaining) }} days ago). Please settle your renewal promptly to avoid interruption in POS cashier syncing.
                </div>
            @elseif($daysRemaining === 0)
                <div class="alert-box alert-critical">
                    🚨 Your subscription is due <strong>TODAY</strong> ({{ $dueDateFormatted }}). Please renew today to keep your POS terminal fully operational.
                </div>
            @elseif($daysRemaining !== null && $daysRemaining <= 3)
                <div class="alert-box alert-critical">
                    ⏳ Only <strong>{{ $daysRemaining }} day(s) remaining</strong> before your subscription expires on {{ $dueDateFormatted }}.
                </div>
            @else
                <div class="alert-box alert-warning">
                    📅 Your subscription is scheduled for renewal on <strong>{{ $dueDateFormatted }}</strong> ({{ $daysRemaining }} days remaining).
                </div>
            @endif

            <!-- Custom Note if provided by SuperAdmin -->
            @if($customNote)
                <div style="background: #eff6ff; border-left: 4px solid #3b82f6; padding: 12px 16px; border-radius: 8px; margin-bottom: 22px; font-size: 14px; color: #1e3a8a;">
                    <strong>Note from LikhaPOS Administration:</strong><br>
                    {{ $customNote }}
                </div>
            @endif

            <!-- Invoice Summary Card -->
            <div class="invoice-card">
                <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; margin-bottom: 12px;">
                    Subscription Details
                </div>
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <tr style="border-bottom: 1px dashed #cbd5e1;">
                        <td style="padding: 8px 0; color: #64748b;">Store Name:</td>
                        <td style="padding: 8px 0; text-align: right; font-weight: 600; color: #0f172a;">{{ $tenant->business_name }}</td>
                    </tr>
                    <tr style="border-bottom: 1px dashed #cbd5e1;">
                        <td style="padding: 8px 0; color: #64748b;">Subscription Plan:</td>
                        <td style="padding: 8px 0; text-align: right; font-weight: 600; color: #0f172a;">{{ $planName }}</td>
                    </tr>
                    <tr style="border-bottom: 1px dashed #cbd5e1;">
                        <td style="padding: 8px 0; color: #64748b;">Due / Expiration Date:</td>
                        <td style="padding: 8px 0; text-align: right; font-weight: 600; color: #0f172a;">{{ $dueDateFormatted }}</td>
                    </tr>
                    <tr style="border-bottom: 1px dashed #cbd5e1;">
                        <td style="padding: 8px 0; color: #64748b;">Extension Period:</td>
                        <td style="padding: 8px 0; text-align: right; font-weight: 600; color: #0f172a;">+30 Days (1 Month)</td>
                    </tr>
                    <tr>
                        <td style="padding: 14px 0 0; font-size: 16px; font-weight: 700; color: #0f172a;">Amount Due:</td>
                        <td style="padding: 14px 0 0; text-align: right; font-size: 20px; font-weight: 800; color: #059669;">₱{{ number_format($amountDue, 2) }}</td>
                    </tr>
                </table>
            </div>

            <!-- How to Pay -->
            <div class="payment-methods-box">
                <div style="font-weight: 700; color: #166534; font-size: 15px; margin-bottom: 8px;">
                    💳 Fast Payment Channels (QRPH / GCash / Maya)
                </div>
                <div style="font-size: 13px; color: #14532d; line-height: 1.5;">
                    • <strong>GCash / Maya Account:</strong> <code>0912 894 1731</code> (LikhaPOS Operations)<br>
                    • <strong>InstaPay / Bank QR:</strong> Scan QRPH code at checkout<br>
                    • After sending payment, simply upload your transaction receipt at the link below for immediate verification.
                </div>
            </div>

            <!-- Action Button -->
            <a href="http://pos.dev.com/subscription/checkout" class="cta-button" target="_blank">
                Renew Subscription &amp; Upload Receipt &rarr;
            </a>

            <!-- 24/7 Support Banner -->
            <div class="support-banner">
                📞 <strong>24/7 Dedicated Support Hotline:</strong> <a href="tel:09128941731" style="color: #059669; font-weight: 700; text-decoration: none;">0912 894 1731</a><br>
                Need assistance with payment, grace periods, or cashier terminals? We are available 24 hours a day, 7 days a week.
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            &copy; {{ date('Y') }} LikhaPOS Cloud Platform. All rights reserved.<br>
            This is an automated system email sent to registered store accounts.
        </div>
    </div>
</body>
</html>
