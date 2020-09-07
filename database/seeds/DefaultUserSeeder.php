<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds default user for testing purposes
 *
 * Class DefaultUserSeeder
 */
class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $name = config('app.default_user.name');
        $email = config('app.default_user.email');
        $password = config('app.default_user.password');
        if (!empty($name) && !empty($email)&& !empty($password)) {
            DB::table('users')->insert([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($password),
            ]);
        }
    }
}
