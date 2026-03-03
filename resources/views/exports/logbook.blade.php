<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logbook Report</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #1a1a1a;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0369a1;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #0369a1;
            margin: 0 0 5px 0;
            font-size: 22px;
        }
        .header .meta {
            color: #666;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th {
            background: #0369a1;
            color: white;
            font-weight: 600;
            padding: 8px 6px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }
        td {
            padding: 6px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }
        tr:nth-child(even) {
            background: #f9fafb;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: 500;
        }
        .badge-open { background: #fef3c7; color: #92400e; }
        .badge-closed { background: #d1fae5; color: #065f46; }
        .badge-urgent { background: #fee2e2; color: #991b1b; }
        .badge-high { background: #fef3c7; color: #92400e; }
        .badge-medium { background: #e5e7eb; color: #374151; }
        .badge-low { background: #e5e7eb; color: #374151; }
        .id-col { width: 40px; font-family: monospace; font-size: 10px; color: #666; }
        .title-col { max-width: 180px; }
        .dept-col { max-width: 80px; }
        .type-col { max-width: 80px; }
        .status-col { width: 60px; }
        .date-col { width: 70px; font-size: 10px; color: #666; }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 9px;
            color: #666;
            text-align: center;
        }
        .summary {
            margin-bottom: 20px;
            padding: 10px;
            background: #f0f9ff;
            border-left: 3px solid #0369a1;
        }
        .summary strong {
            color: #0369a1;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DM-Log Logbook Report</h1>
        <div class="meta">
            @if(isset($filters['date_from']) && isset($filters['date_to']))
                {{ \Carbon\Carbon::parse($filters['date_from'])->format('M d, Y') }} -
                {{ \Carbon\Carbon::parse($filters['date_to'])->format('M d, Y') }}
            @else
                {{ now()->format('M d, Y') }}
            @endif
            • Generated: {{ now()->format('M d, Y H:i') }}
        </div>
    </div>

    <div class="summary">
        <strong>Total Issues:</strong> {{ $issues->count() }} |
        <strong>Open:</strong> {{ $issues->whereNull('closed_at')->count() }} |
        <strong>Closed:</strong> {{ $issues->whereNotNull('closed_at')->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th class="id-col">ID</th>
                <th>Title</th>
                <th class="dept-col">Departments</th>
                <th class="type-col">Type</th>
                <th class="status-col">Status</th>
                <th class="date-col">Created</th>
                <th class="date-col">Closed</th>
            </tr>
        </thead>
        <tbody>
            @foreach($issues as $issue)
                <tr>
                    <td class="id-col">#{{ $issue->id }}</td>
                    <td class="title-col">
                        <strong>{{ $issue->title }}</strong>
                        @if($issue->description)
                            <div style="font-size: 9px; color: #666; margin-top: 2px;">
                                {{ Str::limit(strip_tags($issue->description), 100) }}
                            </div>
                        @endif
                    </td>
                    <td class="dept-col">
                        @foreach($issue->departments as $dept)
                            <span class="badge">{{ $dept->name }}</span><br>
                        @endforeach
                    </td>
                    <td class="type-col">
                        @foreach($issue->issueTypes as $type)
                            <span class="badge">{{ $type->name }}</span><br>
                        @endforeach
                    </td>
                    <td class="status-col">
                        <span class="badge badge-{{ $issue->closed_at ? 'closed' : 'open' }}">
                            {{ $issue->closed_at ? 'Closed' : 'Open' }}
                        </span>
                    </td>
                    <td class="date-col">{{ $issue->created_at?->format('M d, Y') }}</td>
                    <td class="date-col">{{ $issue->closed_at?->format('M d, Y') ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        DM-Log Issue Tracking System • {{ config('app.name') }} •
        Total: {{ $issues->count() }} issues
    </div>
</body>
</html>
