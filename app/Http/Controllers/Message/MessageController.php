<?php

namespace App\Http\Controllers\Message;

use App\Events\MessageSent;
use App\Helpers\FileTypeHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Models\File;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(int $userId, int $conversationId)
    {
        // dd($userId, $conversationId);
        $conversation = Conversation::findOrFail($conversationId);
        $messages = $conversation->messages()->get();

        return MessageResource::collection($messages);
    }

    // Store a new message for a conversation
    public function store(StoreMessageRequest $request, int $userId, int $conversationId)
    {
        // dd($userId, $conversationId);
        // Validate the incoming request
//        $request->validate([
//            'body' => 'required|string', // Make sure body is a string and required
//        ]);
        $validatedData = $request->validated();

        // Find the conversation or fail if it doesn't exist
        $conversation = Conversation::findOrFail($conversationId);

        // Prepare the message data
        $messageData = [
            'conversation_id' => $conversation->id,
            'sender_id' => $validatedData['sender_id'],
            'receiver_id' => $validatedData['receiver_id'],
        ];

        // If there is a body, add it to the message data
        if (isset($validatedData['body'])) {
            $messageData['body'] = $validatedData['body'];
        }

        // Create a new message and associate it with the conversation and authenticated user
        $message = Message::create($messageData);

        // Handle the file if it exists
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileType = FileTypeHelper::getFileType($file); // Use the helper to determine the file type

            // Store the file (assuming you use Cloudinary, adjust accordingly)
            //$filePath = $file->store('messages', 'public'); // Or upload to Cloudinary if needed

            $uploadedFile = cloudinary()->upload($file->getRealPath(), [
                'resource_type' => 'auto', // Cloudinary treats audio files as video resources
                'folder' => 'messages',
            ]);

            // Retrieve the file information (you can also save other details like public_id or URL)
            $fileRecord = File::create([
//                'url' => $uploadedFile->getPublicId(),
                'url' => $uploadedFile->getSecurePath(),
                'type' => $fileType, // Store the file type as a string from the enum
            ]);

            // Attach the file to the created message
            $message->files()->attach($fileRecord);

          //  dd( $message->files()->get());


            // dd('dwdwdw', $data->getPublicId());

            // Add the file details to the message
           /// $messageData['file_path'] = $filePath;
           // $messageData['file_type'] = $fileType->value; // Save the file type as a string from the enum
        }


        // Trigger the event to broadcast the message
        event(new MessageSent($message));

        broadcast(new MessageSent($message));

        //   dd( $message);
        // Return the newly created message as a resource
        return new MessageResource($message);
    }

}
