<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue #{{ $issue->id }}</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #1a1a1a;
            background: #fff;
            margin: 0;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #0369a1;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #0369a1;
            margin: 0 0 5px 0;
            font-size: 24px;
        }
        .header .meta {
            color: #666;
            font-size: 11px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: 600;
            font-size: 14px;
            color: #0369a1;
            margin-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 4px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 8px;
        }
        .info-label {
            font-weight: 500;
            color: #666;
        }
        .info-value {
            color: #1a1a1a;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 500;
        }
        .badge-open { background: #fef3c7; color: #92400e; }
        .badge-closed { background: #d1fae5; color: #065f46; }
        .badge-urgent { background: #fee2e2; color: #991b1b; }
        .badge-high { background: #fef3c7; color: #92400e; }
        .badge-medium { background: #e5e7eb; color: #374151; }
        .badge-low { background: #e5e7eb; color: #374151; }
        .description {
            background: #f9fafb;
            padding: 12px;
            border-radius: 6px;
            border-left: 3px solid #0369a1;
            margin-top: 8px;
        }
        .comments {
            margin-top: 15px;
        }
        .comment {
            background: #f9fafb;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
        }
        .comment-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 11px;
            color: #666;
        }
        .comment-author {
            font-weight: 500;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Issue #{{ $issue->id }}</h1>
        <div class="meta">
            DM-Log Issue Tracking System • Generated: {{ now()->format('M d, Y H:i') }}
        </div>
    </div>

    <div class="section">
        <div class="section-title">Overview</div>
        <div class="info-grid">
            <div class="info-label">Title:</div>
            <div class="info-value"><strong>{{ $issue->title }}</strong></div>

            <div class="info-label">Status:</div>
            <div class="info-value">
                <span class="badge badge-{{ $issue->closed_at ? 'closed' : 'open' }}">
                    {{ $issue->closed_at ? 'Closed' : 'Open' }}
                </span>
            </div>

            <div class="info-label">Priority:</div>
            <div class="info-value">
                @if($issue->priority)
                    <span class="badge badge-{{ $issue->priority }}">
                        {{ ucfirst($issue->priority) }}
                    </span>
                @else
                    <span class="info-value">-</span>
                @endif
            </div>

            <div class="info-label">Severity:</div>
            <div class="info-value">
                @if($issue->severity)
                    <span class="badge badge-{{ $issue->severity }}">
                        {{ ucfirst($issue->severity) }}
                    </span>
                @else
                    <span class="info-value">-</span>
                @endif
            </div>

            <div class="info-label">Created:</div>
            <div class="info-value">{{ $issue->created_at?->format('M d, Y H:i') }}</div>

            @if($issue->closed_at)
                <div class="info-label">Closed:</div>
                <div class="info-value">{{ $issue->closed_at?->format('M d, Y H:i') }}</div>
            @endif

            <div class="info-label">Created By:</div>
            <div class="info-value">{{ $issue->createdBy?->name ?? 'N/A' }}</div>

            @if($issue->assignedTo)
                <div class="info-label">Assigned To:</div>
                <div class="info-value">{{ $issue->assignedTo->name }}</div>
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-title">Departments</div>
        <div class="info-value">
            @foreach($issue->departments as $dept)
                <span class="badge">{{ $dept->name }}</span>{{ !$loop->last ? ' ' : '' }}
            @endforeach
        </div>
    </div>

    <div class="section">
        <div class="section-title">Issue Types</div>
        <div class="info-value">
            @foreach($issue->issueTypes as $type)
                <span class="badge">{{ $type->name }}</span>{{ !$loop->last ? ' ' : '' }}
            @endforeach
        </div>
    </div>

    @if($issue->description)
        <div class="section">
            <div class="section-title">Description</div>
            <div class="description">
                {!! nl2br(e($issue->description)) !!}
            </div>
        </div>
    @endif

    @if($issue->resolution)
        <div class="section">
            <div class="section-title">Resolution</div>
            <div class="description">
                {!! nl2br(e($issue->resolution)) !!}
            </div>
        </div>
    @endif

    @if($issue->comments && $issue->comments->count() > 0)
        <div class="section">
            <div class="section-title">Comments ({{ $issue->comments->count() }})</div>
            <div class="comments">
                @foreach($issue->comments as $comment)
                    <div class="comment">
                        <div class="comment-header">
                            <span class="comment-author">{{ $comment->user->name }}</span>
                            <span>{{ $comment->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div class="comment-body">{!! nl2br(e($comment->content)) !!}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="footer">
        DM-Log Issue Tracking System • {{ config('app.name') }} • {{ $issue->id }}
    </div>
</body>
</html>
