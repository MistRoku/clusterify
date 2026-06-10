<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'subdomain',
        'is_active',
        'created_by',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings'  => 'array',
        'deleted_at'=> 'datetime',
    ];

    // Relationships

    public function users()
    {
        return $this->belongsToMany(User::class, 'company_user')
                    ->withPivot('role', 'accepted_at', 'invited_by')
                    ->withTimestamps();
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper methods

    public function addUser(User $user, string $role = 'member', ?User $invitedBy = null): void
    {
        $this->users()->attach($user->id, [
            'role'        => $role,
            'invited_by'  => $invitedBy?->id,
            'accepted_at' => now(),
        ]);
    }

    public function removeUser(User $user): void
    {
        $this->users()->detach($user->id);
    }

    public function updateUserRole(User $user, string $role): void
    {
        $this->users()->updateExistingPivot($user->id, ['role' => $role]);
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function suspend(): void
    {
        $this->is_active = false;
        $this->save();
    }

    public function activate(): void
    {
        $this->is_active = true;
        $this->save();
    }
}
