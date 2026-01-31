<?php

use Illuminate\Support\Facades\Mail;

echo "==============================================\n";
echo "  TESTING GMAIL SMTP CONFIGURATION\n";
echo "==============================================\n\n";

echo "Configuration:\n";
echo "  Mailer: " . config('mail.default') . "\n";
echo "  Host: " . config('mail.mailers.smtp.host') . "\n";
echo "  Port: " . config('mail.mailers.smtp.port') . "\n";
echo "  From: " . config('mail.from.address') . "\n\n";

echo "Attempting to send test email...\n\n";

try {
    Mail::raw('✅ SUCCESS! Email configuration is working correctly.

This is a test email from Margadarsi Portal.

Configuration Details:
- SMTP Host: smtp.gmail.com
- Port: 587
- Encryption: TLS
- From: noreplymargadarsiinfra@gmail.com

If you received this email, your email system is ready for:
- Forgot Password OTPs
- Password Reset Confirmations
- Lead Assignment Notifications
- System Notifications

Timestamp: ' . now()->format('Y-m-d H:i:s'), function($message) {
        $message->to('noreplymargadarsiinfra@gmail.com')
                ->subject('✅ Test Email - Margadarsi Portal SMTP Working');
    });
    
    echo "✅ SUCCESS! Email sent successfully!\n\n";
    echo "📧 Email Details:\n";
    echo "   To: noreplymargadarsiinfra@gmail.com\n";
    echo "   Subject: ✅ Test Email - Margadarsi Portal SMTP Working\n\n";
    echo "🎯 Next Steps:\n";
    echo "   1. Check your inbox at noreplymargadarsiinfra@gmail.com\n";
    echo "   2. Verify the test email arrived\n";
    echo "   3. Ready to use for Forgot Password feature!\n\n";
    echo "==============================================\n";
    echo "  EMAIL SYSTEM: OPERATIONAL ✅\n";
    echo "==============================================\n";
    
} catch (\Exception $e) {
    echo "❌ FAILED! Email could not be sent.\n\n";
    echo "Error Message:\n";
    echo $e->getMessage() . "\n\n";
    echo "Error Type: " . get_class($e) . "\n";
}
