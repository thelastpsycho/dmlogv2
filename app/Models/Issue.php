<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Issue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'location',
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
        'verified_at',
        'verified_by_user_id',
        'issue_type_id',
    ];

    protected function casts(): array
    {
        return [
            'checkin_date' => 'date',
            'checkout_date' => 'date',
            'issue_date' => 'date',
            'closed_at' => 'datetime',
            'verified_at' => 'datetime',
            'recovery_cost' => 'integer',
        ];
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
     * Get the issue types that belong to the issue (many-to-many via pivot).
     */
    public function issueTypes(): BelongsToMany
    {
        return $this->belongsToMany(IssueType::class, 'issue_issue_type')
            ->withTimestamps();
    }

    /**
     * Get the primary issue type for the issue (many-to-one).
     */
    public function issueType(): BelongsTo
    {
        return $this->belongsTo(IssueType::class);
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
     * Get the user who verified the issue.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
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
    public function activityLogs(): MorphMany
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
     * Scope a query to only include verified issues.
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    /**
     * Check if issue is closed.
     */
    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /**
     * Check if issue is verified.
     */
    public function isVerified(): bool
    {
        return $this->status === 'verified';
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
     * Verify the issue.
     */
    public function verify(int $verifiedByUserId): void
    {
        $this->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by_user_id' => $verifiedByUserId,
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
            'verified_at' => null,
            'verified_by_user_id' => null,
        ]);
    }
}
