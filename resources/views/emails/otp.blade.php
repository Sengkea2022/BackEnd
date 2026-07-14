<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Code</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f7f7f8;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
            -ms-text-size-adjust: none;
        }
        .container {
            max-width: 570px;
            margin: 40px auto;
            background-color: #ffffff;
            border: 1px solid #e3e4e6;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }
        .header {
            background-color: #f97316; /* Theme primary orange */
            color: #ffffff;
            text-align: center;
            padding: 30px 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
            color: #2b2d31;
            line-height: 1.6;
        }
        .content p {
            margin: 0 0 20px 0;
            font-size: 16px;
        }
        .otp-container {
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            display: inline-block;
            background-color: #fff7ed; /* Light orange */
            border: 2px dashed #f97316;
            border-radius: 12px;
            color: #ea580c;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 6px;
            padding: 15px 30px;
            text-shadow: 1px 1px 0 rgba(0,0,0,0.02);
        }
        .footer {
            background-color: #f7f7f8;
            border-top: 1px solid #e3e4e6;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }
        .footer p {
            margin: 0 0 8px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Verify Your Identity</h1>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>Thank you for choosing our platform. Use the following One-Time Password (OTP) to complete your login or registration process. This code is valid for <strong>10 minutes</strong>.</p>
            
            <div class="otp-container">
                <div class="otp-code">{{ $otpCode }}</div>
            </div>

            <p>If you did not request this code, you can safely ignore this email.</p>
            <p>Best regards,<br>The Platform Team</p>
        </div>
        <div class="footer">
            <p>This is an automated security email. Please do not reply directly to this message.</p>
            <p>&copy; {{ date('Y') }} Platform. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
