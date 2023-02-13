<?php

namespace Tests\Feature\Http\Controllers\API\Admin;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * Test as an admin, it should not create a customer with a password that has not lowercase.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_create_a_customer_with_a_password_that_has_not_lowercase(): void
    {
        $payload = [
            'firstname'    => $this->faker->firstName(),
            'lastname'     => $this->faker->lastName(),
            'email'        => $this->faker->unique()->safeEmail,
            'password'     => strtoupper($this->faker->password(8)),
            'phone_number' => $this->faker->phoneNumber(),
            'is_suspended' => $this->faker->randomElement(['active', 'deactivate']),
        ];

        $this->postJson(route('admin.customer.store'), $payload)
            ->assertInvalid('password');
    }

    /**
     * Test as an admin, it should not create a customer with a password that has not uppercase.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_create_a_customer_with_a_password_that_has_not_uppercase(): void
    {
        $payload = [
            'firstname'    => $this->faker->firstName(),
            'lastname'     => $this->faker->lastName(),
            'email'        => $this->faker->unique()->safeEmail,
            'password'     => strtolower($this->faker->password(8)),
            'phone_number' => $this->faker->phoneNumber(),
            'is_suspended' => $this->faker->randomElement(['active', 'deactivate']),
        ];

        $this->postJson(route('admin.customer.store'), $payload)
            ->assertInvalid('password');
    }

    /**
     * Test as an admin, it should not create a customer with a password that has not a number.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_create_a_customer_with_a_password_that_has_not_a_number(): void
    {
        $payload = [
            'firstname'    => $this->faker->firstName(),
            'lastname'     => $this->faker->lastName(),
            'email'        => $this->faker->unique()->safeEmail,
            'password'     => 'Password',
            'phone_number' => $this->faker->phoneNumber(),
            'is_suspended' => $this->faker->randomElement(['active', 'deactivate']),
        ];

        $this->postJson(route('admin.customer.store'), $payload)
            ->assertInvalid('password');
    }

    /**
     * Test as an admin, it should not create a customer with a password that is less than 8 characters.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_create_a_customer_with_a_password_that_is_less_than_8_characters(): void
    {
        $payload = [
            'firstname'    => $this->faker->firstName(),
            'lastname'     => $this->faker->lastName(),
            'email'        => $this->faker->unique()->safeEmail,
            'password'     => $this->faker->password(6, 7),
            'phone_number' => $this->faker->phoneNumber(),
            'is_suspended' => $this->faker->randomElement(['active', 'deactivate']),
        ];

        $this->postJson(route('admin.customer.store'), $payload)
            ->assertInvalid('password');
    }

    /**
     * Test as an admin, it should not create a customer with a duplicate email.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_create_a_customer_with_a_duplicate_email(): void
    {
        $customer = Customer::factory()->createOne();

        $payload = [
            'firstname'    => $this->faker->firstName(),
            'lastname'     => $this->faker->lastName(),
            'email'        => $customer->getAttribute('email'),
            'password'     => Hash::make($this->faker->password(8)),
            'phone_number' => $this->faker->phoneNumber(),
            'is_suspended' => $this->faker->randomElement(['active', 'deactivate']),
        ];

        $this->postJson(route('admin.customer.store'), $payload)
            ->assertInvalid('email');
    }

    /**
     * Test as an admin, it should not create a customer with an invalid email.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_create_a_customer_with_an_invalid_email(): void
    {
        $payload = [
            'firstname'    => $this->faker->firstName,
            'lastname'     => $this->faker->lastName,
            'email'        => $this->faker->name,
            'password'     => Hash::make($this->faker->password(8)),
            'phone_number' => $this->faker->phoneNumber,
            'is_suspended' => $this->faker->randomElement(['active', 'deactivate']),
        ];

        $this->postJson(route('admin.customer.store'), $payload)
            ->assertInvalid('email');
    }

    /**
     * Test as an admin, it should not create a customer without required parameters.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_create_a_customer_without_required_parameters(): void
    {
        $payload = [
            'firstname'    => null,
            'lastname'     => null,
            'email'        => null,
            'password'     => null,
            'phone_number' => $this->faker->phoneNumber,
            'is_suspended' => $this->faker->randomElement(['active', 'deactivate']),
        ];

        $this->postJson(route('admin.customer.store'), $payload)
            ->assertInvalid(['firstname', 'lastname', 'email', 'password']);
    }

    /**
     * Test as an admin, it should not create a customer with invalid parameters.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_create_a_customer_with_invalid_parameters(): void
    {
        $payload = [
            'firstname'    => rand(4, 12),
            'lastname'     => rand(4, 12),
            'email'        => $this->faker->name,
            'password'     => $this->faker->password(6, 7),
            'phone_number' => rand(8, 11),
            'is_suspended' => rand(4, 12),
        ];

        $this->postJson(route('admin.customer.store'), $payload)
            ->assertInvalid(['firstname', 'lastname', 'email', 'password', 'phone_number', 'is_suspended']);
    }

    /**
     * Test as an admin, it should create a customer with valid parameters and insert to database
     *
     * @return void
     */
    public function test_as_an_admin_it_should_create_a_customer_with_valid_parameters_and_insert_to_database(): void
    {
        $payload = [
            'firstname'    => $this->faker->firstName,
            'lastname'     => $this->faker->lastName,
            'email'        => $this->faker->unique()->safeEmail,
            'password'     => Hash::make($this->faker->password(8)),
            'phone_number' => $this->faker->phoneNumber,
            'is_suspended' => $this->faker->randomElement(['active', 'deactivate']),
        ];

        $this->postJson(route('admin.customer.store'), $payload)
            ->assertSuccessful();

        unset($payload['password']);
        $this->assertDatabaseHas('customers', $payload);
    }
}
