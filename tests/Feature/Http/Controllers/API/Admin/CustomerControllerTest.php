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

    const ADMIN_CUSTOMER_STORE = 'admin.customer.store';
    const ADMIN_CUSTOMER_INDEX = 'admin.customer.index';
    const ADMIN_CUSTOMER_UPDATE = 'admin.customer.update';
    const ADMIN_CUSTOMER_DESTROY = 'admin.customer.destroy';

    /**
     * Test as an admin, it should delete a customer
     *
     * @return void
     */
    public function test_as_an_admin_it_should_delete_a_customer(): void
    {
        $customer = Customer::factory()->createOne(
            [
                'firstname'    => $this->faker->firstName,
                'lastname'     => $this->faker->lastName,
                'email'        => $this->faker->safeEmail,
                'password'     => Hash::make($this->faker->password(8)),
                'phone_number' => $this->faker->phoneNumber,
                'is_suspended' => $this->faker->randomElement(['active', 'deactivate']),
            ]
        );

        $customerID = $customer->getAttribute('id');

        $this->deleteJson(route(self::ADMIN_CUSTOMER_DESTROY, [$customerID]))
            ->assertSuccessful();

        $this->assertDatabaseMissing('customers', $customer->toArray());
    }

    /**
     * Test as an admin, It should not edit a customer with a password without lowercase.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_edit_a_customer_with_a_password_without_lowercase(): void
    {
        $customer = Customer::factory()->createOne(
            [
                'firstname'    => $this->faker->firstName,
                'lastname'     => $this->faker->lastName,
                'email'        => $this->faker->safeEmail,
                'password'     => Hash::make($this->faker->password(8)),
                'phone_number' => $this->faker->phoneNumber,
                'is_suspended' => $this->faker->randomElement(['active', 'deactivate']),
            ]
        );

        $customerID = $customer->getAttribute('id');

        $payload = [
            'password' => strtoupper($this->faker->password(8)),
        ];

        $this->putJson(route(self::ADMIN_CUSTOMER_UPDATE, [$customerID]), $payload)
            ->assertInvalid('password');
    }

    /**
     * Test as an admin, It should not edit a customer with a password without uppercase.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_edit_a_customer_with_a_password_without_uppercase(): void
    {
        $customer = Customer::factory()->createOne(
            [
                'firstname'    => $this->faker->firstName,
                'lastname'     => $this->faker->lastName,
                'email'        => $this->faker->safeEmail,
                'password'     => Hash::make($this->faker->password(8)),
                'phone_number' => $this->faker->phoneNumber,
                'is_suspended' => $this->faker->randomElement(['active', 'deactivate']),
            ]
        );

        $customerID = $customer->getAttribute('id');

        $payload = [
            'password' => strtolower($this->faker->password(8)),
        ];

        $this->putJson(route(self::ADMIN_CUSTOMER_UPDATE, [$customerID]), $payload)
            ->assertInvalid('password');
    }

    /**
     * Test as an admin, It should not edit a customer with a password that has not a number.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_edit_a_customer_with_a_password_that_has_not_a_number(): void
    {
        $customer = Customer::factory()->createOne(
            [
                'firstname'    => $this->faker->firstName,
                'lastname'     => $this->faker->lastName,
                'email'        => $this->faker->safeEmail,
                'password'     => Hash::make($this->faker->password(8)),
                'phone_number' => $this->faker->phoneNumber,
                'is_suspended' => $this->faker->randomElement(['active', 'deactivate']),
            ]
        );

        $customerID = $customer->getAttribute('id');

        $payload = [
            'password' => 'Password',
        ];

        $this->putJson(route(self::ADMIN_CUSTOMER_UPDATE, [$customerID]), $payload)
            ->assertInvalid('password');
    }

    /**
     * Test as an admin, It should not edit a customer with a password that is less than 8 characters.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_edit_a_customer_with_a_password_that_is_less_than_8_characters(): void
    {
        $customer = Customer::factory()->createOne(
            [
                'firstname'    => $this->faker->firstName,
                'lastname'     => $this->faker->lastName,
                'email'        => $this->faker->safeEmail,
                'password'     => Hash::make($this->faker->password(8)),
                'phone_number' => $this->faker->phoneNumber,
                'is_suspended' => $this->faker->randomElement(['active', 'deactivate']),
            ]
        );

        $customerID = $customer->getAttribute('id');

        $payload = [
            'password' => $this->faker->password(6, 7),
        ];

        $this->putJson(route(self::ADMIN_CUSTOMER_UPDATE, [$customerID]), $payload)
            ->assertInvalid('password');
    }

    /**
     * Test as an admin, It should not edit a customer with a duplicate email.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_edit_a_customer_with_a_duplicate_email(): void
    {
        $customer = Customer::factory()->createOne(
            [
                'firstname'    => $this->faker->firstName,
                'lastname'     => $this->faker->lastName,
                'email'        => $this->faker->safeEmail,
                'password'     => Hash::make($this->faker->password(8)),
                'phone_number' => $this->faker->phoneNumber,
                'is_suspended' => $this->faker->randomElement(['active', 'deactivate']),
            ]
        );

        $customerID = $customer->getAttribute('id');

        $payload = [
            'email' => $customer->getAttribute('email'),
        ];

        $this->putJson(route(self::ADMIN_CUSTOMER_UPDATE, [$customerID]), $payload)
            ->assertInvalid('email');
    }

    /**
     * Test as an admin, It should not edit a customer with an invalid email.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_edit_a_customer_with_an_invalid_email(): void
    {
        $customer = Customer::factory()->createOne(
            [
                'firstname'    => $this->faker->firstName,
                'lastname'     => $this->faker->lastName,
                'email'        => $this->faker->safeEmail,
                'password'     => Hash::make($this->faker->password(8)),
                'phone_number' => $this->faker->phoneNumber,
                'is_suspended' => $this->faker->randomElement(['active', 'deactivate']),
            ]
        );

        $customerID = $customer->getAttribute('id');

        $payload = [
            'email' => $this->faker->name,
        ];

        $this->putJson(route(self::ADMIN_CUSTOMER_UPDATE, [$customerID]), $payload)
            ->assertInvalid('email');
    }

    /**
     * Test as an admin, it should not edit a customer with invalid inputs.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_edit_a_customer_with_invalid_inputs(): void
    {
        $customer = Customer::factory()->createOne(
            [
                'firstname'    => $this->faker->firstName,
                'lastname'     => $this->faker->lastName,
                'email'        => $this->faker->safeEmail,
                'password'     => Hash::make($this->faker->password(8)),
                'phone_number' => $this->faker->phoneNumber,
                'is_suspended' => $this->faker->randomElement(['active', 'deactivate']),
            ]
        );

        $customerID = $customer->getAttribute('id');

        $payload = [
            'firstname'    => $this->faker->numberBetween(4, 12),
            'lastname'     => $this->faker->numberBetween(4, 12),
            'phone_number' => $this->faker->numberBetween(8, 11),
            'is_suspended' => $this->faker->numberBetween(4, 12),
        ];

        $this->putJson(route(self::ADMIN_CUSTOMER_UPDATE, [$customerID]), $payload)
            ->assertInvalid(['firstname', 'lastname', 'phone_number', 'is_suspended']);
    }

    /**
     * Test as an admin, it should edit a customer with valid inputs.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_edit_a_customer_with_valid_inputs(): void
    {
        $customer = Customer::factory()->createOne(
            [
                'firstname'    => $this->faker->firstName,
                'lastname'     => $this->faker->lastName,
                'email'        => $this->faker->safeEmail,
                'password'     => Hash::make($this->faker->password(8)),
                'phone_number' => $this->faker->phoneNumber,
                'is_suspended' => $this->faker->randomElement(['active', 'deactivate']),
            ]
        );

        $customerID = $customer->getAttribute('id');

        $payload = [
            'firstname'    => $this->faker->firstName,
            'lastname'     => $this->faker->lastName,
            'email'        => $this->faker->safeEmail,
            'password'     => 'Pass1234',
            'phone_number' => $this->faker->phoneNumber,
            'is_suspended' => $this->faker->randomElement(['active', 'deactivate']),
        ];

        $this->putJson(route(self::ADMIN_CUSTOMER_UPDATE, [$customerID]), $payload)
            ->assertSuccessful();
    }

    /**
     * Test as an admin, it should get the count of customers equal to the limit variable exactly
     *
     * @return void
     */
    public function test_as_an_admin_it_should_get_the_count_of_customers_equal_to_the_limit_variable_exactly(): void
    {
        $page = 1;
        $limit = 3;
        $count = 30;

        $customers = Customer::factory()->count($count)->create();

        $payload = $customers->forPage($page, $limit)->toArray();

        $this->assertTrue($limit === count($payload));
    }

    /**
     * Test as an admin, it should get all customers
     *
     * @return void
     */
    public function test_as_an_admin_it_should_get_all_customers(): void
    {
        $count = 30;

        Customer::factory()->count($count)->create();

        $this->getJson(
            route(self::ADMIN_CUSTOMER_INDEX),
            [
                'Accept'       => 'application/json',
                'Content_Type' => 'application/json',
            ]
        )->assertJsonCount($count, 'customers');
    }

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

        $this->postJson(route(self::ADMIN_CUSTOMER_STORE), $payload)
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

        $this->postJson(route(self::ADMIN_CUSTOMER_STORE), $payload)
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

        $this->postJson(route(self::ADMIN_CUSTOMER_STORE), $payload)
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

        $this->postJson(route(self::ADMIN_CUSTOMER_STORE), $payload)
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
            'password'     => 'Pass1234',
            'phone_number' => $this->faker->phoneNumber(),
            'is_suspended' => $this->faker->randomElement(['active', 'deactivate']),
        ];

        $this->postJson(route(self::ADMIN_CUSTOMER_STORE), $payload)
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
            'password'     => 'Pass1234',
            'phone_number' => $this->faker->phoneNumber,
            'is_suspended' => $this->faker->randomElement(['active', 'deactivate']),
        ];

        $this->postJson(route(self::ADMIN_CUSTOMER_STORE), $payload)
            ->assertInvalid('email');
    }

    /**
     * Test as an admin, it should not create a customer without required inputs.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_create_a_customer_without_required_inputs(): void
    {
        $payload = [
            'firstname'    => null,
            'lastname'     => null,
            'email'        => null,
            'password'     => null,
            'phone_number' => $this->faker->phoneNumber,
            'is_suspended' => $this->faker->randomElement(['active', 'deactivate']),
        ];

        $this->postJson(route(self::ADMIN_CUSTOMER_STORE), $payload)
            ->assertInvalid(['firstname', 'lastname', 'email', 'password']);
    }

    /**
     * Test as an admin, it should not create a customer with invalid inputs.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_create_a_customer_with_invalid_inputs(): void
    {
        $payload = [
            'firstname'    => $this->faker->numberBetween(4, 12),
            'lastname'     => $this->faker->numberBetween(4, 12),
            'email'        => $this->faker->name,
            'password'     => $this->faker->password(6, 7),
            'phone_number' => $this->faker->numberBetween(8, 11),
            'is_suspended' => $this->faker->numberBetween(4, 12),
        ];

        $this->postJson(route(self::ADMIN_CUSTOMER_STORE), $payload)
            ->assertInvalid(['firstname', 'lastname', 'email', 'password', 'phone_number', 'is_suspended']);
    }

    /**
     * Test as an admin, it should create a customer with valid inputs and insert to database
     *
     * @return void
     */
    public function test_as_an_admin_it_should_create_a_customer_with_valid_inputs_and_insert_to_database(): void
    {
        $payload = [
            'firstname'    => $this->faker->firstName,
            'lastname'     => $this->faker->lastName,
            'email'        => $this->faker->unique()->safeEmail,
            'password'     => 'Pass1234',
            'phone_number' => $this->faker->phoneNumber,
            'is_suspended' => $this->faker->randomElement(['active', 'deactivate']),
        ];

        $this->postJson(route(self::ADMIN_CUSTOMER_STORE), $payload)
            ->assertSuccessful();

        unset($payload['password']);
        $this->assertDatabaseHas('customers', $payload);
    }
}
