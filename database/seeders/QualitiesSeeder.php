<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QualitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $qualities = [
            ['id' => 1, 'name' => 'Normal'],
            ['id' => 2, 'name' => 'Good'],
            ['id' => 3, 'name' => 'Outstanding'],
            ['id' => 4, 'name' => 'Excellent'],
            ['id' => 5, 'name' => 'Masterpiece'],
        ];

        // Veritabanına ekleyelim
        DB::table('qualities')->insert($qualities);
    }
}
