<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zaproszenie do ustawienia nowego hasła</title>
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
        .cta-button { 
            display: inline-block; 
            background-color: #10b981; 
            color: white; 
            padding: 12px 24px; 
            text-decoration: none; 
            border-radius: 6px; 
            font-weight: 600; 
            margin: 20px 0; 
        }
        .cta-button:hover { 
            background-color: #059669; 
        }
        .warning { 
            background-color: #fef3c7; 
            border: 1px solid #f59e0b; 
            border-radius: 6px; 
            padding: 15px; 
            margin: 20px 0; 
            color: #92400e; 
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
            <h1>🔐 Resetowanie hasła</h1>
        </div>
        
        <div class="content">
            <div class="welcome">
                Cześć <strong>{{ $user->name }}</strong>!
            </div>
            
            <div class="message-content">
                <p>Administrator <strong>{{ $adminName }}</strong> zresetował Twoje hasło w systemie <strong>Grupy Poledance</strong>.</p>
                
                <p>Otrzymujesz nowe zaproszenie do ustawienia nowego hasła.</p>
            </div>
            
            <div class="user-info">
                <strong>Twoje dane:</strong><br>
                Imię i nazwisko: {{ $user->name }}<br>
                Email: {{ $user->email }}
            </div>
            
            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="cta-button">
                    🚀 Ustaw nowe hasło
                </a>
            </div>
            
            <div class="warning">
                <strong>⚠️ Ważne informacje:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Link jest ważny przez <strong>72 godziny</strong> (do {{ $expiresAt->format('d.m.Y H:i') }})</li>
                    <li>Po wygaśnięciu linku skontaktuj się z administratorem</li>
                    <li>Link może być użyty tylko raz</li>
                </ul>
            </div>
            
            <p>Jeśli nie spodziewałeś się tego emaila, skontaktuj się z administratorem systemu.</p>
        </div>
        
        <div class="footer">
            <p>📧 Wiadomość od Grupy Poledance</p>
            <p>To jest wiadomość systemowa z aplikacji Grupy Poledance.</p>
            <p>Jeśli masz pytania, skontaktuj się z administratorem systemu.</p>
            <p><small>Wiadomość wysłana automatycznie przez system Grupy Poledance.</small></p>
        </div>
    </div>
</body>
</html>
