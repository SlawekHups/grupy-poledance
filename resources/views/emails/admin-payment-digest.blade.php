<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #374151; background-color: #f9fafb; margin: 0; padding: 20px; }
        .container { max-width: 860px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; }
        .header { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: white; padding: 24px; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 600; }
        .content { padding: 24px; }
        .stat-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
        .stat { background: #f3f4f6; border-radius: 8px; padding: 14px; text-align: center; }
        .stat .label { color: #6b7280; font-size: 12px; }
        .stat .value { font-size: 18px; font-weight: 700; color: #111827; }
        .group { border: 1px solid #e5e7eb; border-radius: 8px; margin: 16px 0; }
        .group h3 { margin: 0; padding: 12px 16px; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-size: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #e5e7eb; }
        th { background: #f9fafb; text-align: left; }
        td.amount { text-align: right; }
        .footer { background: #f9fafb; padding: 16px 24px; color: #6b7280; font-size: 14px; }
        .muted { color: #6b7280; }
    </style>
    </head>
<body>
    <div class="container">
        <div class="header">
            <h1>📬 Dzienne zestawienie zaległości płatniczych</h1>
        </div>
        <div class="content">
            <p class="muted">Dzień: <strong>{{ $digest['day_name'] }}</strong>, data: <strong>{{ $digest['date'] }}</strong></p>

            <div class="stat-grid">
                <div class="stat"><div class="label">Grupy</div><div class="value">{{ $digest['stats']['groups_count'] }}</div></div>
                <div class="stat"><div class="label">Użytkownicy</div><div class="value">{{ $digest['stats']['users_count'] }}</div></div>
                <div class="stat"><div class="label">Zaległe miesiące</div><div class="value">{{ $digest['stats']['unpaid_count'] }}</div></div>
                <div class="stat"><div class="label">Suma zaległości</div><div class="value">{{ number_format($digest['stats']['total_amount'], 2, ',', ' ') }} zł</div></div>
            </div>

            @forelse ($digest['groups'] as $group)
                <div class="group">
                    <h3>Grupa: {{ $group['name'] }} (osób: {{ $group['users_count'] }})</h3>
                    @if(count($group['users']) === 0)
                        <p class="muted" style="padding: 12px 16px;">Brak zalegających w tej grupie.</p>
                    @else
                        <table>
                            <thead>
                                <tr>
                                    <th>Użytkownik</th>
                                    <th>Mail</th>
                                    <th>Nieopłacone miesiące</th>
                                    <th class="amount">Kwota</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($group['users'] as $user)
                                    <tr>
                                        <td>{{ $user['name'] }}</td>
                                        <td>{{ $user['email'] }}</td>
                                        <td>{{ implode(', ', $user['months']) }}</td>
                                        <td class="amount">{{ number_format($user['amount'], 2, ',', ' ') }} zł</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            @empty
                <p>Brak grup do raportowania na dzisiaj.</p>
            @endforelse
        </div>
        <div class="footer">
            <p>Wiadomość automatyczna z systemu {{ config('app.payment_reminder_company_name') }}.</p>
        </div>
    </div>
</body>
</html>


