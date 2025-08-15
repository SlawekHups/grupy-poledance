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
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
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
        .user-info {
            background-color: #f3f4f6;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .user-info strong {
            color: #1f2937;
        }
        .payment-summary {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .payment-summary h3 {
            color: #dc2626;
            margin-top: 0;
        }
        .payment-details {
            background-color: #f9fafb;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .payment-details table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .payment-details th,
        .payment-details td {
            padding: 10px;
            border: 1px solid #d1d5db;
            text-align: left;
        }
        .payment-details th {
            background-color: #f3f4f6;
            font-weight: 600;
        }
        .payment-details td:last-child {
            text-align: right;
        }
        .action-required {
            background-color: #fffbeb;
            border: 1px solid #fed7aa;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .action-required h3 {
            color: #d97706;
            margin-top: 0;
        }
        .contact-info {
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .contact-info h3 {
            color: #0369a1;
            margin-top: 0;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        .urgent {
            color: #dc2626;
            font-weight: 600;
        }
        .amount {
            font-size: 18px;
            font-weight: 600;
            color: #dc2626;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💰 Przypomnienie o Płatności</h1>
        </div>
        
        <div class="content">
            {!! $content !!}
        </div>
        
        <div class="footer">
            <p>To jest wiadomość systemowa z aplikacji {{ config('app.payment_reminder_company_name') }}.</p>
            <p>Jeśli masz pytania, skontaktuj się z administratorem systemu.</p>
            <p>Wiadomość wysłana automatycznie przez system {{ config('app.payment_reminder_company_name') }}.</p>
            <p>Nie odpowiadaj na ten email - użyj kontaktu w aplikacji.</p>
        </div>
    </div>
</body>
</html>
