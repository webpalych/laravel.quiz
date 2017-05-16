<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\Controller;
use Validator;

use Hash;
use App\User;


class AuthenticateController extends Controller
{

    public function authenticate(Request $request)
    {
        $credentials = $request->only('name', 'password');

        try {
            // verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // if no errors are encountered we can return a JWT
        return response()->json(compact('token'));
    }



    public function registrationUser (Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required',
            'email' => 'required | email | unique:users',
        ]);

        if ($validator->fails()) {
            $errors = $validator->failed();
            if( (!array_key_exists('name',$errors) && array_key_exists('email',$errors)) && array_key_exists('Unique', $errors['email']) ) {

                return $this->callAction('authenticateUser', ['params' => $request->all()]);

            } else {

                return response()->json($validator->errors()->all(), 401);

            }
        }

        $user = $this->createPlayer($data);

        return $this->callAction('authenticateUser', ['params' => [
            'name' => $user->name,
            'email' => $user->email
        ]]);
    }


    public function authenticateUser($data)
    {
        $credentials = [
            'name' => $data['name'],
            'email' =>$data['email'],
            'password' => $data['email'],
        ];

        try {
            // verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // if no errors are encountered we can return a JWT
        return response()->json(compact('token'));
    }

    protected function createPlayer(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['email']),
        ]);
        $user->roles()->attach(2);
        return $user;
    }
}
