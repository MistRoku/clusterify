<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskStatusChange extends Model
{
        use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'task_id',
        'from_status',
        'to_status',
        'changed_by',
        'comment',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the task that this status change belongs to.
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the user who changed the status.
     */
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
