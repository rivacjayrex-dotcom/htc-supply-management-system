<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'school_id',
        'department',    // Keep temporarily so old views don't crash
        'department_id', // <--- ADD THIS
        'email',
        'password',
        'role',
        'is_approved',
    ];

    /**
     * The department/college the user belongs to.
     */
    public function academicDepartment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

        // Check if user is an Admin
    public function isAdmin() {
        return $this->role === 'admin';
    }

    // Check if user is SMO In-charge
    public function isSMO() {
        return $this->role === 'smo';
    }

    // Check if user is an Employee
    public function isEmployee() {
        return $this->role === 'employee';
    }

    public function isDeptHead() { return $this->role === 'dept_head'; }
    public function isVP() { return $this->role === 'vp'; }
    public function isProvost() { return $this->role === 'provost'; }
    public function isPresident() { return $this->role === 'president'; }
}

