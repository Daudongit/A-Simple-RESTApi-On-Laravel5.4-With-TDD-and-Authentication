<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\UserRequest;
use App\User;

class AuthenticateController extends Controller
{   
    /**
     * Attempt login
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(Request $request)
    {
        //Validate fields
        $this->validate($request,['email' => 'required','password'=> 'required']);

        //Attempt validation
        $credentials[$this->username()] = $request->email;

        $credentials['password'] = $request->password;

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json(compact('token'));
    }

    /**
     * Register user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(UserRequest $request)
    {
        //Create user, generate token and return
        $user =  User::create([
            'first_name'=>$request->input('first_name'),
            'last_name'=>$request->input('last_name'),
            'date_of_birth'=>$request->input('date_of_birth'),
            'email' => $request->input('email'),
            'username' => $request->input('username'),
            'password' => Hash::make($request->input('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('token'),200);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {   
        $username = 'username';

        if (filter_var(request('email'), FILTER_VALIDATE_EMAIL)){
            $username = 'email';
        }

        return $username;
    }
}