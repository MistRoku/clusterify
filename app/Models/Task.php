<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'parent_task_id',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'due_time',
        'estimated_hours',
        'actual_hours',
        'recurring_rule',
        'created_by',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'due_date'        => 'date',
        'due_time'        => 'datetime:H:i',
        'estimated_hours' => 'decimal:2',
        'actual_hours'    => 'decimal:2',
        'started_at'      => 'datetime',
        'completed_at'    => 'datetime',
        'deleted_at'      => 'datetime',
    ];

    public static function statuses(): array
    {
        return ['todo', 'in_progress', 'in_review', 'blocked', 'done'];
    }

    public static function priorities(): array
    {
        return ['low', 'medium', 'high', 'urgent'];
    }

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'task_assignees')
                    ->withPivot('assigned_by', 'assigned_at', 'notified_at')
                    ->withTimestamps();
    }

    public function statusChanges()
    {
        return $this->hasMany(TaskStatusChange::class)->latest();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    // Scopes
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                     ->where('status', '!=', 'done');
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->whereHas('assignees', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    // Boot events
    protected static function booted()
    {
        static::updated(function ($task) {
            if ($task->isDirty('status')) {
                $userId = Auth::id();

                TaskStatusChange::create([
                    'task_id'     => $task->id,
                'from_status' => $task->getOriginal('status'),
                'to_status'   => $task->status,
                'changed_by'  => $userId,
            ]);

            if ($task->status === 'in_progress' && !$task->started_at) {
                $task->started_at = now();
                $task->saveQuietly();
            }

            if ($task->status === 'done' && !$task->completed_at) {
                $task->completed_at = now();
                $task->saveQuietly();
            }
        }
    });
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'todo'        => 'To Do',
            'in_progress' => 'In Progress',
            'in_review'   => 'In Review',
            'blocked'     => 'Blocked',
            'done'        => 'Done',
            default       => ucfirst($this->status),
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return ucfirst($this->priority);
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'done';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'done';
    }
}
