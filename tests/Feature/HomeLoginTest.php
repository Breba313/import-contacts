<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use Faker\Generator as Faker;

class HomeLoginTest extends TestCase
{
    use RefreshDatabase;

    private function create_user() {
        return $user = factory(User::class)->make();
    }

    public function test_login_redirects_successfully()
    {
        // Create a user
        $user = $this->create_user();

        $response = $this->post('/login', ['email' => $user->email, 'password' => $user->password]);

        $response->assertStatus(302);
        $response->assertRedirect('/');
    }


    public function test_authenticated_user_can_access_home_page()
    {
        // Create a user
        $user = $this->create_user();

        $response = $this->actingAs($user)->get('/home');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_access_home_page()
    {
        $response = $this->get('/');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }
}