<?php

namespace App\Http\Controllers;

use App\Events\Video\AnswerCall;
use App\Events\Video\SendHandShake;
use App\Events\Video\StartCall;
use App\Models\User;
use Illuminate\Http\Request;

class CallController extends Controller
{
    public function startCall(Request $request)
    {
        $user = User::findOrFail($request->id);
        event(new StartCall($user, auth()->user(), $request->data));

        return response([
            "status" => true,
            "message" => "calling.."
        ]);
    }

    public function AnswerCall(Request $request)
    {
        $user = User::findOrFail($request->id);
        event(new AnswerCall($user, $request->data));

        return response([
            "status" => true,
            "message" => "calling.."
        ]);
    }

    public function handshake(Request $request)
    {
        //dd($request->senderId, $request->reciverId, $request->data);
        event(new SendHandShake($request->senderId, $request->reciverId, $request->data));

        return response([
            "status" => true,
            "message" => "handshake send.."
        ]);
    }
}
