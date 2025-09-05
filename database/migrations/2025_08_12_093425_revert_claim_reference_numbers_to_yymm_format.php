<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Revert SCS claims to YYMM format with separate running numbers
        $scsClaims = DB::table('claims')
            ->where('company', 'SCS')
            ->orderBy('created_at')
            ->get();

        $scsCounter = 1;
        foreach ($scsClaims as $claim) {
            DB::table('claims')
                ->where('claim_id', $claim->claim_id)
                ->update([
                    'reference_number' => 'SCS2508-' . str_pad($scsCounter, 3, '0', STR_PAD_LEFT)
                ]);
            $scsCounter++;
        }

        // Revert CIS claims to YYMM format with separate running numbers
        $cisClaims = DB::table('claims')
            ->where('company', 'CIS')
            ->orderBy('created_at')
            ->get();

        $cisCounter = 1;
        foreach ($cisClaims as $claim) {
            DB::table('claims')
                ->where('claim_id', $claim->claim_id)
                ->update([
                    'reference_number' => 'CIS2508-' . str_pad($cisCounter, 3, '0', STR_PAD_LEFT)
                ]);
            $cisCounter++;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to full year format
        $scsClaims = DB::table('claims')
            ->where('company', 'SCS')
            ->orderBy('created_at')
            ->get();

        $scsCounter = 1;
        foreach ($scsClaims as $claim) {
            DB::table('claims')
                ->where('claim_id', $claim->claim_id)
                ->update([
                    'reference_number' => 'SCS-2025-' . str_pad($scsCounter, 3, '0', STR_PAD_LEFT)
                ]);
            $scsCounter++;
        }

        $cisClaims = DB::table('claims')
            ->where('company', 'CIS')
            ->orderBy('created_at')
            ->get();

        $cisCounter = 1;
        foreach ($cisClaims as $claim) {
            DB::table('claims')
                ->where('claim_id', $claim->claim_id)
                ->update([
                    'reference_number' => 'CIS-2025-' . str_pad($cisCounter, 3, '0', STR_PAD_LEFT)
                ]);
            $cisCounter++;
        }
    }
};
