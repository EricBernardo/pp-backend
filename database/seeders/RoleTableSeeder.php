<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        Role::firstOrCreate(['id' => Role::ADMIN], ['name' => 'admin']);
        Role::firstOrCreate(['id' => Role::USER], ['name' => 'user']);
        Role::firstOrCreate(['id' => Role::RETAILER], ['name' => 'retailer']);
    }
}
