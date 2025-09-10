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
            color: #374151;
            background-color: #f9fafb;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .welcome {
            font-size: 18px;
            margin-bottom: 20px;
            color: #1f2937;
        }
        .message-content {
            background-color: #f3f4f6;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #10b981;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        .user-info {
            background-color: #f3f4f6;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .user-info strong {
            color: #1f2937;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📧 Wiadomość od Grupy Poledance</h1>
        </div>
        
        <div class="content">
            <div class="welcome">
                Cześć <strong>{{ $user->name }}</strong>!
            </div>
            
            <div class="user-info">
                <strong>Wiadomość dla:</strong><br>
                Imię i nazwisko: {{ $user->name }}<br>
                Email: {{ $user->email }}
            </div>
            
            <div class="message-content">
                {!! $content !!}
            </div>
            
            <div style="margin-top: 30px; color: #6b7280; font-size: 14px;">
                <p>To jest wiadomość systemowa z aplikacji {{ config('app.payment_reminder_company_name') }}.</p>
                <p>Jeśli masz pytania, skontaktuj się z nami:</p>
                <p>📧 Email: {{ config('app.payment_reminder_email') }}</p>
                <p>☎️ Telefon: {{ config('app.payment_reminder_phone') }}</p>
                <p>🌐 Strona: {{ config('app.payment_reminder_website') }}</p>
            </div>
        </div>
        
        <div class="footer">
            <p>Wiadomość wysłana automatycznie przez system {{ config('app.payment_reminder_company_name') }}.</p>
            <p>Nie odpowiadaj na ten email - użyj kontaktu w aplikacji.</p>
        </div>
    </div>
</body>
</html> 