<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MemberLevel;

class MemberLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MemberLevel::insert([
            [
                'nama_level'    => 'Bronze',
                'diskon_persen' => 10.00,
            ],
            [
                'nama_level'    => 'Silver',
                'diskon_persen' => 20.00,
            ],
            [
                'nama_level'    => 'Gold',
                'diskon_persen' => 30.00,
            ],
        ]);
    }
}
