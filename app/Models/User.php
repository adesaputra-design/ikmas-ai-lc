<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'whatsapp_number',
        'alumni_year',
        'bio',
        'role',
        'status',
        'permissions',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    public function isSubscriber(): bool
    {
        return $this->role === 'subscriber';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (!$this->isStaff()) {
            return false;
        }

        return is_array($this->permissions) && in_array($permission, $this->permissions, true);
    }

    public function getRoleBadgeAttribute(): array
    {
        return match ($this->role) {
            'admin'      => ['label' => 'Administrator', 'class' => 'badge-primary'],
            'staff'      => ['label' => 'Staf Pengurus', 'class' => 'badge-cyan'],
            'subscriber' => ['label' => 'Subscriber',    'class' => 'badge-amber'],
            default      => ['label' => 'Member Alumni', 'class' => 'badge-emerald'],
        };
    }

    public function showcases(): HasMany
    {
        return $this->hasMany(Showcase::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }
}
