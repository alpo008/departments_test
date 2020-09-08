<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            DefaultUserSeeder::class,
            BunchUsersSeeder::class,
            BunchDepartmentsSeeder::class
        ]);
    }
}
