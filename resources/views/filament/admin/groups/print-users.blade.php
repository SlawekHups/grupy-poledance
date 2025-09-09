<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista grupy - {{ $group->name }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 24px; }
        h1 { margin-bottom: 8px; }
        .muted { color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px 10px; text-align: left; font-size: 14px; }
        th { background: #f9fafb; }
        .right { text-align: right; }
        .toolbar { margin-bottom: 12px; display: flex; gap: 8px; }
        .btn { background: #111827; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; }
        .btn.secondary { background: #6b7280; }
        @media print { .toolbar { display: none; } }
    </style>
    <script>
        function triggerPrint(){ window.print(); }
    </script>
    </head>
<body>
    <div class="toolbar">
        <a href="{{ route('filament.admin.resources.groups.edit', ['record' => $group->id]) }}" class="btn secondary">Powrót do grupy</a>
        <a href="#" class="btn" onclick="triggerPrint();return false;">Drukuj</a>
    </div>
    <h1>Lista użytkowników grupy</h1>
    <div class="muted">Grupa: <strong>{{ $group->name }}</strong></div>
    <div class="muted">Liczba uczestników: <strong>{{ $members->count() }}</strong></div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Imię i nazwisko</th>
                <th>Email</th>
                <th>Telefon</th>
                <th class="right">Kwota (zł)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($members as $i => $u)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->phone }}</td>
                    <td class="right">{{ number_format($u->amount, 2, ',', ' ') }}</td>
                    <td>{{ $u->is_active ? 'Aktywny' : 'Nieaktywny' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>


