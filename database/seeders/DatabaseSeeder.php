<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $now = Carbon::now()->toDateTimeString(); // Geçerli tarih ve saat bilgisini alıyoruz

        $qualities = [
            ['id' => 1, 'name' => 'Normal', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Good', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Outstanding', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Excellent', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'name' => 'Masterpiece', 'created_at' => $now, 'updated_at' => $now],
        ];

        // Veritabanına ekleyelim
        DB::table('qualities')->insert($qualities);

        $cities = [
            ['id' => 1, 'name' => 'Bridgewatch', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Caerleon', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Black Market', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Brecilien', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'name' => 'Fort Sterling', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'name' => 'Lymhurst', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'name' => 'Thetford', 'created_at' => $now, 'updated_at' => $now],
        ];

        // Veritabanına ekleyelim
        DB::table('cities')->insert($cities);

    }
}
