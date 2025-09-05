<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Voucher extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'voucher_id';

    protected $table = 'vouchers';

    protected $fillable = [
        'claim_id',
        'voucher_number',
        'mode_of_payment_id',
        'payment_date',
        'remarks',
        'approved_by',
        'created_by',
        'company',
    ];

    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class, 'claim_id', 'claim_id');
    }

    public function modeOfPayment(): BelongsTo
    {
        return $this->belongsTo(ModeOfPayment::class, 'mode_of_payment_id', 'mode_of_payment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'user_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'user_id');
    }


    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public static function generateVoucherNumber(): string
    {
        $prefix = 'V' . now()->format('ym'); // e.g. V2508

        $count = self::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count() + 1;

        return $prefix . '-' . str_pad($count, 3, '0', STR_PAD_LEFT); // e.g. V2508-001
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($voucher) {
            if (empty($voucher->voucher_number)) {
                $voucher->voucher_number = self::generateVoucherNumber();
            }
        });
    }
}
