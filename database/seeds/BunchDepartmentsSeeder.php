<?php

use Illuminate\Database\Seeder;

/**
 * Seeds 15 departments for testing purposes
 *
 * Class BunchDepartmentsSeeder
 */
class BunchDepartmentsSeeder extends Seeder
{
    /**
     * Creates 15 departments using faker.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Department::class, 15)->create();
    }
}
