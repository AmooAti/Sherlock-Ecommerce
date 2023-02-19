<?php

namespace Tests\Feature\Http\Controllers\API\Admin;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    const ADMIN_CUSTOMER_STORE = 'admin.customer.store';
    const ADMIN_CUSTOMER_INDEX = 'admin.customer.index';
    const ADMIN_CUSTOMER_UPDATE = 'admin.customer.update';
    const ADMIN_CUSTOMER_DESTROY = 'admin.customer.destroy';

    /**
     * A single instance of the model
     *
     * @var Customer
     */
    private Customer $payload;

    /**
     * A single model for persistence in the database
     *
     * @var Customer
     */
    private Customer $customer;

    /**
     * Setup the test environment
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->payload = Customer::factory()->makeOne([
            'password'     => 'Pass1234',
            'is_suspended' => $this->faker->randomElement(['active', 'deactivate']),
        ]);

        $this->customer = Customer::factory()->createOne([
            'is_suspended' => $this->faker->randomElement(['active', 'deactivate']),
        ]);
    }

    /**
     * Test as an admin, it should delete a customer
     *
     * @return void
     */
    public function test_as_an_admin_it_should_delete_a_customer(): void
    {
        $this->deleteJson(
            route(self::ADMIN_CUSTOMER_DESTROY, [$this->customer->id])
        )->assertSuccessful();

        $this->assertDatabaseMissing('customers', $this->customer->toArray());
    }

    /**
     * Test as an admin, It should not edit a customer with a password without lowercase.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_edit_a_customer_with_a_password_without_lowercase(): void
    {
        $this->putJson(
            route(self::ADMIN_CUSTOMER_UPDATE, [$this->customer->id]),
            ['password' => strtoupper($this->faker->password(8))]
        )->assertInvalid('password');
    }

    /**
     * Test as an admin, It should not edit a customer with a password without uppercase.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_edit_a_customer_with_a_password_without_uppercase(): void
    {
        $this->putJson(
            route(self::ADMIN_CUSTOMER_UPDATE, [$this->customer->id]),
            ['password' => strtolower($this->faker->password(8))]
        )->assertInvalid('password');
    }

    /**
     * Test as an admin, It should not edit a customer with a password that has not a number.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_edit_a_customer_with_a_password_that_has_not_a_number(): void
    {
        $this->putJson(
            route(self::ADMIN_CUSTOMER_UPDATE, [$this->customer->id]),
            ['password' => 'Password']
        )->assertInvalid('password');
    }

    /**
     * Test as an admin, It should not edit a customer with a password that is less than 8 characters.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_edit_a_customer_with_a_password_that_is_less_than_8_characters(): void
    {
        $this->putJson(
            route(self::ADMIN_CUSTOMER_UPDATE, [$this->customer->id]),
            ['password' => $this->faker->password(6, 7)]
        )->assertInvalid('password');
    }

    /**
     * Test as an admin, It should not edit a customer with a duplicate email.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_edit_a_customer_with_a_duplicate_email(): void
    {
        $this->putJson(
            route(self::ADMIN_CUSTOMER_UPDATE, [$this->customer->id]),
            ['email' => $this->customer->email]
        )->assertInvalid('email');
    }

    /**
     * Test as an admin, It should not edit a customer with an invalid email.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_edit_a_customer_with_an_invalid_email(): void
    {
        $this->putJson(
            route(self::ADMIN_CUSTOMER_UPDATE, [$this->customer->id]),
            ['email' => $this->faker->name]
        )->assertInvalid('email');
    }

    /**
     * Test as an admin, it should not edit a customer with invalid inputs.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_edit_a_customer_with_invalid_inputs(): void
    {
        $payload = [
            'firstname'    => $this->faker->numberBetween(4, 12),
            'lastname'     => $this->faker->numberBetween(4, 12),
            'phone_number' => $this->faker->numberBetween(8, 11),
            'is_suspended' => $this->faker->numberBetween(4, 12),
        ];

        $this->putJson(
            route(self::ADMIN_CUSTOMER_UPDATE, [$this->customer->id]),
            $payload
        )->assertInvalid(['firstname', 'lastname', 'phone_number', 'is_suspended']);
    }

    /**
     * Test as an admin, it should edit a customer with valid inputs.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_edit_a_customer_with_valid_inputs(): void
    {
        $this->putJson(
            route(self::ADMIN_CUSTOMER_UPDATE, [$this->customer->id]),
            $this->payload->getAttributes()
        )->assertSuccessful();
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

        $this->customer->delete();

        Customer::factory($count)->create();

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
        $this->payload->setAttribute('password', strtoupper($this->faker->password(8)));

        $this->postJson(
            route(self::ADMIN_CUSTOMER_STORE), $this->payload->getAttributes()
        )->assertInvalid('password');
    }

    /**
     * Test as an admin, it should not create a customer with a password that has not uppercase.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_create_a_customer_with_a_password_that_has_not_uppercase(): void
    {
        $this->payload->setAttribute('password', strtolower($this->faker->password(8)));

        $this->postJson(
            route(self::ADMIN_CUSTOMER_STORE), $this->payload->getAttributes()
        )->assertInvalid('password');
    }

    /**
     * Test as an admin, it should not create a customer with a password that has not a number.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_create_a_customer_with_a_password_that_has_not_a_number(): void
    {
        $this->payload->setAttribute('password', 'Password');

        $this->postJson(
            route(self::ADMIN_CUSTOMER_STORE), $this->payload->getAttributes()
        )->assertInvalid('password');
    }

    /**
     * Test as an admin, it should not create a customer with a password that is less than 8 characters.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_create_a_customer_with_a_password_that_is_less_than_8_characters(): void
    {
        $this->payload->setAttribute('password', $this->faker->password(6, 7));

        $this->postJson(
            route(self::ADMIN_CUSTOMER_STORE), $this->payload->getAttributes()
        )->assertInvalid('password');
    }

    /**
     * Test as an admin, it should not create a customer with a duplicate email.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_create_a_customer_with_a_duplicate_email(): void
    {
        $this->payload->setAttribute('email', $this->customer->email);

        $this->postJson(
            route(self::ADMIN_CUSTOMER_STORE), $this->payload->getAttributes()
        )->assertInvalid('email');
    }

    /**
     * Test as an admin, it should not create a customer with an invalid email.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_create_a_customer_with_an_invalid_email(): void
    {
        $this->payload->setAttribute('email', $this->faker->name);

        $this->postJson(
            route(self::ADMIN_CUSTOMER_STORE), $this->payload->getAttributes()
        )->assertInvalid('email');
    }

    /**
     * Test as an admin, it should not create a customer without required inputs.
     *
     * @return void
     */
    public function test_as_an_admin_it_should_not_create_a_customer_without_required_inputs(): void
    {
        $this->payload->setAttribute('firstname', null);
        $this->payload->setAttribute('lastname', null);
        $this->payload->setAttribute('email', null);
        $this->payload->setAttribute('password', null);

        $this->postJson(
            route(self::ADMIN_CUSTOMER_STORE), $this->payload->getAttributes()
        )->assertInvalid(['firstname', 'lastname', 'email', 'password']);
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

        $this->postJson(
            route(self::ADMIN_CUSTOMER_STORE), $payload
        )->assertInvalid(['firstname', 'lastname', 'email', 'password', 'phone_number', 'is_suspended']);
    }

    /**
     * Test as an admin, it should create a customer with valid inputs and insert to database
     *
     * @return void
     */
    public function test_as_an_admin_it_should_create_a_customer_with_valid_inputs_and_insert_to_database(): void
    {
        $this->postJson(
            route(self::ADMIN_CUSTOMER_STORE), $this->payload->getAttributes()
        )->assertSuccessful();

        unset($this->payload->password);
        $this->assertDatabaseHas('customers', $this->payload->getAttributes());
    }
}
