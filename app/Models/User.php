<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Helpers\Operators\CombinedOperators\DateOperators;
use App\Helpers\Operators\CombinedOperators\NumberOperators;
use App\Helpers\Operators\CombinedOperators\StringOperators;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    use HasFactory, SoftDeletes;

    public const relationMethods = ['teacherCourses', 'enrollments', 'grades', 'cohortMembers'];

    public const filterable = [
        'username' => StringOperators::class,
        'email' => StringOperators::class,
        'email_verified_at' => DateOperators::class,
        'lastname' => StringOperators::class,
        'firstname' => StringOperators::class,
        'nationality_id' => NumberOperators::class,
        'birthdate' => DateOperators::class,
        'address' => StringOperators::class,
        'postal_code' => StringOperators::class,
        'address_country_id' => NumberOperators::class,
        'phone' => StringOperators::class,
        'created_at' => DateOperators::class,
        'updated_at' => DateOperators::class,
        'site_role_id' => NumberOperators::class,
    ];

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
        'site_role_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birthdate' => 'date',
        'email_verified_at' => 'datetime',
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

    public function nationality() {
        return $this->belongsTo(Country::class, 'nationality_country_id');
    }

    public function addressCountry() {
        return $this->belongsTo(Country::class, 'address_country_id');
    }

    public function siteRole() {
        return $this->belongsTo(SiteRole::class);
    }

    public function teacherCourses() {
        return $this->hasMany(Course::class, 'teacher_user_id');
    }

    public function cohortMembers() {
        return $this->hasMany(CohortMember::class)
            ->with('cohort');
    }

    public function enrollments() {
        return $this->hasMany(Enrollment::class)
            ->with('formation');
    }

    public function grades() {
        return $this->hasMany(Grade::class)
            ->with('course');
    }
}
