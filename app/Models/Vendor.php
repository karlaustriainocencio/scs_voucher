<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Vendor extends Model
{
    //
    protected $primaryKey = 'vendor_id';

    protected $table = 'vendors';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'company',
    ];
    public function claims(): MorphMany
    {
        return $this->morphMany(Claim::class, 'payee');
    }

}
