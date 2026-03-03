<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Issue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'description',
        'name',
        'room_number',
        'checkin_date',
        'checkout_date',
        'issue_date',
        'source',
        'nationality',
        'contact',
        'recovery',
        'recovery_cost',
        'training',
        'priority',
        'status',
        'created_by',
        'updated_by',
        'assigned_to_user_id',
        'closed_at',
        'closed_by_user_id',
        'issue', // Legacy column for title
    ];

    protected function casts(): array
    {
        return [
            'checkin_date' => 'date',
            'checkout_date' => 'date',
            'issue_date' => 'date',
            'closed_at' => 'datetime',
            'recovery_cost' => 'integer',
        ];
    }

    /**
     * Get the title of the issue (uses 'issue' column).
     */
    public function getTitleAttribute(): string
    {
        return $this->attributes['issue'] ?? '';
    }

    /**
     * Set the title of the issue (sets 'issue' column).
     */
    public function setTitleAttribute(string $value): void
    {
        $this->attributes['issue'] = $value;
    }

    /**
     * Get the departments that belong to the issue.
     */
    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'department_issue')
            ->withTimestamps();
    }

    /**
     * Get the user who created the issue.
     * Note: created_by is a varchar in the legacy table containing user IDs.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Get the issue types that belong to the issue.
     */
    public function issueTypes(): BelongsToMany
    {
        return $this->belongsToMany(IssueType::class, 'issue_issue_type')
            ->withTimestamps();
    }

    /**
     * Get the user who updated the issue.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who is assigned to the issue.
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    /**
     * Get the user who closed the issue.
     */
    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by_user_id');
    }

    /**
     * Get the comments for the issue.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(IssueComment::class);
    }

    /**
     * Get the activity logs for the issue.
     */
    public function activityLogs(): HasMany
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }

    /**
     * Scope a query to only include open issues.
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope a query to only include closed issues.
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * Check if issue is closed.
     */
    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /**
     * Close the issue.
     */
    public function close(int $closedByUserId): void
    {
        $this->update([
            'status' => 'closed',
            'closed_at' => now(),
            'closed_by_user_id' => $closedByUserId,
        ]);
    }

    /**
     * Reopen the issue.
     */
    public function reopen(): void
    {
        $this->update([
            'status' => 'open',
            'closed_at' => null,
            'closed_by_user_id' => null,
        ]);
    }
}
