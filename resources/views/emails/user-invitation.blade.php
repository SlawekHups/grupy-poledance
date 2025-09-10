<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zaproszenie do systemu Grupy Poledance</title>
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
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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
        .description {
            margin-bottom: 30px;
            color: #6b7280;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            margin: 20px 0;
        }
        .button:hover {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        .warning {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            color: #92400e;
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
            <h1>🎉 Witaj w Grupy Poledance!</h1>
        </div>
        
        <div class="content">
            <div class="welcome">
                Cześć <strong>{{ $user->name }}</strong>!
            </div>
            
            <div class="description">
                Zostałeś zaproszony do systemu zarządzania szkołą tańca Grupy Poledance. 
                Aby rozpocząć korzystanie z systemu, musisz ustawić swoje hasło.
            </div>
            
            <div class="user-info">
                <strong>Twoje dane:</strong><br>
                Imię i nazwisko: {{ $user->name }}<br>
                Email: {{ $user->email }}
            </div>
            
            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="button">
                    🚀 Ustaw hasło i rozpocznij
                </a>
            </div>
            
            <div class="warning">
                ⚠️ <strong>Ważne:</strong> Link jest ważny do {{ $expiresAt }}. 
                Po tym czasie będziesz musiał poprosić o nowy link.
            </div>
            
            <div style="margin-top: 30px; color: #6b7280; font-size: 14px;">
                <p>Po ustawieniu hasła będziesz mógł:</p>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Zalogować się do swojego panelu</li>
                    <li>Uzupełnić swój profil</li>
                    <li>Przeglądać płatności i obecności</li>
                    <li>Akceptować regulamin</li>
                </ul>
            </div>
        </div>
        
        <div class="footer">
            <p>Jeśli nie spodziewałeś się tego emaila, możesz go zignorować.</p>
            <p>Link jest bezpieczny i ważny tylko dla Ciebie.</p>
            <p>Masz pytania? Skontaktuj się z nami:</p>
            <p>📧 Email: {{ config('app.payment_reminder_email') }}</p>
            <p>☎️ Telefon: {{ config('app.payment_reminder_phone') }}</p>
            <p>🌐 Strona: {{ config('app.payment_reminder_website') }}</p>
        </div>
    </div>
</body>
</html> 