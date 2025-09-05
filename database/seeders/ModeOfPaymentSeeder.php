<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ModeOfPayment;

class ModeOfPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modeOfPayments = [
            [
                'name' => 'Bank Transfer',
                'description' => 'Direct bank transfer to payee account'
            ],
            [
                'name' => 'Cash',
                'description' => 'Cash payment'
            ],
            [
                'name' => 'Check',
                'description' => 'Bank check payment'
            ],
            [
                'name' => 'Credit Card',
                'description' => 'Credit card payment'
            ],
            [
                'name' => 'PayPal',
                'description' => 'PayPal online payment'
            ],
            [
                'name' => 'Wire Transfer',
                'description' => 'International wire transfer'
            ],
            [
                'name' => 'Mobile Payment',
                'description' => 'Mobile payment apps (PayNow, etc.)'
            ],
        ];

        foreach ($modeOfPayments as $modeOfPayment) {
            ModeOfPayment::create($modeOfPayment);
        }
    }
}
