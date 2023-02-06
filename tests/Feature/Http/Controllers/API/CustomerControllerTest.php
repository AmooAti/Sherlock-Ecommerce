<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Models\Customer;
use Database\Factories\CustomerFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;
    const ROUTE_CUSTOMER_REGISTER = 'customer.register';

    /**
     * An array of fake customer
     *
     * @var array
     */
    private array $payload;

    public function __construct()
    {
        parent::__construct();

        $this->payload = CustomerFactory::new()->definition();
    }

    /**
     * Test as a customer to register using a password without a number.
     *
     * @return void
     */
    public function test_as_a_customer_it_should_not_be_registered_with_a_password_without_a_number(): void
    {
        $this->payload['password'] = Str::repeat('p@sS', 2);

        $this->postJson(route(self::ROUTE_CUSTOMER_REGISTER), $this->payload)
            ->assertInvalid('password');
    }

    /**
     * Test as a customer to register using a password without an uppercase.
     *
     * @return void
     */
    public function test_as_a_customer_it_should_not_be_registered_with_a_password_without_an_uppercase(): void
    {
        $this->payload['password'] = strtolower(fake()->password(8));

        $this->postJson(route(self::ROUTE_CUSTOMER_REGISTER), $this->payload)
            ->assertInvalid('password');
    }

    /**
     * Test as a customer to register using a password without a lowercase.
     *
     * @return void
     */
    public function test_as_a_customer_it_should_not_be_registered_with_a_password_without_a_lowercase(): void
    {
        $this->payload['password'] = strtoupper(fake()->password(8));

        $this->postJson(route(self::ROUTE_CUSTOMER_REGISTER), $this->payload)
            ->assertInvalid('password');
    }

    /**
     * Test as a customer to register using a password with less than 8 characters.
     *
     * @return void
     */
    public function test_as_a_customer_it_should_not_be_registered_with_a_password_with_less_than_8_characters(): void
    {
        $this->payload['password'] = fake()->password(6, 7);

        $this->postJson(route(self::ROUTE_CUSTOMER_REGISTER), $this->payload)
            ->assertInvalid('password');
    }

    /**
     * Test as a customer to register using a duplicate email.
     *
     * @return void
     */
    public function test_as_a_customer_it_should_not_be_registered_with_duplicate_email(): void
    {
        $customer = Customer::factory()->createOne();

        $this->payload['email'] = $customer->getAttribute('email');

        $this->postJson(route(self::ROUTE_CUSTOMER_REGISTER), $this->payload)
            ->assertInvalid('email');
    }

    /**
     * Test as a customer to register using an invalid email.
     *
     * @return void
     */
    public function test_as_a_customer_it_should_not_be_registered_with_invalid_email(): void
    {
        $this->payload['email'] = fake()->userName();

        $this->postJson(route(self::ROUTE_CUSTOMER_REGISTER), $this->payload)
            ->assertInvalid('email');
    }

    /**
     * Test as a customer to register using valid parameters.
     *
     * @return void
     */
    public function test_as_a_customer_it_should_be_registered_with_valid_params(): void
    {
        $this->postJson(route(self::ROUTE_CUSTOMER_REGISTER), $this->payload)
            ->assertCreated();

        unset($this->payload['password']);

        $this->assertDatabaseHas('customers', $this->payload);
    }
}
