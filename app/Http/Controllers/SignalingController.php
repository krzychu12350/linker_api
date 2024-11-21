<?php

namespace App\Http\Controllers;

use App\Events\SignalReceived;
use Illuminate\Http\Request;

class SignalingController extends Controller
{
    public function signal(Request $request)
    {
        // Validate and broadcast the signaling data
        broadcast(new SignalReceived($request->all()));
    }
}
