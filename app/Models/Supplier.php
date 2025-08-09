<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Supplier extends Model
{
    protected $primaryKey = 'supplier_id';

    protected $table = 'suppliers';
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
