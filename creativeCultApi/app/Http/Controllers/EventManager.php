<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\EventEntry;
use App\Models\Session;
use Illuminate\Http\Request;
use App\Models\EventDetail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

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


        /* Business validation */
        if ($request['business'] != 'MAKE_NEW_EVENT')
        {
            return json_encode(
                [
                    'id' => 0,
                    'message' => 'Wrong business'
                ]
            );
        }

        // Finding if all keys are present
        if (!$request->has('token', 'business', 'starting_datetime', 'ending_datetime', 'event_name', 'description'))
        {
            return json_encode([
                'id' => 0,
                'message' => 'Does not have required fields'
            ]);
        }

        // validation of keys
        $validator = Validator::make($request->all(), [
            'event_name' => 'required|unique:event_details|max:255',
            'starting_datetime' => 'required',
            'ending_datetime' => 'required',
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

        /* Body of the api */

        $event_insertable = new EventDetail();

        $event_insertable->event_name = $request->event_name;
        $event_insertable->description = $request->description;
        $event_insertable->event_name = $request->event_name;
        $event_insertable->starting_time = $request->starting_datetime;
        $event_insertable->ending_time = $request->ending_datetime;

        $event_insertable->save();

        /* Return value */
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
         * 1. entry_id
         * 2.
         * 3.
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


        if (!$request->has('token', 'business', 'entry_id', 'points'))
        {
            return json_encode([
                'id' => 0,
                'message' => 'Does not have required fields in event manager/give marks'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'entry_id' => 'required',
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

        EventEntry::where(
            [
                'id' => $request['entry_id'],
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


    function show_events(Request $request)
    {
        // security not applied
        // all events are public
        $entries = EventDetail::with('has_entry')->orderBy('starting_time', 'DESC') ->get();
        return $entries;
    }

    public function show_event_details(Request $request)
    {
        return EventEntry::with('has_participant', 'has_event')->where('event_id', $request['event_id'])->get();

    }

    public function entry_details(Request $request)
    {
        // entry id
//        return $request->all();
        return EventEntry::with('has_participant', 'has_event')->where('id', $request['entry_id'])->get();
    }

    public function insert_entry(Request $request)
    {
        /*
         * Insert entry
         *
         */

//        return $request->all();

        $old_entry = EventEntry::where('event_id', $request['event_id'])->where('user_id', $request['user_id'])->get();

        if (sizeof($old_entry) > 0)
        {
            return json_encode(
                [
                    'id' => 0,
                    'message' => 'Entry already exists'
                ]
            );
        }

        $new_entry = new EventEntry();

        $new_entry->event_id = $request['event_id'];
        $new_entry->user_id = $request['user_id'];
        $new_entry->pic = $request['pic'];

        $new_entry->save();

        return json_encode(
            [
                'id' => 1,
                'message' => 'Success'
            ]
        );


    }

    public function show_attended_events(Request $request)
    {
        /*
         * Shows all the events attended by the user
         *
         * $request -> user_id
         *
         */


        $user_id = $request['user_id'];
        $all_events = EventDetail::all();

        $current_time = Carbon::now();

//        return $all_events;

        foreach ($all_events as $event)
        {
            if ($this->has_attended($event['id'], $user_id))
            {
                $event['attended'] = true;
            }
            else
            {
                $event['attended'] = false;
            }

            if($current_time > $event['ending_time'])
            {
                $event['expired'] = true;
            }
            else
            {
                $event['expired'] = false;
            }

            $rows = EventEntry::where('user_id', $request['user_id'])->where('event_id', $event['id'])->get();

//            return $rows;

            if (sizeof($rows) == 0)
            {
                $event['marks'] = NULL;
            }
            else
            {
                $event['marks'] = $rows[0]['points'];
            }

        }

        return $all_events;


    }

    private function has_attended($event_id, $user_id)
    {
        $entires = EventEntry::where('event_id', $event_id)->where('user_id', $user_id)->get();

        if (sizeof($entires) > 0)
        {
            return true;
        }
        return false;

    }




}
