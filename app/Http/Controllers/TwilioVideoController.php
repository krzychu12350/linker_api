<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;

class TwilioVideoController extends Controller
{
    public function generateToken(Request $request)
    {
        $validated = $request->validate([
            'identity' => 'required|string',
            'room_name' => 'required|string',
        ]);

        $identity = $validated['identity'];
        $roomName = $validated['room_name'];

        /*
         TWILIO_SID=ACc419fb868e32834253f8ab7be993014a
        TWILIO_AUTH_TOKEN=4bf3cc3f3a16b6c8c3a00e500e134ac6
        TWILIO_API_KEY=SKbaa4f4e826e490326bb0527aea730250
        TWILIO_API_SECRET=5sdykwX44LnuEBbr5y1J3828WbZ06De6
        TWILIO_SERVICE_SID=your_video_service_sid

         */
        $twilioSid = 'ACc419fb868e32834253f8ab7be993014a';
        $twilioApiKey = 'SK725bf536003da5f326a5a07c7896caae';
        $twilioApiSecret = 'dVStSUggNgFbJQKXuvc7EwOJUFXsFpJD';


        $token = new AccessToken($twilioSid, $twilioApiKey, $twilioApiSecret, 3600, $identity);

        $videoGrant = new VideoGrant();
        $videoGrant->setRoom($roomName);

        $token->addGrant($videoGrant);

        return response()->json([
            'token' => $token->toJWT(),
        ]);
    }
}
