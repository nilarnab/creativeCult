<?php

namespace App\Http\Middleware;

use App\Models\Client;
use App\Models\PageAction;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use App\Models\Session;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function handle($request, Closure $next, ...$guards)
    {
        /*
         * Accepting parameter
         * 1. token
         * 2. business
         *
         */

        $session_situation = Session::where('session_code', $request['token'])->get();

        $message = NULL;

        if (count($session_situation)) {
            // There exists a session
            // Checking if the session is valid

            // Taking the first record
            $session_situation = $session_situation[0];


            // finding it alive
            if ($session_situation['is_alive']) {
                // alive
                // Finding if time not expired


                // this code is left empty for now

                // Check if the business corresponds to the access level
                $user = Client::where('client_id', $session_situation['user_id'])->get()[0];
                $access_required = PageAction::with('page_action')->where('page_name', $request['business'])->get()[0];

                if ($access_required['page_action']['min_access_required'] <= $user['access'])
                {
                    return $next($request);
                }

                else
                {
                    $message = "Insufficient access";
                }


            }
            else
            {
                $message = "Session is not alive";
            }


        }

        else
        {
            $message = "Session not found";
        }

        return redirect('/reject?msg='.$message);
    }
}
