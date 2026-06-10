<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TaskAssignee extends Model
{
        use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'task_assignees';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'task_id',
        'user_id',
        'assigned_by',
        'assigned_at',
        'notified_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'assigned_at' => 'datetime',
        'notified_at' => 'datetime',
    ];

    /**
     * Get the task that owns the assignee record.
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the user (assignee).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who assigned the task.
     */
    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
