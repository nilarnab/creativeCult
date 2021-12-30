<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\EventEntry;
use App\Models\Session;
use Illuminate\Http\Request;
use App\Models\EventDetail;
use Illuminate\Support\Facades\Validator;

class EventManager extends Controller
{
    //
    public function make_event(Request $request)
    {
        /*
         *  Accepting parameter
         *      1. token
         *      2. business
         *      3. starting_datetime
         *      4. ending_datetime
         *      5. event_name
         *      6. description
         *
         *  has to business to MAKE_NEW_EVENT
         */

        if ($request['business'] != 'MAKE_NEW_EVENT')
        {
            return json_encode(
                [
                    'id' => 0,
                    'message' => 'Wrong business'
                ]
            );
        }

        if (!$request->has('token', 'business', 'starting_datetime', 'ending_datetime', 'event_name', 'description'))
        {
            return json_encode([
                'id' => 0,
                'message' => 'Does not have required fields'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'event_name' => 'required|unique:event_details|max:255',
            'starting_datetime' => 'required',
            'ending_datetime' => 'required',
        ]);

        if ($validator->fails()) {
            return json_encode(
                [
                    'id' => 0,
                    'message' => $validator->messages(),
                ]
            );
        }

        $event_insertable = new EventDetail();

        $event_insertable->event_name = $request->event_name;
        $event_insertable->description = $request->description;
        $event_insertable->event_name = $request->event_name;
        $event_insertable->starting_time = $request->starting_datetime;
        $event_insertable->ending_time = $request->ending_datetime;

        $event_insertable->save();

        return json_encode(
            [
                'id' => 1,
                'message' => 'new event created successfully'
            ]
        );
    }

    public function give_mark(Request $request)
    {
        /*
         * Updates the mark column of events
         *
         * expecting
         * 1. user_id
         * 2. event_id
         * 3. pic (LONGBLOB)
         * 4. points
         *
         */

        if ($request['business'] != 'GIVE_MARK')
        {
            return json_encode(
                [
                    'id' => 0,
                    'message' => 'Wrong business'
                ]
            );
        }


        if (!$request->has('token', 'business', 'user_id', 'event_id', 'pic', 'points'))
        {
            return json_encode([
                'id' => 0,
                'message' => 'Does not have required fields in event manager/give marks'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'event_id' => 'required',
            'pic' => 'required',
            'points' => 'required',
        ]);

        if ($validator->fails()) {
            return json_encode(
                [
                    'id' => 0,
                    'message' => $validator->messages(),
                ]
            );
        }


        echo "Auth complete \n";


        EventEntry::where(
            [
                'user_id' => $request['user_id'],
                'event_id' => $request['event_id']
            ]
        ) -> update(
            [
                'points' => $request['points']
            ]
        );

        return json_encode(
            [
                'id' => 1,
                'message' => 'Points updated successfully'
            ]
        );





    }
}
