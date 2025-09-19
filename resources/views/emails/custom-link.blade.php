<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9fafb;
        }
        .container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .message {
            font-size: 16px;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .link-container {
            text-align: center;
            margin: 30px 0;
        }
        .link-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            transition: transform 0.2s;
        }
        .link-button:hover {
            transform: translateY(-2px);
        }
        .link-text {
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            font-family: monospace;
            font-size: 14px;
            word-break: break-all;
            color: #374151;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px 30px;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
            color: #6b7280;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $subject }}</h1>
        </div>
        
        <div class="content">
            <div class="message">
                {!! nl2br(e($message ?? 'Wiadomość z linkiem.')) !!}
            </div>
            
            <div class="link-container">
                <a href="{{ $link }}" class="link-button">
                    🔗 Otwórz link
                </a>
            </div>
            
            <div class="link-text">
                <strong>Link:</strong><br>
                {{ $link }}
            </div>
        </div>
        
        <div class="footer">
            <p>📧 Wiadomość od {{ config('app.payment_reminder_company_name') }}</p>
            <p>To jest wiadomość systemowa z aplikacji {{ config('app.payment_reminder_company_name') }}.</p>
            <p>Jeśli masz pytania, skontaktuj się z nami:</p>
            <p>📧 Email: {{ config('app.payment_reminder_email') }}</p>
            <p>☎️ Telefon: {{ config('app.payment_reminder_phone') }}</p>
            <p>🌐 Strona: {{ config('app.payment_reminder_website') }}</p>
            <p><small>Wiadomość wysłana automatycznie przez system {{ config('app.payment_reminder_company_name') }}.</small></p>
        </div>
    </div>
</body>
</html>
