<?php

namespace Tests\Unit;

use App\Department;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Tests\TestCase;

class DepartmentTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */

    private $departments;

    /**
     * @inheritDoc
     */
    protected function setUp() :void
    {
        parent::setUp();

        $this->departments = Department::all();
    }

    /**
     * Testing class App\Department
     */
    public function testDepartmentClass()
    {
        $rules = Department::rules();
        $this->assertClassHasAttribute('fillable', Department::class);
        $this->assertTrue(is_array($rules));
        $this->assertCount(3, $rules);
    }

    /**
     * Testing App\Department[] Collection
     */
    public function testDepartments()
    {
        $this->assertTrue($this->departments instanceof Collection);
        foreach ($this->departments as $department) {
            $this->assertTrue($department instanceof Department);
            if ($logo = $department->logo) {
                $this->assertTrue(is_file(public_path($logo)));
            }
            if ($users = $department->users) {
                $this->assertTrue($users instanceof Collection);
                foreach ($users as $user) {
                    $this->assertTrue($user instanceof User);
                    $this->assertGreaterThan(0, count($user->departments));
                    $this->assertTrue($user->pivot instanceof Pivot);
                    $this->assertEquals('department_user', $user->pivot->getTable());
                }
            }
        }
    }
}
