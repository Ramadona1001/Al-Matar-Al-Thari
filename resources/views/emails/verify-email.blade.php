<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $locale === 'ar' ? 'تحقق من بريدك الإلكتروني' : 'Verify Your Email' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            color: #ffffff;
        }
        .email-header .logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 40px;
        }
        .email-header h1 {
            font-size: 28px;
            font-weight: 600;
            margin: 0;
            color: #ffffff;
        }
        .email-body {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .message {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.8;
        }
        .button-container {
            text-align: center;
            margin: 40px 0;
        }
        .verify-button {
            display: inline-block;
            padding: 16px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .verify-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 30px 0;
            border-radius: 4px;
        }
        .info-box p {
            margin: 0;
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }
        .alternative-link {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e0e0e0;
        }
        .alternative-link p {
            font-size: 14px;
            color: #999;
            margin-bottom: 10px;
        }
        .alternative-link a {
            color: #667eea;
            word-break: break-all;
            font-size: 12px;
            text-decoration: none;
        }
        .email-footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
        }
        .email-footer p {
            font-size: 14px;
            color: #999;
            margin: 5px 0;
        }
        .email-footer .social-links {
            margin-top: 20px;
        }
        .email-footer .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #667eea;
            text-decoration: none;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 20px;
                border-radius: 8px;
            }
            .email-header {
                padding: 30px 20px;
            }
            .email-header h1 {
                font-size: 24px;
            }
            .email-body {
                padding: 30px 20px;
            }
            .verify-button {
                padding: 14px 30px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="logo">
                <i class="fas fa-envelope-circle-check" style="color: #ffffff;"></i>
            </div>
            <h1>{{ $locale === 'ar' ? 'تحقق من بريدك الإلكتروني' : 'Verify Your Email' }}</h1>
        </div>

        <!-- Body -->
        <div class="email-body">
            <p class="greeting">
                {{ $locale === 'ar' ? 'مرحباً ' : 'Hello ' }}{{ $userName }},
            </p>

            <p class="message">
                {{ $locale === 'ar' 
                    ? 'شكراً لك على التسجيل في منصتنا! نحن متحمسون لانضمامك إلينا. لاستكمال عملية التسجيل والبدء في استخدام حسابك، يرجى التحقق من عنوان بريدك الإلكتروني عن طريق النقر على الزر أدناه.' 
                    : 'Thank you for registering with us! We\'re excited to have you on board. To complete your registration and start using your account, please verify your email address by clicking the button below.' }}
            </p>

            <div class="button-container">
                <a href="{{ $verificationUrl }}" class="verify-button">
                    {{ $locale === 'ar' ? 'تحقق من البريد الإلكتروني' : 'Verify Email Address' }}
                </a>
            </div>

            <div class="info-box">
                <p>
                    {{ $locale === 'ar' 
                        ? '⏰ هذا الرابط صالح لمدة 60 دقيقة فقط. إذا انتهت صلاحية الرابط، يمكنك طلب رابط جديد من صفحة التحقق.' 
                        : '⏰ This link is valid for 60 minutes only. If the link has expired, you can request a new one from the verification page.' }}
                </p>
            </div>

            <div class="alternative-link">
                <p>
                    {{ $locale === 'ar' ? 'إذا لم يعمل الزر، يمكنك نسخ ولصق الرابط التالي في متصفحك:' : 'If the button doesn\'t work, you can copy and paste the following link into your browser:' }}
                </p>
                <a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p>
                {{ $locale === 'ar' 
                    ? 'إذا لم تقم بإنشاء حساب، يمكنك تجاهل هذا البريد الإلكتروني بأمان.' 
                    : 'If you did not create an account, you can safely ignore this email.' }}
            </p>
            <p style="margin-top: 15px;">
                {{ $locale === 'ar' ? 'مع أطيب التحيات،' : 'Best regards,' }}<br>
                <strong>{{ config('app.name') }}</strong>
            </p>
        </div>
    </div>
</body>
</html>

