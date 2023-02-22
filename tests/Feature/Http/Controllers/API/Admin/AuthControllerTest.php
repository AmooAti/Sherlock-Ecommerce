<?php

namespace Tests\Feature\Http\Controllers\API\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use WithFaker;

    const ADMIN_LOGIN = 'admin.login';
    const ADMIN_LOGOUT = 'admin.logout';

    /**
     * A single instance of the model
     *
     * @var Admin
     */
    private Admin $admin;

    /**
     * Setup the test environment
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->createOne();
    }

    /*
     |--------------------------------------------------------------------------
     | Logout Tests
     |--------------------------------------------------------------------------
     */

    /**
     * Test as an admin, it should not log out without a bearer token.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_logout_without_a_bearer_token()
    {
        $this->admin->createToken('api_token');

        $headers = [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $this->get(route(self::ADMIN_LOGOUT), $headers)
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'Unauthenticated.']);
    }

    /**
     * Test as an admin, it should not log out with an invalid bearer token.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_logout_with_an_invalid_bearer_token()
    {
        $this->admin->createToken('api_token');

        $fakeToken = "3|BSYHMiRchjh05WmkPAAbT1dRWHdoTnwv5plc7C5L";

        $headers = [
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => "Bearer $fakeToken",
        ];

        $this->getJson(route(self::ADMIN_LOGOUT), $headers)
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'Unauthenticated.']);
    }

    /**
     * Test as an admin, it should log out with a valid bearer token.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_logout_with_a_valid_bearer_token()
    {
        $token = $this->admin->createToken('api_token')->plainTextToken;

        $payload = [
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => "Bearer $token",
        ];

        $this->actingAs($this->admin)
            ->getJson(route(self::ADMIN_LOGOUT), $payload)
            ->assertSuccessful()
            ->assertExactJson(['message' => 'Logged out has been successful.']);

        $this->assertDatabaseMissing(
            'personal_access_tokens',
            ['id' => explode('|', $token)]
        );
    }

    /*
     |--------------------------------------------------------------------------
     | Login Tests
     |--------------------------------------------------------------------------
     */

    /**
     * Test as an admin, it should not log in with an incorrect password.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_login_with_an_incorrect_password(): void
    {
        $payload = [
            'email'    => $this->admin->email,
            'password' => 'Pass1234',
        ];

        $this->actingAs($this->admin)
            ->postJson(route(self::ADMIN_LOGIN), $payload)
            ->assertExactJson(['message' => 'The email or password are incorrect!'])
            ->assertUnauthorized();
    }

    /**
     * Test as an admin, it should not log in with an incorrect email.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_login_with_an_incorrect_email(): void
    {
        $payload = [
            'email'    => $this->faker->safeEmail,
            'password' => 'password',
        ];

        $this->actingAs($this->admin)
            ->postJson(route(self::ADMIN_LOGIN), $payload)
            ->assertExactJson(['message' => 'The email or password are incorrect!'])
            ->assertUnauthorized();
    }

    /**
     * Test as an admin, it should not log in with an invalid email.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_login_with_an_invalid_email(): void
    {
        $payload = [
            'email'    => $this->faker->name,
            'password' => 'password',
        ];

        $this->actingAs($this->admin)
            ->postJson(route(self::ADMIN_LOGIN), $payload)
            ->assertInvalid('email');
    }

    /**
     * Test as an admin, it should log in with valid inputs.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_login_with_valid_inputs(): void
    {
        $payload = [
            'email'    => $this->admin->email,
            'password' => 'password',
        ];

        $this->actingAs($this->admin)
            ->postJson(route(self::ADMIN_LOGIN), $payload)
            ->assertJsonStructure(['data' => ['bearer_token', 'expires_at']])
            ->assertOk();
    }
}
