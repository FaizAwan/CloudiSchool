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
            background: #1E293B;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }

        .content {
            padding: 20px;
        }

        .field {
            margin-bottom: 10px;
        }

        .label {
            font-weight: bold;
            color: #0A6CFF;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>New Contact Inquiry</h1>
        </div>
        <div class="content">
            <div class="field"><span class="label">Name:</span> {{ $contact->name }}</div>
            <div class="field"><span class="label">Email:</span> {{ $contact->email }}</div>
            <div class="field"><span class="label">Subject:</span> {{ $contact->subject }}</div>
            <div class="field"><span class="label">Message:</span></div>
            <p style="background: #f9f9f9; padding: 15px; border-left: 4px solid #1E293B;">{{ $contact->message }}</p>
        </div>
    </div>
</body>

</html>