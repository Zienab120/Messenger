<?php

namespace App\Http\Controllers;
use App\Events\SocketMessage;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\User;
use App\Models\Group;
use App\Models\Conversation;
use Str;
use Storage;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function byUser(User $user)
    {
        $messages = Message::where('sender_id',auth()->id())
        ->where('receiver_id',$user->id)
        ->orWhere('sender_id',$user->id)
        ->where('sender_id',auth()->id())
        ->latest()
        ->paginate(10);
        
        return inertia('Home',[
            'selectedConverastion' => $user->toConversationArray(),
            'messages' => MessageResource::collection($messages)
        ]);
    }

    public function byGroup(Group $group)
    {
        $messages = Message::where('group_id',$group->id)
        ->latest()->paginate(10);

        return inertia('Home',[
            'selectedConverastion' => $group->toConversationArray(),
            'messages' => MessageResource::collection($messages)
        ]);
    }

    public function loadOlder(Message $message)
    {
        if($message->group_id)
        {
            $messages = Message::where('created_at','<',$message->created_at)
            ->where('group_id',$message->group_id)
            ->latest()->paginate(10);
        }
        else
        {
            $messages = Message::where('created_at','<',$message->created_at)
            ->where(function($query) use ($message)
            {
                $query->where('sender_id',$message->sender_id)
                ->where('receiver_id',$message->receiver_id)
                ->orWhere('receiver_id',$message->sender_id)
                ->where('sender_id',$message->receiver_id);
            })->latest()->paginate(10);
        }
        return MessageResource::collection($messages);
    }

    public function store(StoreMessageRequest $request)
    {
        $data = $request->validated();
        $date['sender_id'] = auth()->id();
        $receiverId = $data['receiver_id'] ?? null;
        $groupId = $data['group_id'] ?? null;
        $files = $data['attachments'] ?? [];
        $message = Message::create($data);

        $attachments = [];
        if($files)
        {
            foreach($files as $file)
            {
                $directory = 'attachments/' . Str::random(32);
                Storage::makeDirectory($directory);

                $model = [
                    'message_id' => $message->id,
                    'name' => $file->getClientOriginalName(),
                    'mime' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'path' => $file->store($directory,'public'),
                ];

                $attachment = MessageAttachment::create($model);
                $attachments[] = $attachment;
            }
            $message->attachments = $attachments;
        }

        if($receiverId)
        {
            Conversation::updateConversationWithMessage($receiverId,auth()->id(),$message);
        }

        if($groupId)
        {
            Group::updateGroupWithMessage($groupId,$message);
        }
        SocketMessage::dispatch($message);
        return new MessageResource($message);
    }

    public function destroy(Message $message): void
    {
        if($message->id !== auth()->id())
        {
            response()->json(['message' => 'forbidden'],403);
        }
        $message->delete();
        response('',204);
    }

}
