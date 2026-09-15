<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Requisition;

class BackfillTrackingNumbersSeeder extends Seeder
{
    public function run(): void
    {
        $requisitions = Requisition::with('user')->whereNull('tracking_number')->get();

        foreach ($requisitions as $r) {
            $prefix = strtoupper($r->request_type) === 'MAJOR' ? 'MAJ' : 'MIN';
            $tierCode = strtoupper($r->request_type) === 'MAJOR' ? '10' : '20';
            $dept = strtoupper($r->user->department ?? 'HTC');
            $uniqueNumber = $tierCode . str_pad($r->id, 6, '0', STR_PAD_LEFT);

            $r->tracking_number = "{$prefix}-{$dept}-{$uniqueNumber}";
            $r->saveQuietly();
        }
    }
}
