<?php

use Illuminate\Database\Seeder;

class BunchUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class, 15)->create();
    }
}
