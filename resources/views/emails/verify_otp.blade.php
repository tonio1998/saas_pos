<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LikhaPOS Email Verification</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f8; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; color: #1e293b;">

    <!-- Outer Container -->
    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f3f4f8; padding: 40px 16px;">
        <tr>
            <td align="center">
                <!-- Main Email Card -->
                <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 540px; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 35px rgba(15, 23, 42, 0.08); border: 1px solid #e2e8f0;">
                    
                    <!-- Gradient Hero Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #7d2ae8 0%, #5e17eb 50%, #00c4cc 100%); padding: 36px 32px 32px; text-align: center;">
                            <!-- Logo Icon -->
                            <table role="presentation" border="0" cellspacing="0" cellpadding="0" align="center" style="margin-bottom: 14px;">
                                <tr>
                                    <td style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.35); width: 52px; height: 52px; border-radius: 16px; text-align: center; vertical-align: middle; font-size: 26px;">
                                        ✨
                                    </td>
                                </tr>
                            </table>
                            <h1 style="margin: 0; font-size: 26px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">
                                Likha<span style="color: #67e8f9;">POS</span> Cloud
                            </h1>
                            <p style="margin: 6px 0 0; font-size: 13px; font-weight: 600; color: rgba(255, 255, 255, 0.88); text-transform: uppercase; letter-spacing: 1.5px;">
                                Account Verification & Security
                            </p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 36px 36px 28px;">
                            <h2 style="margin: 0 0 12px; font-size: 20px; font-weight: 700; color: #0f172a;">
                                Kumusta, {{ $name }}! 👋
                            </h2>
                            <p style="margin: 0 0 20px; font-size: 15px; line-height: 1.6; color: #475569;">
                                Salamat sa pagpili sa <strong>LikhaPOS Cloud Minimart & CRM</strong>. Isang hakbang na lang bago mo ma-access ang iyong tindahan. Gamitin ang <strong>6-digit verification code</strong> sa ibaba para i-activate ang iyong account:
                            </p>

                            <!-- 6-Digit OTP Interactive-Look Display -->
                            <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 18px; padding: 24px 16px; text-align: center; margin: 26px 0 22px;">
                                <div style="font-size: 11px; font-weight: 800; color: #64748b; letter-spacing: 1.2px; text-transform: uppercase; margin-bottom: 14px;">
                                    Your 6-Digit Verification Code
                                </div>

                                <!-- Digit Boxes Table -->
                                <table role="presentation" border="0" cellspacing="0" cellpadding="0" align="center" style="margin: 0 auto;">
                                    <tr>
                                        @php
                                            $digits = str_split(strval($otp));
                                        @endphp
                                        @foreach($digits as $digit)
                                            <td style="padding: 0 4px;">
                                                <div style="width: 48px; height: 56px; line-height: 56px; text-align: center; background: #ffffff; border: 2px solid #7d2ae8; border-radius: 12px; font-size: 28px; font-weight: 800; color: #5e17eb; box-shadow: 0 4px 12px rgba(125, 42, 232, 0.12); font-family: 'SF Pro Display', Consolas, Monaco, monospace;">
                                                    {{ $digit }}
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                </table>

                                <!-- Countdown / Expiry Pill -->
                                <div style="margin-top: 16px;">
                                    <span style="display: inline-block; background: #fef3c7; color: #92400e; font-size: 12px; font-weight: 700; padding: 5px 14px; border-radius: 30px; border: 1px solid #fde68a;">
                                        ⏳ Valid for 10 minutes only
                                    </span>
                                </div>
                            </div>

                            <!-- Onboarding Roadmap Chips -->
                            <div style="background: #f8fafc; border-radius: 16px; padding: 18px 20px; margin-bottom: 24px; border-left: 4px solid #00c4cc;">
                                <div style="font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 8px;">
                                    📍 Next Steps after verification:
                                </div>
                                <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td style="font-size: 13px; color: #475569; padding: 3px 0;">
                                            <strong style="color: #5e17eb;">1.</strong> I-enter ang 6-digit code sa verification screen
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 13px; color: #475569; padding: 3px 0;">
                                            <strong style="color: #5e17eb;">2.</strong> I-setup ang iyong Main Store Branch
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 13px; color: #475569; padding: 3px 0;">
                                            <strong style="color: #5e17eb;">3.</strong> Simulan ang benta at pautang tracking nang libre!
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Security Reminder Note -->
                            <p style="margin: 0; font-size: 12px; line-height: 1.5; color: #94a3b8; text-align: center;">
                                🔒 <strong>Paalala sa Seguridad:</strong> Huwag ibigay ang code na ito kahit kanino. Ang LikhaPOS team ay hindi kailanman hihingi ng iyong OTP.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 22px 32px; text-align: center;">
                            <p style="margin: 0 0 6px; font-size: 13px; font-weight: 700; color: #334155;">
                                LikhaPOS • Cloud Minimart & CRM Platform
                            </p>
                            <p style="margin: 0; font-size: 11px; color: #94a3b8; line-height: 1.5;">
                                © {{ date('Y') }} LikhaPOS. Lahat ng karapatan ay nakalaan.<br>
                                Kung hindi ikaw ang nag-rehistro ng account na ito, maaari mong ligtas na balewalain ang email.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
