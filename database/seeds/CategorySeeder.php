<?php

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::updateOrCreate(
            ['id' => 1],
            [
                'name'     => 'Silver'
            ]
        );
    
        Category::updateOrCreate(
            ['id' => 2],
            [
                'name'     => 'Gold'
            ]
        );
    
        Category::updateOrCreate(
            ['id' => 3],
            [
                'name'     => 'Platinum'
            ]
        );
    
        Category::updateOrCreate(
            ['id' => 4],
            [
                'name'     => 'Black'
            ]
        );
    }
}
