<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMode extends Model
{
    protected $primaryKey = 'payment_mode_id';

    protected $table = 'payment_modes';

    protected $fillable = [
        'name',
        'description',
    ];

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class, 'payment_mode_id', 'payment_mode_id');
    }
}
