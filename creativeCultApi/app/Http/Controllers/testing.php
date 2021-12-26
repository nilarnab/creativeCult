<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class testing extends Controller
{
    //

    public function index(Request $request)
    {
        
        return Client::all();
    }

    public function handle_post(Request $request)
    {
        return $request->all();
        
    }   
}