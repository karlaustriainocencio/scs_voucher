<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimReference extends Model
{
    protected $primaryKey = 'claim_reference_id';
    protected $table = 'claim_references';
    protected $fillable = [
        'claim_id',
        'category_id',
        'description',
        'expense_date',
        'amount',
        'receipt_path',
    ];

    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class, 'claim_id', 'claim_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }
}
