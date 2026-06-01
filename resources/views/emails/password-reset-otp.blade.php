<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password - CloudiSchool</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

        .wrapper {
            width: 100%;
            background-color: #f8fafc;
            padding: 40px 0;
        }

        .content {
            max-width: 500px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .header {
            background: linear-gradient(135deg, #4169E1 0%, #2e51bb 100%);
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
        }

        .body {
            padding: 40px;
            text-align: center;
            color: #4a5568;
        }

        .body p {
            font-size: 16px;
            margin-bottom: 24px;
        }

        .otp-container {
            background-color: #f1f5f9;
            padding: 20px;
            border-radius: 12px;
            margin: 30px 0;
            border: 2px dashed #4169E1;
        }

        .otp-code {
            font-size: 36px;
            font-weight: 800;
            color: #4169E1;
            letter-spacing: 12px;
            margin: 0;
        }

        .footer {
            padding: 20px;
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
            border-top: 1px solid #f1f5f9;
        }

        .warning {
            font-size: 13px;
            color: #ef4444;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="content">
            <div class="header">
                <h1>CloudiSchool</h1>
            </div>
            <div class="body">
                <p>We received a request to reset your password. Use the code below to verify your identity.</p>
                <div class="otp-container">
                    <h2 class="otp-code">{{ $otp }}</h2>
                </div>
                <p>This code is valid for <strong>10 minutes</strong>.</p>
                <div class="warning">If you did not request this code, please ignore this email. Your password will remain unchanged.</div>
            </div>
            <div class="footer">
                &copy; 2026 CloudiSchool - Smart School Management System.
            </div>
        </div>
    </div>
</body>

</html>