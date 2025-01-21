<?php

namespace App\Http\Resources;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //dd($this->messages()->get()->last());
//        $messagesWithAuthors = $this->messages()->with(['files', 'sender'])->get()->map(function ($message) {
//
//            $messageArray = [
//                'id' => $message->id,
//                'body' => $message->body,
//                'type' => $message->files->isEmpty() ? MessageType::TEXT : MessageType::FILE,
//                'read_at' => $message->read_at,
//                'author' => [
//                    'id' => $message->sender->id,
//                    'first_name' => $message->sender->first_name,
//                    'photo' => $message->sender->photos->first()->url
//                ],
//            ];
//
//            // Conditionally add 'files' only if it's not empty
//            if (!$message->files->isEmpty()) {
//                $messageArray['files'] = FileResource::collection($message->files);
//            }
//
//            return $messageArray;
//        });
//        dd($this->users);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'last_message' => new MessageResource($this->messages->last()),
            'users' => UserResource::collection($this->users),
        ];
    }
}
