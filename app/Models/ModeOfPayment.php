<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModeOfPayment extends Model
{
    protected $primaryKey = 'mode_of_payment_id';

    protected $table = 'mode_of_payments';

    protected $fillable = [
        'name',
        'description',
    ];

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class, 'mode_of_payment_id', 'mode_of_payment_id');    
    }
}
