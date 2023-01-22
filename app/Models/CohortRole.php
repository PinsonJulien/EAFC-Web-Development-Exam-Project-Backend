<?php

namespace App\Models;

use App\Helpers\Operators\CombinedOperators\DateOperators;
use App\Helpers\Operators\CombinedOperators\StringOperators;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CohortRole extends Model
{
    use HasFactory, SoftDeletes;

    public const relationMethods = ['cohortMembers'];

    public const filterable = [
        'name' => StringOperators::class,
        'created_at' => DateOperators::class,
        'updated_at' => DateOperators::class,
    ];

    protected $fillable = [
        'name',
    ];

    public function cohortMembers() {
        return $this->hasMany(CohortMember::class)
            ->with(['cohort', 'user']);
    }
}
