<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Carbon\Carbon;

class StaffUser extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $connection = 'mongodb';
    protected $collection = 'staff_users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'permissions',
        'invitation_token',
        'invitation_sent_at',
        'invitation_accepted_at',
        'last_login_at',
        'approved_by',
        'approved_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'invitation_token'
    ];

    protected $casts = [
        'permissions' => 'array',
        'invitation_sent_at' => 'datetime',
        'invitation_accepted_at' => 'datetime',
        'last_login_at' => 'datetime',
        'approved_at' => 'datetime'
    ];

    // Role-based permissions
    public static $rolePermissions = [
        'staff' => [
            'dashboard_view',
            'watering_control',
            'sensor_history'
        ],
        'manager' => [
            'dashboard_view',
            'watering_control',
            'sensor_history',
            'user_management',
            'system_settings'
        ],
        'admin' => [
            'dashboard_view',
            'watering_control',
            'sensor_history',
            'user_management',
            'audit_logs',
            'system_settings'
        ]
    ];

    public function generateInvitationToken()
    {
        $this->invitation_token = Str::random(64);
        $this->invitation_sent_at = now();
        $this->save();
        
        return $this->invitation_token;
    }

    public function getInvitationUrl()
    {
        return url("/staff/invitation/{$this->invitation_token}");
    }

    public function acceptInvitation($password)
    {
        $this->password = bcrypt($password);
        $this->status = 'pending';
        $this->invitation_accepted_at = now();
        $this->invitation_token = null;
        $this->save();
    }

    public function approve($approvedBy)
    {
        $this->status = 'active';
        $this->approved_by = $approvedBy;
        $this->approved_at = now();
        $this->save();
    }

    public function deactivate()
    {
        $this->status = 'inactive';
        $this->save();
    }

    public function reactivate()
    {
        $this->status = 'active';
        $this->save();
    }

    public function hasPermission($permission)
    {
        if ($this->role === 'admin') {
            return true;
        }

        $rolePermissions = self::$rolePermissions[$this->role] ?? [];
        $userPermissions = $this->permissions ?? [];

        return in_array($permission, array_merge($rolePermissions, $userPermissions));
    }

    public function getRolePermissions()
    {
        return self::$rolePermissions[$this->role] ?? [];
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isManager()
    {
        return in_array($this->role, ['manager', 'admin']);
    }

    public function canApproveUsers()
    {
        return $this->isManager();
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

}
