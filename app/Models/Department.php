<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Department extends Model
{
    protected $fillable = [
        'name',
        'code',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the issues that belong to the department.
     */
    public function issues(): BelongsToMany
    {
        return $this->belongsToMany(Issue::class, 'department_issue')
            ->withTimestamps();
    }
}
