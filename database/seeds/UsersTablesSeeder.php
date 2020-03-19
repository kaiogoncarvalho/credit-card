<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use Laravel\Passport\Client;
use App\Enums\Scope;

class UsersTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate(
            ['id' => 1],
            [
                'name'     => 'Admin',
                'email'    => 'admin@admin.com',
                'password' => Hash::make('Admin123'),
                'scopes'   => [Scope::ADMIN]
            ]
        );
        
        Client::updateOrCreate(
            [
                'id' => 1
            ],
            [
                'user_id'                => 1,
                'name'                   => 'Admin',
                'secret'                 => 'eB82RBLG47GT5RjVfOMaKhOBPyinWPFyuNUEuzYn',
                'redirect'               => env('APP_URL'),
                'personal_access_client' => false,
                'password_client'        => true,
                'revoked'                => false
            ]
        );
    }
}
