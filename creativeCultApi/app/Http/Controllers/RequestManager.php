<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\EventDetail;
use App\Models\Requests;
use App\Models\Session;
use App\Models\StageKeys;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RequestManager extends Controller
{
    //
    public function add_request(Request $request)
    {


        /* Business validation */
        if ($request['business'] != 'MAKE_NEW_REQUEST')
        {
            return json_encode(
                [
                    'id' => 0,
                    'message' => 'Wrong business'
                ]
            );
        }

//        return $request;


        // Finding if all keys are present
        if (!$request->has('token', 'business', 'title', 'description', 'is_delivery', 'open_for', 'expected_deadline'))
        {
            return json_encode([
                'id' => 0,
                'message' => 'Does not have required fields'
            ]);
        }

        if($request['is_delivery'] == 1)
        {
            if(!$request->has('postal_address') or $request['postal_address'] == NULL)
            {
                return json_encode(
                    [
                        'id' => 0,
                        'message' => 'Delivery is set to true without any postal address'
                    ]
                );
            }
        }

        if(!$request->has('amount_range_start') or $request['amount_range_start'] == NULL)
        {
            if(!$request['amount_range_end'] == NULL)
            {
                return json_encode(
                    [
                        'id' => 0,
                        'message' => 'Ending of range is present but not starting of range'
                    ]
                );
            }
        }

        if(!$request->has('amount_range_end') or $request['amount_range_end'] == NULL)
        {
            if(!$request['amount_range_start'] == NULL)
            {
                return json_encode(
                    [
                        'id' => 0,
                        'message' => 'Starting of range is present but not ending of range'
                    ]
                );
            }
        }



        $request['open_for'] = (int) $request['open_for'];
        $request['is_delivery'] = (int) $request['is_delivery'];



        // validation of keys
        $validator = Validator::make($request->all(), [
            'open_for' => 'required|numeric|min:1',
            'is_delivery' => 'required|numeric|min:0|max:1',
        ]);

        // take action if validaiton fails
        if ($validator->fails()) {
            return json_encode(
                [
                    'id' => 0,
                    'message' => $validator->messages(),
                ]
            );
        }

        // managing nullable values
        if (!$request->has('expected_deadline'))
        {
            $request['expected_deadline'] = NULL;
        }

        if (!$request->has('amount_range_start'))
        {
            $request['amount_range_start'] = NULL;
        }

        if (!$request->has('amount_range_end'))
        {
            $request['amount_range_end'] = NULL;
        }

        if (!$request->has('postal_address'))
        {
            $request['postal_address'] = NULL;
        }

        /* Body of the api */

        $new_row = new Requests();
        $new_row->description = $request['description'];
        $new_row->title = $request['title'];
        $new_row->is_delivery = $request['is_delivery'];
        $new_row->postal_address = $request['postal_address'];
        $new_row->expected_deadline = $request['expected_deadline'];
        $new_row->amount_range_start = $request['amount_range_start'];
        $new_row->amount_range_end = $request['amount_range_end'];
        $new_row->requester_id = $this->get_current_session($request['token'])['user_id'];

        $new_row->save();

        return json_encode(
            [
                'id' => 1,
                'message' => 'New request created'
            ]
        );


//        /* Return value */
//        return json_encode(
//            [
//                'id' => 1,
//                'message' => 'new event created successfully'
//            ]
//        );
    }


    public function update_request_status(Request $request)
    {


        /* Business validation */
        if ($request['business'] != 'UPDATE_REQUEST_STATUS')
        {
            return json_encode(
                [
                    'id' => 0,
                    'message' => 'Wrong business'
                ]
            );
        }

        // Finding if all keys are present
        if (!$request->has('token', 'business', 'request_id', 'new_status_id'))
        {
            return json_encode([
                'id' => 0,
                'message' => 'Does not have required fields'
            ]);
        }

        $request['request_id'] = (int) $request['request_id'];
        $request['new_status_id'] = (int) $request['new_status_id'];
        $request['client_id'] = (int) $request['client_id'];

//        return StageKeys::all();

        // finding if the stage is actually right
        if(!array_key_exists($request['new_status_id'], [
            1 => true,
            2 => true,
            3 => true,
            4 => true,
        ]))
        {
            return json_encode(
                [
                    'id' => 0,
                    'message' => 'Unknown new status id'
                ]
            );
        }

        // finding if the correct user is doing the work

        $request_acceptor = Requests::where('id', $request['request_id'])->get()[0]['acceptor_id'];

//        return $request_acceptor;

        $session_now = $this->get_current_session($request['token']);

//        $session_now = json_decode($session_now, true);
//        return $session_now['user_id'];

        $user_id = $session_now['user_id'];




        if($request_acceptor == NULL)
        {
            return json_encode(
                [
                    'id' => 0,
                    'message' => 'No request acceptor'
                ]
            );
        }


        if ($request_acceptor != $user_id)
        {
            return json_encode(
                [
                    'id' => 0,
                    'message' => 'Somebody else had accepted this request'
                ]
            );
        }

        /* Body of the api */
        Requests::where('id', $request['request_id'])->update(
            [
                'stage' => $request['new_status_id']
            ]
        );

        return json_encode(
            [
                'id' => 1,
                'message' => 'Stage of the request updated'
            ]
        );

    }


    public function add_pic(Request $request)
    {


        /* Business validation */
        if ($request['business'] != 'ADD_PIC_TO_REQUEST')
        {
            return json_encode(
                [
                    'id' => 0,
                    'message' => 'Wrong business'
                ]
            );
        }

        // Finding if all keys are present
        if (!$request->has('token', 'business', 'request_id', 'pic'))
        {
            return json_encode([
                'id' => 0,
                'message' => 'Does not have required fields'
            ]);
        }



        // finding if the correct user is doing the work
        $request_acceptor = Requests::where('id', $request['request_id'])->get()[0]['acceptor_id'];

        $session_now = $this->get_current_session($request['token']);

//        $session_now = json_decode($session_now, true);
//        return $session_now['user_id'];

        $user_id = $session_now['user_id'];


        if($request_acceptor == NULL)
        {
            return json_encode(
                [
                    'id' => 0,
                    'message' => 'No request acceptor'
                ]
            );
        }


        if ($request_acceptor != $user_id)
        {
            return json_encode(
                [
                    'id' => 0,
                    'message' => 'Somebody else had accepted this request'
                ]
            );
        }


//        return $request->all();



        /* Body of the api */
        Requests::where('id', $request['request_id'])->update(
            [
                'pic' => $request['pic']
            ]
        );



        return json_encode(
            [
                'id' => 1,
                'message' => 'Pic updated'
            ]
        );
    }


    public function accept_request(Request $request)
    {

        /* Business validation */
        if ($request['business'] != 'ACCEPT_REQUEST')
        {
            return json_encode(
                [
                    'id' => 0,
                    'message' => 'Wrong business'
                ]
            );
        }

        // Finding if all keys are present
        if (!$request->has('token', 'business', 'request_id'))
        {
            return json_encode([
                'id' => 0,
                'message' => 'Does not have required fields'
            ]);
        }

        // finding if the correct user is doing the work
        $request_acceptor = Requests::where('id', $request['request_id'])->get()[0]['acceptor_id'];


        $user_id = $this->get_current_session($request['token'])['user_id'];


        if($request_acceptor != NULL) {
            return json_encode(
                [
                    'id' => 0,
                    'message' => 'Request already accepted'
                ]
            );
        }


        /* Body of the api */
        Requests::where('id', $request['request_id'])->update(
            [
                'acceptor_id' => $user_id
            ]
        );

        return json_encode(
            [
                'id' => 1,
                'message' => 'Accepted request'
            ]
        );

    }



    public function show_requests(Request $request)
    {

        $all_req = Requests::all();

//        return $all_req;

        /* Business validation */

        if ($request['business'] != 'SHOW_ALL_REQUESTS')
        {
            return json_encode(
                [
                    'id' => 0,
                    'message' => 'Wrong business'
                ]
            );
        }

        // Finding if all keys are present
        if (!$request->has('token', 'business'))
        {
            return json_encode([
                'id' => 0,
                'message' => 'Does not have required fields'
            ]);
        }

        if ($request->has('client_id'))
        {
            if ($request['client_id'] != NULL)
            {

                return json_encode(
                    [
                        'id' => 1,
                        'message' => 'Showing the information of client_id',
                        'data' => Requests::where('requester_id', $request['client_id'])->get()

                    ]
                );


            }
        }



        return json_encode(
            [
               'id' => 1,
               'message' => 'Showing information of all the clients',
                'data' => $all_req
            ]
        );





    }

    public function show_one_request(Request $request)
    {
        $request_info = Requests::with(['has_stage', 'has_open_for', 'has_requester', 'has_acceptor'])->where('id', $request['id'])->get();
        $all_stages = StageKeys::all();

        return json_encode(
            [
                'request_info' => $request_info,
                'all_stages' => $all_stages
            ]
        );
    }





    private function get_current_session($token)
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


        $session = Session::where('session_code', $token)
            ->where('is_alive', 1)
            ->get();

        if (sizeof($session) ==  1)
        {
            return
                [
                    'id' => 0,
                    'verdict' => 'logged in',
                    'message' => 'user already logged in',
                    'token' => $session[0]['session_code'],
                    'user_id' => $session[0]['user_id']
                ];
        }
        else
        {
            return json_encode(
                [
                    'id' => 1,
                    'verdict' => 'no session',
                    'message' => 'no valid session exists',
                ]
            );
        }


    }

    public function set_price(Request $request)
    {

        if(!$request->has(['request_id']))
        {
            return json_encode(
                [
                    'id' => 0,
                    'message'=> 'request id not given'
                ]
            );
        }

        if(!$request->has(['price']))
        {
            return json_encode(
                [
                    'id' => 0,
                    'message'=> 'price not given'
                ]
            );
        }

        Requests::where('id', $request['request_id'])->update(
            [
                'final_cost' => $request['price']
            ]
        );

        return json_encode([
            'id' => 1,
            'message' => 'Work Done'
        ]);

    }
}

