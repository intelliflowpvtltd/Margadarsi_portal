<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .container {
            background: #f9f9f9;
            border-radius: 10px;
            padding: 30px;
            border: 1px solid #e0e0e0;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .otp-box {
            background: #ffffff;
            border: 2px dashed #4CAF50;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }

        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #4CAF50;
            letter-spacing: 8px;
            margin: 10px 0;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Password Reset Request</h2>
        </div>

        <p>Hello <strong>{{ $userName }}</strong>,</p>

        <p>We received a request to reset your password for your Margadarsi Portal account.</p>

        <p>Please use the following One-Time Password (OTP) to verify your identity:</p>

        <div class="otp-box">
            <p style="margin: 0; font-size: 14px; color: #666;">Your OTP Code</p>
            <div class="otp-code">{{ $otp }}</div>
            <p style="margin: 10px 0 0 0; font-size: 13px; color: #999;">Valid for 10 minutes</p>
        </div>

        <p><strong>Important:</strong></p>
        <ul>
            <li>This OTP will expire in <strong>10 minutes</strong></li>
            <li>Do not share this code with anyone</li>
            <li>If you didn't request this, please ignore this email</li>
        </ul>

        <div class="footer">
            <p>© {{ date('Y') }} Margadarsi Infra. All rights reserved.</p>
            <p>This is an automated email. Please do not reply.</p>
        </div>
    </div>
</body>

</html>