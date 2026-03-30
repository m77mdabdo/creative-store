<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Audit Logs Print</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #111;
            margin: 20px;
            font-size: 12px;
        }
        h1 {
            margin-bottom: 5px;
        }
        .meta {
            margin-bottom: 20px;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            vertical-align: top;
            text-align: left;
        }
        th {
            background: #f5f5f5;
        }
        .muted {
            color: #666;
            font-size: 11px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom:20px;">
        <button onclick="window.print()">Print</button>
    </div>

    <h1>Audit Logs</h1>
    <div class="meta">
        Generated at: {{ now()->format('Y-m-d h:i A') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Action</th>
                <th>Auditable Type</th>
                <th>Auditable ID</th>
                <th>IP Address</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($auditLogs as $auditLog)
                <tr>
                    <td>{{ $auditLog->id }}</td>
                    <td>
                        {{ $auditLog->user?->name ?? 'System / Unknown' }}
                        @if($auditLog->user?->email)
                            <div class="muted">{{ $auditLog->user->email }}</div>
                        @endif
                    </td>
                    <td>{{ $auditLog->action }}</td>
                    <td>{{ class_basename($auditLog->auditable_type) }}</td>
                    <td>{{ $auditLog->auditable_id }}</td>
                    <td>{{ $auditLog->ip_address ?: '—' }}</td>
                    <td>{{ optional($auditLog->created_at)->format('Y-m-d h:i A') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
