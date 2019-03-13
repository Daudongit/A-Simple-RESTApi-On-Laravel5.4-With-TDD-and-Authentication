<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AuthenticationTest extends TestCase
{   
    use DatabaseMigrations;

    public function test_that_a_user_can_register()
    {
        //User's data
        $user = factory('App\User')->make(
            ['password_confirmation'=>'secret1234']
        );
        
        $userArray = $user->toArray()+['password'=>'secret1234'];
        //Send post request
        $response = $this->json('POST',route('api.signup'),$userArray);
        //Assert it was successful
        $response->assertStatus(200);
        //Assert we received a token
        $this->assertArrayHasKey('token',$response->json());
    }

    /** @dataProvider loginDataSet */
    public function test_that_a_user_can_login_with_username_or_email($userData)
    {
        //Create user
        $user = factory('App\User')->create([
            'username'=>'testUsername',
            'email'=>'sample@email.com',
            'password'=>bcrypt('secret1234')
        ]);
        //attempt login
        $response = $this->json(
            'POST',route('api.login'),
            ['email' => $userData,
            'password' => 'secret1234']
        );
        //Assert it was successful and a token was received
        $response->assertStatus(200);
        $this->assertArrayHasKey('token',$response->json());
    }

    //Validation

    public function test_that_login_required_username_and_password()
    {   
        $this->withExceptionHandling();

        //Create user
        $user = factory('App\User')->create();
        //attempt login
        $response = $this->json(
            'POST',route('api.login'),
            ['email' => null,
            'password' => null]
        );

        $response->assertStatus(422);

        $response->assertJsonStructure(['email','password']);
    }

    /** @dataProvider nameDataSet */
    public function test_that_a_user_required_valid_first_and_last_name($nameData)
    {
        $response = $this->createUserAttempt($nameData);

        $response->assertStatus(422);

        $response->assertJsonStructure(['first_name','last_name']);
    }

    /** @dataProvider dobDataSet */
    public function test_that_a_user_required_valid_date_of_birth($dobData)
    {
        $response = $this->createUserAttempt($dobData);

        $response->assertStatus(422);

        $response->assertJsonStructure(['date_of_birth']);
    }

    /** @dataProvider emailDataSet */
    public function test_that_a_user_required_valid_email($emailData)
    {
        $response = $this->createUserAttempt($emailData);

        $response->assertStatus(422);

        $response->assertJsonStructure(['email']);
    }

    public function test_that_a_user_required_a_unique_email()
    {   
        $user = factory('App\User')->create();

        $response = $this->createUserAttempt(['email'=>$user->email]);

        $response->assertStatus(422);

        $response->assertJsonStructure(['email']);
    }

    /** @dataProvider usernameDataSet */
    public function test_that_a_user_required_a_valid_username($usernameData)
    {   
        $response = $this->createUserAttempt($usernameData);

        $response->assertStatus(422);
        
        $response->assertJsonStructure(['username']);
    }

    /** @dataProvider passwordDataSet */
    public function test_that_a_user_required_a_valid_password($passwordData)
    {   
        $this->withExceptionHandling();

        //User's data
        $user = factory('App\User')->make(
            ['password_confirmation'=>$passwordData]
        );

        $userArray = $user->toArray()+['password'=>$passwordData];

        $response = $this->json('POST',route('api.signup'),$userArray);

        $response->assertStatus(422);
        
        $response->assertJsonStructure(['password']);
    }

    public function test_that_a_user_required_a_password_confirmation()
    {   
        $this->withExceptionHandling();

        //User's data
        $user = factory('App\User')->make();

        $userArray = $user->toArray()+[
            'password'=>'secret1234',
            'password_confirmation'=>'difference'
        ];

        $response = $this->json('POST',route('api.signup'),$userArray);

        $response->assertStatus(422);
        
        $response->assertJsonStructure(['password']);
    }

    
    protected function createUserAttempt($overrides = [])
    {
        $this->withExceptionHandling();

        $user = factory('App\User')->make($overrides);

        $userArray = $user->toArray() + [
            'password'=>'secret1234',
            'password_confirmation'=>'secret1234',
        ];

        return $this->json(
            'POST',
            route('api.signup'),
            $userArray
        );
    }

    //DataSet for validation test

    public function loginDataSet()
    {   
        return [
           'usernameTest'=> ['testUsername'],
           'emailTest'=>['sample@email.com']
        ];
    }

    public function nameDataSet()
    {
        return [
           'nullTest'=> [['first_name'=>null,'last_name'=>null]],
           'numericTest'=> [['first_name'=>999,'last_name'=>999]]
        ];
    }

    public function dobDataSet()
    {
        return [
           'nullTest'=> [['date_of_birth'=>null]],
           //'numericTest'=> [['date_of_birth'=>999]],
          // 'stringTest'=> [['date_of_birth'=>'string']]
        ];
    }

    public function emailDataSet()
    {   
        $email = str_random(255).'@email.com';
        return [
           'nullTest'=> [['email'=>null]],
           'numericTest'=> [['email'=>999]],
           'stringTest'=> [['email'=>'string']],
           'maximumTest'=>[['email'=>$email]]
        ];
    }

    public function usernameDataSet()
    {   
        $username = str_random(256);
        return [
           'nullTest'=> [['username'=>null]],
           'maximumTest'=>[['username'=>$username]]
        ];
    }

    public function passwordDataSet()
    {   
        return [
           'nullTest'=> [null],
           'minimumTest'=>['minimum']
        ];
    }
}
