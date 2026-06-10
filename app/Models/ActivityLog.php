<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'loggable_type',
        'loggable_id',
        'user_id',
        'event',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get the parent loggable model (polymorphic).
     */
    public function loggable()
    {
        return $this->morphTo();
    }

    /**
     * Get the user who performed the action.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
