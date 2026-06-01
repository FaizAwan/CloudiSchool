<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to CloudiSchool</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f7fa;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

        .email-wrapper {
            width: 100%;
            background-color: #f4f7fa;
            padding: 40px 0;
        }

        .email-content {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .hero-section {
            background: linear-gradient(135deg, #4169E1 0%, #2e51bb 100%);
            padding: 60px 40px;
            text-align: center;
            color: #ffffff;
        }

        .hero-section img.logo {
            width: 80px;
            margin-bottom: 24px;
        }

        .hero-section h1 {
            font-size: 32px;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .body-section {
            padding: 40px;
            color: #4a5568;
            line-height: 1.7;
        }

        .body-section h2 {
            color: #1a202c;
            font-size: 24px;
            margin-top: 0;
        }

        .feature-card {
            background-color: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #4169E1;
        }

        .feature-card h3 {
            margin: 0 0 8px 0;
            font-size: 18px;
            color: #2d3748;
        }

        .feature-card p {
            margin: 0;
            font-size: 14px;
        }

        .cta-button {
            display: inline-block;
            background: #4169E1;
            color: #ffffff !important;
            padding: 16px 40px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 16px;
            margin-top: 30px;
            box-shadow: 0 4px 14px rgba(65, 105, 225, 0.39);
        }

        .footer-section {
            background-color: #f8fafc;
            padding: 30px 40px;
            text-align: center;
            color: #718096;
            font-size: 13px;
        }

        .footer-section p {
            margin: 8px 0;
        }

        .social-links {
            margin-top: 20px;
        }

        .social-links a {
            margin: 0 10px;
            color: #4169E1;
            text-decoration: none;
            font-weight: 600;
        }

        .highlight {
            color: #4169E1;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <div class="email-content">
            <!-- Hero -->
            <div class="hero-section">
                <img src="https://cloudischool.com/public/images/rebrand/cloudischool_logo_3d.png" alt="cloudischool" class="logo">
                <h1>Welcome to the Future of Schooling!</h1>
            </div>

            <!-- Body -->
            <div class="body-section">
                <h2>Hi {{ $name }},</h2>
                <p>Welcome to <strong>cloudischool</strong>! We are excited to partner with your institution to transform your daily operations into a seamless digital experience.</p>

                <p>Your dedicated school instance has been successfully created. You can now start managing everything from one powerful dashboard.</p>

                <div class="feature-card">
                    <h3>🚀 Quick Onboarding</h3>
                    <p>Log in to your admin panel and follow the 5-minute setup wizard to configure your school profile.</p>
                </div>

                <div class="feature-card">
                    <h3>👥 Centralized Management</h3>
                    <p>Easily manage Students, Teachers, and Parents. Auto-generate IDs and manage enrollments with one click.</p>
                </div>

                <div class="feature-card">
                    <h3>📊 Real-time Insights</h3>
                    <p>Get instant reports on Fee collections, Attendance, and Academic performance through interactive charts.</p>
                </div>

                <div style="text-align: center;">
                    <a href="https://cloudischool.com/login" class="cta-button">Launch Your Dashboard</a>
                </div>

                <p style="margin-top: 40px;">Need help getting started? Reply to this email or reach us on <span class="highlight">WhatsApp: +92 321 0000000</span> (Placeholder).</p>

                <p>Best Regards,<br><strong>The cloudischool Team</strong></p>
            </div>

            <!-- Footer -->
            <div class="footer-section">
                <p>&copy; 2026 cloudischool - Smart School Management System. All rights reserved.</p>
                <p>This is an automated message. Please do not reply directly to this email.</p>
                <div class="social-links">
                    <a href="#">Website</a> | <a href="#">Facebook</a> | <a href="#">LinkedIn</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>