<?php

namespace Modules\ReferrerModule\Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Modules\ReferrerModule\Entities\Referral;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * ReferrerModuleControllerTest
 *
 */
class ReferrerModuleControllerTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    const TRYAGAIN = 'UserManagement::messages.try_again';


    const INVALIDTOKEN = 'UserManagement::messages.invalid_token';


    /**
     * Test retrieving the referral ID associated with the currently authenticated user.
     *
     * @return void
     */
    public function testGetReferrerIdReturnsReferrerIdForAuthenticatedUser()
    {
        // Create a user and generate a valid JWT token
        $token = $this->getUserToken();

        // Make a GET request to the user list endpoint with the token
        $response = $this->get(route('getReferrerId', [
            'token' => $token,
        ]));

        // Assert that the response has a HTTP status code of 200 (OK)
        $response->assertStatus(Response::HTTP_OK);

        // Assert that the response contains the expected JSON structure and data
        $response->assertJsonStructure([
            'status',
            'referrerId',
        ]);

        $response->assertJson([
            'status' => true,
        ]);
    }

    /**
     * Test retrieving the referral ID associated with invalid token.
     *
     * @return void
     */
    public function testGetReferrerIdReturnsReferrerIdForWithInvalidToken()
    {

        // Make a GET request to the user list endpoint with the token
        $response = $this->get(route('getReferrerId', [
            'token' => 'invalid_token',
        ]));

        // Assert that the response has a HTTP status code of 500 (Internal Server Error)
        $response->assertStatus(Response::HTTP_OK);

        // Assert that the response contains the expected JSON structure and data
        $response->assertJsonStructure([
            'status',
        ]);
        $response->assertJson([
            'status' => __(self::INVALIDTOKEN),
        ]);
    }

    /**
     * creates a new referral record in the database
     *
     * @return void
     */
    public function testCreateNewRefrrralrecordWithValidToken()
    {
        // Create a user and generate a valid JWT token
        $user = User::factory()->create();
        $user->update(['referrer_id' => Str::uuid()->toString()]);
        $token = JWTAuth::fromUser($user);

        $referrerName = $this->faker->name;
        $referrerEmail = $this->faker->email;
        $referredName = $user->name;
        $referredEmail = $user->email;
        $referredCode = $user->referrer_id;

        // Make a Post request to the referrer list endpoint with the token
        $response = $this->post(route('referrer.save', [
            'token' => $token,
            'referrer_name' => $referrerName,
            'referrer_email' => $referrerEmail,
            'referred_name' => $referredName,
            'referred_email' => $referredEmail,
            'referred_code' => $referredCode,
        ]));

        // Assert that the response has a HTTP status code of 200 (OK)
        $response->assertStatus(Response::HTTP_OK);

        // Assert that the response contains the expected JSON structure and data
        $response->assertJsonStructure([
            'status',
            'message',
            'data',
        ]);

        $response->assertJson([
            'status' => true,
            'message' => __('referrermodule::messages.referrer.saved'),
            'data' => [
                'referrer_name' => $referrerName,
                'referrer_email' => $referrerEmail,
                'referred_name' => $referredName,
                'referred_email' => $referredEmail,
                'referral_code' => $referredCode,
                'user_id' => $user->id,
            ],
        ]);

        //Check log table data in database
        $this->assertDatabaseHas('referrals', [
            'referrer_name' => $referrerName,
            'referrer_email' => $referrerEmail,
            'referred_name' => $referredName,
            'referred_email' => $referredEmail,
            'referral_code' => $referredCode,
            'user_id' => $user->id,
        ]);
    }

    /**
     * creates a new referral record with invalid data
     *
     * @return void
     */
    public function testCreateNewRefrrralrecordWithInvalidData()
    {
        // Create a user and generate a valid JWT token
        $user = User::factory()->create();
        $user->update(['referrer_id' => Str::uuid()->toString()]);
        $token = JWTAuth::fromUser($user);

        $referrerName = $this->faker->name;
        $referrerEmail = $this->faker->email;
        $referredName = $user->name;
        $referredEmail = $user->email;
        $referredCode = 'test-ref-code';

        // Make a Post request to the referrer list endpoint with the token
        $response = $this->post(route('referrer.save', [
            'token' => $token,
            'referrer_name' => $referrerName,
            'referrer_email' => $referrerEmail,
            'referred_name' => $referredName,
            'referred_email' => $referredEmail,
            'referred_code' => $referredCode,
        ]));

        // Assert that the response has a HTTP status code of 200 (OK)
        $response->assertStatus(Response::HTTP_OK);

        // Assert that the response contains the expected JSON structure and data
        $response->assertJsonStructure([
            'status',
            'message',
        ]);

        $response->assertJson([
            'status' => false,
        ]);

        //Check referrals table data in database
        $this->assertDatabaseMissing('referrals', [
            'referrer_name' => $referrerName,
            'referrer_email' => $referrerEmail,
            'referred_name' => $referredName,
            'referred_email' => $referredEmail,
            'referral_code' => $referredCode,
            'user_id' => $user->id,
        ]);
    }

    /**
     * creates a new referral record with invalid Token
     *
     * @return void
     */
    public function testCreateNewRefrrralrecordWithInvalidToken()
    {
        // Create a user and generate a valid JWT token
        $user = User::factory()->create();
        $user->update(['referrer_id' => Str::uuid()->toString()]);

        $referrerName = $this->faker->name;
        $referrerEmail = $this->faker->email;
        $referredName = $user->name;
        $referredEmail = $user->email;
        $referredCode = $user->referrer_id;

        // Make a Post request to the referrer list endpoint with the token
        $response = $this->post(route('referrer.save', [
            'token' => 'invalid-token',
            'referrer_name' => $referrerName,
            'referrer_email' => $referrerEmail,
            'referred_name' => $referredName,
            'referred_email' => $referredEmail,
            'referred_code' => $referredCode,
        ]));

        // Assert that the response has a HTTP status code of 200 (OK)
        $response->assertStatus(Response::HTTP_OK);

        // Assert that the response contains the expected JSON structure and data
        $response->assertJsonStructure([
            'status',
        ]);
        $response->assertJson([
            'status' => __(self::INVALIDTOKEN),
        ]);

        //Check referrals table data in database
        $this->assertDatabaseMissing('referrals', [
            'referrer_name' => $referrerName,
            'referrer_email' => $referrerEmail,
            'referred_name' => $referredName,
            'referred_email' => $referredEmail,
            'referral_code' => $referredCode,
            'user_id' => $user->id,
        ]);
    }

    /**
     * updates an existing referral record
     *
     * @return void
     */
    public function testUpdateRefrralRecordWithValidToken()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        // Create a referrer
        $referrer = Referral::factory()->create();

        $user->update(['referrer_id' => $referrer->referral_code]);

        $name = $this->faker->name;

        $response = $this->put(route('referrer.update', [
            'token' => $token,
            'id' => $referrer->id,
            'referrer_name' => $name,
            'referrer_email' => $referrer->referrer_email,
            'referred_name' => $referrer->referred_name,
            'referred_email' => $referrer->referred_email,
            'referred_code' => $referrer->referral_code,
        ]));

        // Assert that the response has a HTTP status code of 200 (OK)
        $response->assertStatus(Response::HTTP_OK);

        // Assert that the response contains the expected JSON structure and data
        $response->assertJsonStructure([
            'status',
            'message',
        ]);

        $response->assertJson([
            'status' => true,
            'message' => __('referrermodule::messages.referrer.updated_success'),
        ]);

        //Check referrals table data in database
        $this->assertDatabaseHas('referrals', [
            'referrer_name' => $name,
            'referrer_email' => $referrer->referrer_email,
            'referred_name' => $referrer->referred_name,
            'referred_email' => $referrer->referred_email,
            'referral_code' => $referrer->referral_code,
            'user_id' => $user->id,
        ]);
    }

    /**
     * deletes a referral record from the database.
     *
     * @return void
     */
    public function testDeleteRefrralRecordWithValidToken()
    {
        $token = $this->getUserToken();

        $referrer = Referral::factory()->create();

        $response = $this->delete(route('referrer.delete', [
            'token' => $token,
            'id' => $referrer->id,
        ]));

        // Assert that the response has a HTTP status code of 200 (OK)
        $response->assertStatus(Response::HTTP_OK);

        // Assert that the response contains the expected JSON structure and data
        $response->assertJsonStructure([
            'status',
            'message',
        ]);

        $response->assertJson([
            'status' => true,
            'message' => __('referrermodule::messages.referrer.delete_success'),
        ]);

        //Check referrals table data in database
        $this->assertDatabaseMissing('referrals', [
            'id' => $referrer->id,
        ]);
    }

    /**
     * delete a referral record with invalid data.
     *
     * @return void
     */
    public function testDeleteRefrralRecordWithInvalidData()
    {
        $token = $this->getUserToken();

        $referrer = Referral::factory()->create();

        $response = $this->delete(route('referrer.delete', [
            'token' => $token,
            'id' => 999,
        ]));

        // Assert that the response has a HTTP status code of 400 (BAD Request)
        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        // Assert that the response contains the expected JSON structure and data
        $response->assertJsonStructure([
            'status',
            'message',
        ]);

        $response->assertJson([
            'status' => false,
            'message' => 'Sorry!! Try again',
        ]);

        //Check referrals table data in database
        $this->assertDatabaseHas('referrals', [
            'id' => $referrer->id,
        ]);
    }

    /**
     * update a referral status from the database
     */
    public function testUpdateRefrralStatusWithValidToken()
    {
        $token = $this->getUserToken();

        $referrer = Referral::factory()->create();
        $approved = 'approved';
        $response = $this->post(route('referrer.update.status', [
            'token' => $token,
            'id' => $referrer->id,
            'status' => $approved,
        ]));

        // Assert that the response has a HTTP status code of 200 (OK)
        $response->assertStatus(Response::HTTP_OK);

        // Assert that the response contains the expected JSON structure and data
        $response->assertJsonStructure([
            'status',
            'message',
        ]);

        $response->assertJson([
            'status' => true,
            'message' => __('referrermodule::messages.referrer.status_updated'),
        ]);

        //Check referrals table data in database
        $this->assertDatabaseHas('referrals', [
            'id' => $referrer->id,
            'status' => $approved,
        ]);

    }

    /**
     * update a referral status from the database with invalid data
     */
    public function testUpdateRefrralStatusWithInvalidData()
    {
        $token = $this->getUserToken();

        $referrer = Referral::factory()->create();
        $approved = 'approved';
        $response = $this->post(route('referrer.update.status', [
            'token' => $token,
            'id' => 999,
            'status' => $approved,
        ]));

        // Assert that the response has a HTTP status code of 200 (OK)
        $response->assertStatus(Response::HTTP_OK);

        // Assert that the response contains the expected JSON structure and data
        $response->assertJsonStructure([
            'status',
            'message',
        ]);

        $response->assertJson([
            'status' => false,
            'message' => [
                'id' => [
                    'The referred id does not exist in the referred table.',
                ],
            ],
        ]);

        //Check referrals table data in database
        $this->assertDatabaseHas('referrals', [
            'id' => $referrer->id,
            'status' => $referrer->status,
        ]);

    }

    /**
     * update a referral status from the database with invalid Token
     */
    public function testUpdateRefrralStatusWithInvalidToken()
    {
        $referrer = Referral::factory()->create();
        $approved = 'approved';
        $response = $this->post(route('referrer.update.status', [
            'token' => 'invalid-token',
            'id' => $referrer->id,
            'status' => $approved,
        ]));

        // Assert that the response has a HTTP status code of 200 (OK)
        $response->assertStatus(Response::HTTP_OK);

        // Assert that the response contains the expected JSON structure and data
        $response->assertJsonStructure([
            'status',
        ]);
        $response->assertJson([
            'status' => __(self::INVALIDTOKEN),
        ]);

        //Check referrals table data in database
        $this->assertDatabaseHas('referrals', [
            'id' => $referrer->id,
            'status' => $referrer->status,
        ]);

    }

    /**
     * retrieves information about a user and the number of referrals they have made.
     *
     * @return void
     */
    public function testRetriversInformationAboutUserAndNumberOfReferralsWithValidToken()
    {
        $token = $this->getUserToken();

        $user = User::factory()->create();
        $user->update(['referrer_id' => Str::uuid()->toString()]);

        Referral::factory(10)->create([
                  'referral_code' => $user->referrer_id,
                  'user_id' => $user->id,
                  'status'=>'approved'
                ]);

        $response = $this->get(route('user.referrers', [
            'userId' => $user->id,
            'token' => $token,

        ]));

        // Assert that the response has a HTTP status code of 200 (OK)
        $response->assertStatus(Response::HTTP_OK);

        // Assert that the response contains the expected JSON structure and data
        $response->assertJsonStructure([
            'status',
            'data',
        ]);

        $response->assertJson([
            'status' => true,
            'data'   => [
                'name' => $user->name,
                'email' => $user->email,
                'referrers_count' => '10'
            ]
        ]);
    }


    /**
     * retrieves information about a user and the number of referrals they have made.
     *
     * @return void
     */
    public function testRetriversInformationAboutUserAndNumberOfReferralsWithInvalidData()
    {
        $token = $this->getUserToken();

        $user = User::factory()->create();
        $user->update(['referrer_id' => Str::uuid()->toString()]);

        Referral::factory(10)->create([
                  'referral_code' => $user->referrer_id,
                  'user_id' => $user->id,
                  'status'=>'approved'
                ]);

        $response = $this->get(route('user.referrers', [
            'userId' => 999,
            'token' => $token,

        ]));

        // Assert that the response has a HTTP status code of 400
        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        // Assert that the response contains the expected JSON structure and data
        $response->assertJsonStructure([
            'status',
            'message'
        ]);

        $response->assertJson([
            'status' => false,
            'message'   => __(self::TRYAGAIN)
        ]);
    }

        /**
     * retrieves information about a user and the number of referrals they have made.
     *
     * @return void
     */
    public function testRetriversInformationAboutUserAndNumberOfReferralsWithInvalidToken()
    {
        $user = User::factory()->create();
        $user->update(['referrer_id' => Str::uuid()->toString()]);

        Referral::factory(10)->create([
                  'referral_code' => $user->referrer_id,
                  'user_id' => $user->id,
                  'status'=>'approved'
                ]);

        $response = $this->get(route('user.referrers', [
            'userId' => 999,
            'token' => 'invalid-token',

        ]));

        // Assert that the response has a HTTP status code of 200
        $response->assertStatus(Response::HTTP_OK);

        // Assert that the response contains the expected JSON structure and data
        $response->assertJsonStructure([
            'status',
        ]);

        $response->assertJson([
            'status' =>  __(self::INVALIDTOKEN)
        ]);
    }


    private function getUserToken()
    {
        $user = User::factory()->create();
        return JWTAuth::fromUser($user);
    }
}
