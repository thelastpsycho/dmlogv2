<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DmLogBook extends Model
{
    protected $fillable = [
        'guest_name',
        'room_number',
        'category',
        'description',
        'priority',
        'assigned_to',
        'status',
        'action_taken',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Scope a query to only include open entries.
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope a query to only include in-progress entries.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope a query to only include resolved entries.
     */
    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    /**
     * Scope a query to only include closed entries.
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }
}
