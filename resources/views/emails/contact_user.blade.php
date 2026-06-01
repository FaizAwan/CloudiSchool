<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #eee;
            border-radius: 10px;
        }

        .header {
            background: #0A6CFF;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }

        .content {
            padding: 20px;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>CloudiSchool</h1>
        </div>
        <div class="content">
            <p>Hello <strong>{{ $contact->name }}</strong>,</p>
            <p>Thank you for reaching out to us. We have received your inquiry and our representatives will contact you shortly.</p>
            <p><strong>Your Message:</strong></p>
            <p style="background: #f9f9f9; padding: 15px; border-left: 4px solid #0A6CFF;">{{ $contact->message }}</p>
            <p>Best Regards,<br>CloudiSchool Team</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} CloudiSchool. All rights reserved.
        </div>
    </div>
</body>

</html>