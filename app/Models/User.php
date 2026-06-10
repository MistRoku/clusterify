<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_super_admin',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'last_login_at',
        'last_login_ip',
        'last_login_device',
        'failed_login_attempts',
        'locked_until',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'locked_until'      => 'datetime',
        'is_super_admin'    => 'boolean',
        'last_login_at'     => 'datetime',
    ];

    // Relationships

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_user')
                    ->withPivot('role', 'accepted_at', 'invited_by')
                    ->withTimestamps();
    }

    public function currentCompany()
    {
        $companyId = session('current_company_id');
        if ($companyId && $this->companies->contains($companyId)) {
            return Company::find($companyId);
        }
        return $this->companies()->first();
    }

    public function tasksAssigned()
    {
        return $this->belongsToMany(Task::class, 'task_assignees')
                    ->withPivot('assigned_by', 'assigned_at', 'notified_at');
    }

    public function loginHistory()
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Helper methods

    public function isGlobalSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    public function hasCompanyRole(string $role, ?int $companyId = null): bool
    {
        $companyId = $companyId ?? session('current_company_id');
        if (!$companyId) return false;

        $membership = $this->companies()->where('company_id', $companyId)->first();
        return $membership && $membership->pivot->role === $role;
    }

    public function isLockedOut(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    public function incrementFailedAttempts(): void
    {
        $this->increment('failed_login_attempts');
        if ($this->failed_login_attempts >= 5) {
            $this->locked_until = now()->addMinutes(15);
            $this->save();
        }
    }

    public function resetFailedAttempts(): void
    {
        $this->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);
    }
}
