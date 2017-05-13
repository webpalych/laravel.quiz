<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Models\Role;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('users')->delete();
        DB::table('roles')->delete();

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'player']);

        $user = User::create([
            'name' => 'admin',
            'email' => 'admin@mail.com',
            'password' => Hash::make('admin@mail.com')
            ]);
        $user->roles()->attach(1);

        $user = User::create([
            'name' => 'alex',
            'email' => 'test@mail.com',
            'password' => Hash::make('test@mail.com')
        ]);
        $user->roles()->attach(2);

    }
}
