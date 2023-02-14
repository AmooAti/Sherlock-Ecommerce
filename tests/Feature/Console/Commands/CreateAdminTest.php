<?php

namespace Tests\Feature\Console\Commands;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateAdminTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_as_an_admin_should_be_register_with_correct_email_and_password()
    {

        $payload = [
            "email" => $this->faker->email(),
            "password" => $this->faker->password(8)
        ];


        $this->artisan('make:admin', $payload)
            ->assertSuccessful();
        unset($payload['password']);
        $this->assertDatabaseHas('admins', $payload);
    }


    public function test_as_an_admin_should_be_register_with_correct_email()
    {

        $payload = [
            "email" => $this->faker->email(),
        ];


        $this->artisan('make:admin', $payload)
            ->assertSuccessful();
        $this->assertDatabaseHas('admins', $payload);
    }

    public function test_as_an_admin_should_not_be_register_with_invalid_email()
    {

        $payload = [
            "email" => $this->faker->firstName(),
        ];

        $command = sprintf('make:admin %s', $payload["email"]);
        $this->artisan($command)
            ->expectsOutputToContain('The email must be a valid email address.')
            ->assertExitCode(1);
        $this->assertDatabaseMissing('admins', $payload);
    }

    public function test_as_an_admin_should_not_be_register_with_invalid_password()
    {

        $payload = [
            "email" => $this->faker->email(),
            "password" => "1234"
        ];

        $this->artisan('make:admin', $payload)
            ->expectsOutputToContain(
                'The password must contain at least one uppercase and one lowercase letter.')
            ->expectsOutputToContain('The password must be at least 8 characters')
            ->assertExitCode(1);

        $this->assertDatabaseMissing('admins', $payload);
    }


    public function test_as_an_admin_it_can_not_register_with_duplicated_email()
    {

        $user = Admin::factory()->create();
        $payload = [
            "email" => $user->email,
            "password" => $this->faker->password(8),
        ];
        $this->artisan('make:admin', $payload)
            ->expectsOutputToContain('The email has already been taken.')
            ->assertExitCode(1);
    }
}
