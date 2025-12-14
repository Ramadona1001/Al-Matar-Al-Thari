<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $locale === 'ar' ? 'إعادة تعيين كلمة المرور' : 'Reset Password' }}</title>
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
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
        .reset-button {
            display: inline-block;
            padding: 16px 40px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(245, 87, 108, 0.4);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .reset-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 87, 108, 0.5);
        }
        .info-box {
            background: #fff3f4;
            border-left: 4px solid #f5576c;
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
        .warning-box {
            background: #fff8e1;
            border-left: 4px solid #ffc107;
            padding: 20px;
            margin: 30px 0;
            border-radius: 4px;
        }
        .warning-box p {
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
            color: #f5576c;
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
            .reset-button {
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
                <i class="fas fa-key" style="color: #ffffff;"></i>
            </div>
            <h1>{{ $locale === 'ar' ? 'إعادة تعيين كلمة المرور' : 'Reset Password' }}</h1>
        </div>

        <!-- Body -->
        <div class="email-body">
            <p class="greeting">
                {{ $locale === 'ar' ? 'مرحباً ' : 'Hello ' }}{{ $userName }},
            </p>

            <p class="message">
                {{ $locale === 'ar' 
                    ? 'لقد تلقينا طلباً لإعادة تعيين كلمة المرور لحسابك. إذا كنت أنت من طلب هذا التغيير، يرجى النقر على الزر أدناه لإعادة تعيين كلمة المرور الخاصة بك.' 
                    : 'We received a request to reset the password for your account. If you requested this change, please click the button below to reset your password.' }}
            </p>

            <div class="button-container">
                <a href="{{ $resetUrl }}" class="reset-button">
                    {{ $locale === 'ar' ? 'إعادة تعيين كلمة المرور' : 'Reset Password' }}
                </a>
            </div>

            <div class="info-box">
                <p>
                    {{ $locale === 'ar' 
                        ? '⏰ هذا الرابط صالح لمدة 60 دقيقة فقط. إذا انتهت صلاحية الرابط، يمكنك طلب رابط جديد من صفحة إعادة تعيين كلمة المرور.' 
                        : '⏰ This link is valid for 60 minutes only. If the link has expired, you can request a new one from the password reset page.' }}
                </p>
            </div>

            <div class="warning-box">
                <p>
                    {{ $locale === 'ar' 
                        ? '🔒 إذا لم تطلب إعادة تعيين كلمة المرور، يمكنك تجاهل هذا البريد الإلكتروني بأمان. لن يتم تغيير كلمة المرور الخاصة بك إلا إذا قمت بالنقر على الرابط أعلاه.' 
                        : '🔒 If you did not request a password reset, you can safely ignore this email. Your password will not be changed unless you click the link above.' }}
                </p>
            </div>

            <div class="alternative-link">
                <p>
                    {{ $locale === 'ar' ? 'إذا لم يعمل الزر، يمكنك نسخ ولصق الرابط التالي في متصفحك:' : 'If the button doesn\'t work, you can copy and paste the following link into your browser:' }}
                </p>
                <a href="{{ $resetUrl }}">{{ $resetUrl }}</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p>
                {{ $locale === 'ar' 
                    ? 'هذا البريد الإلكتروني تم إرساله تلقائياً. يرجى عدم الرد عليه.' 
                    : 'This email was sent automatically. Please do not reply to it.' }}
            </p>
            <p style="margin-top: 15px;">
                {{ $locale === 'ar' ? 'مع أطيب التحيات،' : 'Best regards,' }}<br>
                <strong>{{ config('app.name') }}</strong>
            </p>
        </div>
    </div>
</body>
</html>

