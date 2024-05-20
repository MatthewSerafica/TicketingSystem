<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminDepartmentControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testStoreMethodCreatesDepartment()
    {
        $response = $this->post('/admin/department/create/store', ['department' => 'Test Department']);
        $response->assertRedirect('/');
        $response->assertSessionHas('success');
    }

    public function testStoreMethodHandlesDatabaseError()
    {
        // Simulate a database error by causing a unique constraint violation
        $response = $this->post('/admin/department/create/store', ['department' => 'Duplicate Department']);
        $response->assertRedirect('/');
        $response->assertSessionHas('error');
    }
}
