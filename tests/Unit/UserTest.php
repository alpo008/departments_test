<?php

namespace Tests\Unit;

use App\Department;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */

    private $user;
    private $users;

    /**
     * @inheritDoc
     */
    protected function setUp() :void
    {
        parent::setUp();

        $this->user = User::first();
        $this->users = User::all();
    }

    /**
     * Testing class App\User
     */
    public function testUserClass()
    {
        $rules = User::rules();
        $this->assertClassHasAttribute('fillable', User::class);
        $this->assertClassHasAttribute('hidden', User::class);
        $this->assertClassHasAttribute('casts', User::class);
        $this->assertTrue(is_array($rules));
        $this->assertCount(3, $rules);
    }

    /**
     * Testing instance of class App\User
     */
    public function testUser()
    {
        $this->assertTrue($this->user instanceof User);
        $this->assertTrue($this->users instanceof Collection);
        $this->assertEquals('Tester', $this->user->name);
        $this->assertTrue($this->user->departments  instanceof Collection);
        $this->assertGreaterThan(1, count($this->user->departments));

        foreach ($this->user->departments as $department) {
            $this->assertTrue($department instanceof Department);
            $this->assertTrue($department->pivot instanceof Pivot);
            $this->assertEquals('department_user', $department->pivot->getTable());
        }
    }

    /**
     * Testing App\User[] Collection
     */
    public function testUsers()
    {
        $this->assertTrue($this->users instanceof Collection);
        foreach ($this->users as $user) {
            $this->assertTrue($user instanceof User);
        }
    }
}
