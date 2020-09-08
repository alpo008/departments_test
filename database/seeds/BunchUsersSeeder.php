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
     * Seeds 15 users for testing purposes.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class, 15)->create();
    }
}
