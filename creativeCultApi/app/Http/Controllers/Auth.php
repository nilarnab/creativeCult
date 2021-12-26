<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Session;
use Illuminate\Support\Facades\Validator;

class Auth extends Controller
{
    //

    public function register(Request $request)
    {

        /* untested code, flying blind */
        $data = $request->all();

        $user = new Client;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $data['password'];


        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:client_user|max:255',
            'email' => 'required',
            'password' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            return json_encode(
                [
                    'id' => 0,
                    'message' => $validator->messages(),
                ]
            );
        } else {
            
        }



        $user->save();

        return json_encode([
            'id' => 1,
            'message' => 'success'
        ]);
        
    }

    public function login(Request $request)
    {
        /*
            Makes login
            if login is successful:
                1. make an entry in sesssion
                2. return the session id with success

            else:
                1. return failure with reason
        */

        // echo "Login engine working \n";
        
        // $data => [email, password]
        $data = $request->all();
        // print_r($data);

        // get current session info
        $session_info = json_decode($this->get_current_session($data['email']), TRUE);
        // print_r($session_info);

        // return $session_info;
                

        if ($session_info['id'] == 1)
        {
            $user = $session_info['user'];

            $response = $this->create_session($user);
            
            return json_encode(
                [
                    'id' => 1,
                    'verdict' => 'success',
                    'message' => 'new login complete',
                    'user' => $user,
                    'session_info' => $session_info
                ]
                );
        }

        else
        {
            if ($session_info['verdict'] = 'logged in')
            {
                
                return json_encode(
                    [
                        'id' => 1,
                        'verdict' => 'success',
                        'message' => 'already logged in',
                        'user' => $user,
                        'session_info' => $session_info
                    ]
                    );

            }

            else
            {
                return json_encode(
                    [
                        'id' => 0,
                        'verdict' => $session_info['verdict'],
                        'message' => $session_info['message']
                    ]
                    );
            }

        }
        
        
    }


    private function create_session($user)
    {

        $session_code = uniqid($user['name']);

        $session_insert = new Session;

        $session_insert->user_id = $user['client_id'];
        $session_insert->session_code = $session_code;
        $session_insert->is_alive = 1;
        $session_insert->time_to_live = "2021-12-25 06:19:23";

        $session_insert->save();

        return json_encode([
            'id'=> 1,
            'verdict' => 'success',
            'message' => 'session created successfully',
            'session_code' => $session_code
        ]);
        
    }

    private function get_current_session($email)
    {

        /*
            returns
                {
                    session: 0 or 1, 0 is absent, 1 present
                    creds :
                        {
                            name: 
                            email: 
                        }
                }
        */


        $user = Client::where('email', $email)
                ->get();

        if(sizeof($user) > 1)
        {
            return json_encode(
                [
                    'id' => 0,
                    'verdict' => 'server error',
                    'message' => 'more than one users under single email'
                ]
                );
        }

        if (sizeof($user) == 0)
        {
            return json_encode(
                [
                    'id' => 0,
                    'verdict' => 'no user',
                    'message' => 'user does not exist'
                ]
            );
        }

        $user = $user[0];

        $session = Session::where('user_id', $user['client_id'])
                    ->where('is_alive', 1)
                    ->get();

        if (sizeof($session) > 0)
        {
            return json_encode(
                [
                    'id' => 0,
                    'verdict' => 'logged in',
                    'message' => 'user already logged in',
                    'data' => $user
                ]
            );
        }
        else
        {
            return json_encode(
                [
                    'id' => 1,
                    'verdict' => 'no session',
                    'message' => 'no valid session exists',
                    'user' => $user
                ]
            );
        }

        
    }


    // private function is_clear_to
}