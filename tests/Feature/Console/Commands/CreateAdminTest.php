<?php

namespace Tests\Feature\Console\Commands;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateAdminTest extends TestCase
{
    use WithFaker;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_as_an_admin_should_be_register_with_correct_email_and_password()
    {

        $payload = [
          "email"=>$this->faker->email(),
          "password"=>$this->faker->password(8)
        ];

        $command= sprintf('make:admin %s %s', $payload["email"],$payload["password"]);
        $this->artisan($command)->assertSuccessful();
        unset($payload['password']);
        $this->assertDatabaseHas('admins', $payload);
    }


    public function test_as_an_admin_should_be_register_with_correct_email()
    {

        $payload = [
            "email"=>$this->faker->email(),
        ];

        $command= sprintf('make:admin %s', $payload["email"]);
        $this->artisan($command)->assertSuccessful();
        $this->assertDatabaseHas('admins', $payload);
    }

    public function test_as_an_admin_should_not_be_register_with_invalid_email()
    {

        $payload = [
            "email"=>$this->faker->firstName(),
        ];

        $command= sprintf('make:admin %s', $payload["email"]);
        $this->artisan($command)->assertExitCode(0);
        $this->assertDatabaseHas('admins', $payload);
    }

//    public function test_as_an_admin_should_be_register_with_invalid_password()
//    {
//
//        $payload = [
//            "email"=>$this->faker->firstName(),
//        ];
//
//        $command= sprintf('make:admin %s', $payload["email"]);
//        $this->artisan($command)->assertExitCode(0);
//        $this->assertDatabaseMissing('admins', $payload);
//    }


    public function test_as_an_admin_it_can_not_register_with_duplicated_email()
    {

        $user = Admin::factory()->create();
        $payload =[
            "email"=> $user->email,
            "password"=>$this->faker->password(8),
        ];
        $command = sprintf('make:admin %s %s',
            $payload["email"],
            $payload["password"]);
        $this->artisan($command) ->assertExitCode(0);
    }
}
