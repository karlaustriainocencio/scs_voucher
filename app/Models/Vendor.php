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
        'address',
        'contact_name',
        'contact_number'
    ];
    public function claims(): MorphMany
    {
        return $this->morphMany(Claim::class, 'payee');
    }

}
