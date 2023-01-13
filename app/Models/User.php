<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'lastname',
        'firstname',
        'nationality_country_id',
        'birthdate',
        'address',
        'postal_code',
        'address_country_id',
        'phone',
        'picture',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function nationality() {
        return $this->belongsTo(Country::class, 'nationality_country_id');
    }

    public function addressCountry() {
        return $this->belongsTo(Country::class, 'address_country_id');
    }

    public function teacherCourses() {
        return $this->hasMany(Course::class, 'teacher_user_id');
    }

    public function cohorts() {
        return $this->belongsToMany(Cohort::class, 'users_cohorts');
    }
}
