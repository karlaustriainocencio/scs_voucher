<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Claim extends Model
{
    protected $primaryKey = 'claim_id';
    protected $table = 'claims';
    protected $fillable = [
        'reference_number',
        'payee_type',
        'payee_id',
        'total_amount',
        'status',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
    ];


   

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function modeOfPayment(): BelongsTo
    {
        return $this->belongsTo(ModeOfPayment::class, 'mode_of_payment_id', 'mode_of_payment_id');
    }

    public function claimReferences(): HasMany
    {
        return $this->hasMany(ClaimReference::class, 'claim_id', 'claim_id');
    }

    public function voucher(): HasOne   
    {
        return $this->hasOne(Voucher::class, 'claim_id', 'claim_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function payee()
    {
        return $this->morphTo();
    }

    public static function generateReferenceNumber(): string
    {
        $prefix = 'C' . now()->format('ym'); // e.g. C2508

        $count = self::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count() + 1;

        return $prefix . '-' . str_pad($count, 3, '0', STR_PAD_LEFT); // e.g. C2508-001
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($claim) {
            if (empty($claim->reference_number)) {
                $claim->reference_number = self::generateReferenceNumber();
            }
            
            // Always set status to draft for new claims
            if (empty($claim->status)) {
                $claim->status = 'draft';
            }
        });

        static::updating(function ($claim) {
            // Auto-fill submitted_at when status changes to submitted
            if ($claim->isDirty('status') && $claim->status === 'submitted' && empty($claim->submitted_at)) {
                $claim->submitted_at = now();
            }
        });
    }
}