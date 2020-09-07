<?php

use Illuminate\Database\Seeder;

/**
 * Seeds 15 users
 *
 * Class BunchUsersSeeder
 */
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
