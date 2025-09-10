<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Employee extends Model
{
    protected $primaryKey = 'employee_id';

    protected $table = 'employees';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'address',
        'department_id',
        'company',
        'role', // Temporary field for form processing
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }


    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class, 'employee_id', 'employee_id');
    }

    public function payeeClaims(): MorphMany
    {
        return $this->morphMany(Claim::class, 'payee');
    }

}
