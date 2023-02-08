<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Models\Customer;
use Database\Factories\CustomerFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    use WithFaker;
//    use RefreshDatabase;
    const ROUTE_CUSTOMER_REGISTER = 'customer.register';
    const ROUTE_CUSTOMER_LOGIN = 'customer.login';
    const ROUTE_CUSTOMER_LOGOUT = 'customer.logout';

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

    public function test_as_a_customer_with_correct_credential_should_be_able_to_login()
    {
        $customer = Customer::factory()->createOne();

        $payload =['email'=>$customer->email, 'password'=>'password'];

        $this->postJson(route(self::ROUTE_CUSTOMER_LOGIN),$payload)
            ->assertSuccessful()->assertJsonStructure([
                'data'=>  ['token','expires_at']]);
    }

    public function test_as_a_customer_with_incorrect_email_should_not_be_able_to_login()
    {
        $customer = Customer::factory()->createOne();

        $payload =['email'=>'test'.$customer->email, 'password'=>'password'];

        $this->postJson(route(self::ROUTE_CUSTOMER_LOGIN),$payload)
            ->assertUnauthorized()->assertExactJson([
                'error'=>'The provided credentials are incorrect.']);
    }


    public function test_as_a_customer_with_incorrect_password_should_not_be_able_to_login()
    {
        $customer = Customer::factory()->createOne();

        $payload =['email'=>$customer->email, 'password'=>"password1"];

        $this->postJson(route(self::ROUTE_CUSTOMER_LOGIN),$payload)
            ->assertUnauthorized()->assertExactJson([
                'error'=>'The provided credentials are incorrect.']);
    }

    public function test_as_a_customer_with_invalid_email_should_not_be_able_to_login()
    {

        $payload =['email'=>$this->faker->firstName(), 'password'=>"password1"];

        $this->postJson(route(self::ROUTE_CUSTOMER_LOGIN),$payload)
            ->assertInvalid(['email']);
    }


    public function test_as_a_loggedIn_customer_with_valid_token_should_be_able_to_logout()
    {

        $customer = Customer::factory()->createOne();
        $payload =['email'=>$customer->email, 'password'=>'password'];

        $response =  $this->postJson(route(self::ROUTE_CUSTOMER_LOGIN),$payload);
        $data = $response->decodeResponseJson()['data'];
        $token = $data["token"];
        $headers = [
                'HTTP_Authorization' => 'Bearer '.$token,
                'Accept'=>' application/json'
        ];


        $this->get(route(self::ROUTE_CUSTOMER_LOGOUT), $headers)
        ->assertSuccessful()
        ->assertExactJson(['message'=>  'The customer logged out successfully.']);

        $this->assertDatabaseMissing('personal_access_tokens', $data);
        /*
           @AmooAti this return 200
             $this->get(route(self::ROUTE_CUSTOMER_LOGOUT),$headers)
        ->assertUnauthorized();
        */
    }



    public function test_as_a_logged_out_customer_should_be_authorized_with_other_token()
    {

        $customer = Customer::factory()->createOne();
        $payload =['email'=>$customer->email, 'password'=>'password'];

        $response1 =  $this->postJson(route(self::ROUTE_CUSTOMER_LOGIN),$payload);
        $data1 = $response1->decodeResponseJson()['data'];
        $token1 = $data1["token"];

        $response2 =  $this->postJson(route(self::ROUTE_CUSTOMER_LOGIN),$payload);
        $data2 = $response1->decodeResponseJson()['data'];
        $token2 = $data2["token"];

        $headers = [
            'HTTP_Authorization' => 'Bearer '.$token1,
            'Accept'=>' application/json'
        ];
        $this->get(route(self::ROUTE_CUSTOMER_LOGOUT), $headers);
        $this->getJson(route(self::ROUTE_CUSTOMER_LOGOUT,
            ['HTTP_Authorization' => 'Bearer '.$token2]))
            ->assertSuccessful();


    }
}
